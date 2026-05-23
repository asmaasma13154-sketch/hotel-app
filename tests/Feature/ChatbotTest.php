<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Services\ChatbotService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatbotTest extends TestCase
{
    use RefreshDatabase;

    public function test_chatbot_returns_valid_response(): void
    {
        // Mock du ChatbotService pour éviter l'appel API réel
        $this->mock(ChatbotService::class, function ($mock) {
            $mock->shouldReceive('ask')
                 ->once()
                 ->andReturn("Nous avons 3 hôtels disponibles à Paris.");
        });

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/chatbot', [
            'message' => 'Quels hôtels avez-vous ?'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['reply', 'success'])
                 ->assertJson(['success' => true]);
    }

    public function test_chatbot_requires_authentication(): void
    {
        $response = $this->postJson('/api/chatbot', ['message' => 'Bonjour']);
        $response->assertStatus(401);
    }

    public function test_chatbot_validates_message_required(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->postJson('/api/chatbot', ['message' => '']);
        $response->assertStatus(422);
    }
}