<?php

namespace Tests\Unit\Events;

use App\Events\ServiceOrderCompleted;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Notifications\ServiceOrderCompletedNotification;
use Tests\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ServiceOrderCompletedTest extends TestCase
{
    use DatabaseMigrations;

    public function test_event_contains_service_order()
    {
        $client = User::factory()->create(['role' => 'client']);
        $technician = User::factory()->create(['role' => 'technician']);

        $order = ServiceOrder::create([
            'title' => 'Ordem para Evento',
            'description' => 'Testando evento de conclusão',
            'status' => 'concluida',
            'client_id' => $client->id,
            'technician_id' => $technician->id,
        ]);

        $event = new ServiceOrderCompleted($order);

        $this->assertInstanceOf(ServiceOrder::class, $event->serviceOrder);
        $this->assertEquals($order->id, $event->serviceOrder->id);
    }

    public function test_event_dispatches_notification()
    {
        Notification::fake();
        
        $client = User::factory()->create(['role' => 'client']);
        $technician = User::factory()->create(['role' => 'technician']);

        $order = ServiceOrder::create([
            'title' => 'Ordem para Notificação',
            'description' => 'Testando notificação via evento',
            'status' => 'pendente',
            'client_id' => $client->id,
            'technician_id' => $technician->id,
        ]);

        $order->status = 'concluida';
        $order->save();
        
        event(new ServiceOrderCompleted($order->load('client', 'technician')));

        Notification::assertSentTo(
            $client,
            ServiceOrderCompletedNotification::class,
            function ($notification, $channels) use ($order) {
                return $notification->serviceOrder->id === $order->id;
            }
        );
    }
}
