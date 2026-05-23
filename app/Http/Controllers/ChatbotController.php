<?php
namespace App\Http\Controllers;

use App\Services\ChatbotService;
use App\Models\ChatHistory;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct(private ChatbotService $chatbotService) {}

    // Page chat (optionnel)
    public function index()
    {
        $history = ChatHistory::where('user_id', auth()->id())
            ->latest()->limit(20)->get()->reverse();
        return view('chatbot.index', compact('history'));
    }

    // Endpoint API POST /api/chatbot
    public function ask(Request $request)
    {
        $request->validate([
            'message' => 'required|string|min:2|max:1000',
        ]);

        $response = $this->chatbotService->ask(
            $request->message,
            auth()->id()
        );

        return response()->json([
            'reply'   => $response,
            'success' => true,
        ]);
    }

    // Effacer l'historique
    public function clearHistory()
    {
        ChatHistory::where('user_id', auth()->id())->delete();
        return back()->with('success', 'Historique effacé.');
    }
}