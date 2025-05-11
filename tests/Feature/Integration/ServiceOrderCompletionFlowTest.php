<?php

namespace Tests\Feature\Integration;

use App\Events\ServiceOrderCompleted;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Notifications\ServiceOrderCompletedNotification;
use Tests\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ServiceOrderCompletionFlowTest extends TestCase
{
    use DatabaseMigrations;
    
    public function test_order_completion_flow()
    {
        // Configurar fakes para eventos e notificações
        Event::fake([ServiceOrderCompleted::class]);
        Notification::fake();
        
        // Criar os usuários
        $technician = User::factory()->technician()->create();
        $client = User::factory()->client()->create();
        
        // Criar a ordem de serviço
        $order = ServiceOrder::factory()
            ->emAndamento()
            ->comUsuarios($client, $technician)
            ->create([
                'title' => 'Ordem para Fluxo Completo',
                'description' => 'Testando fluxo completo de conclusão'
            ]);
        
        // Autenticar como o técnico
        $this->actingAs($technician);
        
        // Fazer a requisição para concluir a ordem
        $response = $this->put("/ordens/{$order->id}", [
            'title' => 'Ordem para Fluxo Completo',
            'description' => 'Testando fluxo completo de conclusão',
            'status' => 'concluida'
        ]);
        
        // Verificar redirecionamento
        $response->assertRedirect('/ordens');
        
        // Verificar que a ordem foi atualizada no banco
        $this->assertDatabaseHas('service_orders', [
            'id' => $order->id,
            'status' => 'concluida'
        ]);
        
        // Verificar que o evento foi disparado
        Event::assertDispatched(ServiceOrderCompleted::class, function ($event) use ($order) {
            return $event->serviceOrder->id === $order->id;
        });
        
        // Processar o evento manualmente (já que estamos usando fake)
        $dispatchedEvents = Event::dispatched(ServiceOrderCompleted::class);
        $dispatchedEvent = $dispatchedEvents[0][0] ?? null;
        
        if ($dispatchedEvent) {
            (new \App\Listeners\NotifyClientOfCompletion())->handle($dispatchedEvent);
        }
        
        // Verificar que a notificação foi enviada
        Notification::assertSentTo(
            $client,
            ServiceOrderCompletedNotification::class,
            function ($notification, $channels) use ($order) {
                return $notification->serviceOrder->id === $order->id;
            }
        );
    }
}
