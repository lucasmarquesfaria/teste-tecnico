<?php

namespace Tests\Unit\Notifications;

use App\Models\ServiceOrder;
use App\Models\User;
use App\Notifications\ServiceOrderCompletedNotification;
use Tests\DatabaseMigrations;
use Tests\TestCase;

class ServiceOrderCompletedNotificationTest extends TestCase
{
    use DatabaseMigrations;

    public function test_notification_has_correct_content()
    {
        $client = User::factory()->create(['role' => 'client']);
        $technician = User::factory()->create(['role' => 'technician']);

        $order = ServiceOrder::create([
            'title' => 'Ordem para Notificação',
            'description' => 'Testando conteúdo da notificação',
            'status' => 'concluida',
            'client_id' => $client->id,
            'technician_id' => $technician->id,
        ]);

        $notification = new ServiceOrderCompletedNotification($order);
        
        $channels = $notification->via($client);
        $this->assertEquals(['mail'], $channels);

        $mail = $notification->toMail($client);
        $this->assertEquals("Ordem de Serviço #{$order->id} - Concluída", $mail->subject);
        $this->assertEquals('emails.service-order-completed', $mail->view);
        $this->assertArrayHasKey('serviceOrder', $mail->viewData);
        $this->assertEquals($order->id, $mail->viewData['serviceOrder']->id);
    }

    public function test_notification_is_queued()
    {
        $client = User::factory()->create(['role' => 'client']);
        $technician = User::factory()->create(['role' => 'technician']);

        $order = ServiceOrder::create([
            'title' => 'Ordem para Fila',
            'description' => 'Testando queue da notificação',
            'status' => 'concluida',
            'client_id' => $client->id,
            'technician_id' => $technician->id,
        ]);

        $notification = new ServiceOrderCompletedNotification($order);
        
        // Verifica se a notification implementa ShouldQueue
        $this->assertContains(
            'Illuminate\Contracts\Queue\ShouldQueue',
            class_implements(get_class($notification))
        );
    }
}
