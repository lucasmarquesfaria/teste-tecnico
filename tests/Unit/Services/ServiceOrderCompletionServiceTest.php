<?php

namespace Tests\Unit\Services;

use App\Events\ServiceOrderCompleted;
use App\Listeners\NotifyClientOfCompletion;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Notifications\ServiceOrderCompletedNotification;
use Tests\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ServiceOrderCompletionServiceTest extends TestCase
{
    use DatabaseMigrations;
    
    public function test_listener_sends_notification_when_order_completed()
    {
        Notification::fake();
        
        $client = User::factory()->client()->create();
        $technician = User::factory()->technician()->create();
        
        $order = ServiceOrder::factory()
            ->emAndamento()
            ->comUsuarios($client, $technician)
            ->create();
            
        // Mudamos o status para concluído
        $order->status = 'concluida';
        $order->save();
        
        // Simulamos o disparo do evento
        $event = new ServiceOrderCompleted($order->load('client', 'technician'));
        
        // Executamos o listener diretamente
        $listener = new NotifyClientOfCompletion();
        $listener->handle($event);
        
        // Verificamos se a notificação foi enviada ao cliente
        Notification::assertSentTo(
            $client,
            ServiceOrderCompletedNotification::class,
            function ($notification, $channels) use ($order) {
                return $notification->serviceOrder->id === $order->id;
            }
        );
    }
    
    public function test_notification_contains_correct_order_data()
    {
        $client = User::factory()->client()->create([
            'name' => 'Cliente Teste',
            'email' => 'cliente@teste.com'
        ]);
        
        $technician = User::factory()->technician()->create([
            'name' => 'Técnico Teste',
            'email' => 'tecnico@teste.com'
        ]);
        
        $order = ServiceOrder::factory()
            ->concluida()
            ->comUsuarios($client, $technician)
            ->create([
                'title' => 'Ordem de Teste',
                'description' => 'Descrição da ordem para teste'
            ]);
        
        $notification = new ServiceOrderCompletedNotification($order);
        
        $mailData = $notification->toMail($client);
        
        $this->assertEquals("Ordem de Serviço #{$order->id} - Concluída", $mailData->subject);
        $this->assertEquals('emails.service-order-completed', $mailData->view);
        
        $serviceOrder = $mailData->viewData['serviceOrder'];
        $this->assertEquals('Ordem de Teste', $serviceOrder->title);
        $this->assertEquals('concluida', $serviceOrder->status);
        $this->assertEquals('Cliente Teste', $serviceOrder->client->name);
        $this->assertEquals('Técnico Teste', $serviceOrder->technician->name);
    }
}
