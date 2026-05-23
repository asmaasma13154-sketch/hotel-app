<?php
namespace App\Services;

use App\Models\ChatHistory;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\Reservation;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 30]);
    }

    /**
     * Point d'entrée principal : traiter la question de l'utilisateur
     */
    public function ask(string $userMessage, int $userId): string
    {
        try {
            // 1. Récupérer les données métier pertinentes
            $contextData = $this->fetchRelevantData($userMessage, $userId);

            // 2. Récupérer l'historique de conversation (5 derniers)
            $history = $this->getConversationHistory($userId);

            // 3. Construire le prompt intelligent
            $prompt = $this->buildPrompt($userMessage, $contextData, $history);

            // 4. Appeler l'API Gemini
            $response = $this->callGeminiAPI($prompt);

            // 5. Sauvegarder en base
            $this->saveHistory($userId, $userMessage, $response);

            return $response;

        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            Log::error('Chatbot API timeout: ' . $e->getMessage());
            return "Je suis temporairement indisponible. Veuillez réessayer dans quelques instants.";
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            Log::error('Chatbot API error: ' . $e->getMessage());
            if ($e->getCode() === 429) {
                return "Le quota de l'API est dépassé. Veuillez patienter quelques minutes.";
            }
            return "Une erreur s'est produite. Veuillez contacter l'assistance.";
        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage());
            return "Je rencontre une difficulté. Pouvez-vous reformuler votre question ?";
        }
    }

    /**
     * Récupérer les données métier selon le contexte de la question
     */
    private function fetchRelevantData(string $message, int $userId): array
    {
        $data = [];
        $msg = strtolower($message);

        // Données sur les hôtels
        if (str_contains_any($msg, ['hôtel', 'hotel', 'établissement', 'hébergement'])) {
            $data['hotels'] = Hotel::withCount('rooms')
                ->with(['rooms' => fn($q) => $q->where('status', 'available')])
                ->get()
                ->map(fn($h) => [
                    'nom'        => $h->name,
                    'ville'      => $h->city,
                    'etoiles'    => $h->stars,
                    'chambres_disponibles' => $h->rooms->where('status', 'available')->count(),
                ])->toArray();
        }

        // Données sur les chambres
        if (str_contains_any($msg, ['chambre', 'room', 'disponible', 'prix', 'tarif', 'suite', 'double', 'simple'])) {
            $data['rooms'] = Room::with('hotel')
                ->where('status', 'available')
                ->get()
                ->map(fn($r) => [
                    'numéro'   => $r->number,
                    'type'     => $r->type,
                    'prix'     => $r->price_per_night . '€/nuit',
                    'capacité' => $r->capacity . ' personne(s)',
                    'hôtel'    => $r->hotel->name,
                    'ville'    => $r->hotel->city,
                ])->toArray();
        }

        // Réservations de l'utilisateur connecté
        if (str_contains_any($msg, ['réservation', 'reservation', 'booking', 'mes', 'mon', 'ma', 'commande'])) {
            $data['my_reservations'] = Reservation::with(['room.hotel'])
                ->where('user_id', $userId)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get()
                ->map(fn($r) => [
                    'id'         => $r->id,
                    'hôtel'      => $r->room->hotel->name,
                    'chambre'    => $r->room->number . ' (' . $r->room->type . ')',
                    'check_in'   => $r->check_in->format('d/m/Y'),
                    'check_out'  => $r->check_out->format('d/m/Y'),
                    'nuits'      => $r->nights,
                    'total'      => $r->total_price . '€',
                    'statut'     => $r->status,
                ])->toArray();
        }

        // Statistiques (pour admin)
        if (str_contains_any($msg, ['statistique', 'stat', 'chiffre', 'total', 'revenu', 'bilan', 'populaire'])) {
            $data['stats'] = [
                'total_hotels'       => Hotel::count(),
                'total_rooms'        => Room::count(),
                'available_rooms'    => Room::where('status', 'available')->count(),
                'total_reservations' => Reservation::count(),
                'this_month_revenue' => Reservation::thisMonth()->sum('total_price'),
                'pending_count'      => Reservation::where('status', 'pending')->count(),
                'most_booked_room'   => Room::withCount('reservations')
                    ->orderByDesc('reservations_count')->first()?->number,
            ];
        }

        return $data;
    }

    /**
     * Construire un prompt intelligent avec contexte + données + historique
     */
    private function buildPrompt(string $userMessage, array $contextData, array $history): string
    {
        $dataJson = json_encode($contextData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $historyText = empty($history) ? "Aucun historique" : implode("\n", array_map(
            fn($h) => "Client: {$h['message']}\nAssistant: {$h['response']}",
            $history
        ));

        return <<<PROMPT
Tu es l'assistant virtuel intelligent de notre système hôtelier.
Tu aides les clients et le personnel à trouver des informations sur les hôtels, chambres, et réservations.

RÈGLES IMPORTANTES:
- Réponds UNIQUEMENT en français
- Utilise les données fournies ci-dessous pour répondre précisément
- Ne jamais inventer des données (prix, dates, noms) non présentes ci-dessous
- Sois concis, chaleureux et professionnel
- Si une information est manquante dans les données, dis-le clairement
- Pour des actions (créer une réservation, annuler...) guide l'utilisateur vers les pages appropriées

DONNÉES ACTUELLES DU SYSTÈME:
{$dataJson}

HISTORIQUE DE CONVERSATION:
{$historyText}

QUESTION DU CLIENT:
{$userMessage}

RÉPONSE (en français):
PROMPT;
    }

    /**
     * Appeler l'API Google Gemini
     */
    private function callGeminiAPI(string $prompt): string
    {
        $apiKey = config('services.gemini.api_key');
        $apiUrl = config('services.gemini.api_url');

        $response = $this->client->post("{$apiUrl}?key={$apiKey}", [
            'json' => [
                'contents' => [
                    ['parts' => [['text' => $prompt]]]
                ],
                'generationConfig' => [
                    'temperature'     => 0.7,
                    'maxOutputTokens' => 512,
                ]
            ]
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data['candidates'][0]['content']['parts'][0]['text']
            ?? "Je n'ai pas pu générer une réponse. Veuillez réessayer.";
    }

    private function getConversationHistory(int $userId): array
    {
        return ChatHistory::where('user_id', $userId)
            ->latest()
            ->limit(5)
            ->get()
            ->reverse()
            ->map(fn($h) => ['message' => $h->message, 'response' => $h->response])
            ->toArray();
    }

    private function saveHistory(int $userId, string $message, string $response): void
    {
        ChatHistory::create([
            'user_id'  => $userId,
            'message'  => $message,
            'response' => $response,
        ]);
    }
}

// Helper manquant dans PHP natif
if (!function_exists('str_contains_any')) {
    function str_contains_any(string $haystack, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (str_contains($haystack, $needle)) return true;
        }
        return false;
    }
}