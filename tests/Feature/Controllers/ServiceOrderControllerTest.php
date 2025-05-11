<?php

namespace Tests\Feature\Controllers;

use App\Models\ServiceOrder;
use App\Models\User;
use Tests\DatabaseMigrations;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ServiceOrderControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function test_technician_can_create_service_order()
    {
        $technician = User::factory()->create(['role' => 'technician']);
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($technician);

        $response = $this->post('/ordens', [
            'title' => 'Nova Ordem de Serviço',
            'description' => 'Descrição da nova ordem',
            'client_id' => $client->id
        ]);

        $response->assertRedirect('/ordens');
        
        $this->assertDatabaseHas('service_orders', [
            'title' => 'Nova Ordem de Serviço',
            'description' => 'Descrição da nova ordem',
            'status' => 'pendente',
            'client_id' => $client->id,
            'technician_id' => $technician->id
        ]);
    }

    public function test_client_cannot_create_service_order()
    {
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($client);

        $response = $this->post('/ordens', [
            'title' => 'Ordem Não Autorizada',
            'description' => 'Cliente tentando criar ordem',
            'client_id' => $client->id
        ]);

        $response->assertStatus(403);
        
        $this->assertDatabaseMissing('service_orders', [
            'title' => 'Ordem Não Autorizada'
        ]);
    }

    public function test_technician_can_update_own_service_order()
    {
        $technician = User::factory()->create(['role' => 'technician']);
        $client = User::factory()->create(['role' => 'client']);
        
        $order = ServiceOrder::create([
            'title' => 'Ordem Original',
            'description' => 'Descrição original',
            'status' => 'pendente',
            'client_id' => $client->id,
            'technician_id' => $technician->id
        ]);

        $this->actingAs($technician);

        $response = $this->put("/ordens/{$order->id}", [
            'title' => 'Ordem Atualizada',
            'description' => 'Descrição atualizada',
            'status' => 'em_andamento'
        ]);

        $response->assertRedirect('/ordens');
        
        $this->assertDatabaseHas('service_orders', [
            'id' => $order->id,
            'title' => 'Ordem Atualizada',
            'description' => 'Descrição atualizada',
            'status' => 'em_andamento'
        ]);
    }

    public function test_event_is_dispatched_when_order_completed()
    {
        Event::fake();
        
        $technician = User::factory()->create(['role' => 'technician']);
        $client = User::factory()->create(['role' => 'client']);
        
        $order = ServiceOrder::create([
            'title' => 'Ordem para Concluir',
            'description' => 'Descrição da ordem',
            'status' => 'em_andamento',
            'client_id' => $client->id,
            'technician_id' => $technician->id
        ]);

        $this->actingAs($technician);

        $response = $this->put("/ordens/{$order->id}", [
            'title' => 'Ordem para Concluir',
            'description' => 'Descrição da ordem',
            'status' => 'concluida'
        ]);

        $response->assertRedirect('/ordens');
        
        Event::assertDispatched(\App\Events\ServiceOrderCompleted::class, function ($event) use ($order) {
            return $event->serviceOrder->id === $order->id;
        });
    }

    public function test_technician_cannot_update_other_technicians_order()
    {
        $technician1 = User::factory()->create(['role' => 'technician']);
        $technician2 = User::factory()->create(['role' => 'technician']);
        $client = User::factory()->create(['role' => 'client']);
        
        $order = ServiceOrder::create([
            'title' => 'Ordem de Outro Técnico',
            'description' => 'Descrição da ordem',
            'status' => 'pendente',
            'client_id' => $client->id,
            'technician_id' => $technician1->id
        ]);

        $this->actingAs($technician2);

        $response = $this->put("/ordens/{$order->id}", [
            'title' => 'Ordem Modificada',
            'description' => 'Modificação não autorizada',
            'status' => 'em_andamento'
        ]);

        $response->assertStatus(403);
        
        $this->assertDatabaseMissing('service_orders', [
            'id' => $order->id,
            'title' => 'Ordem Modificada'
        ]);
    }

    public function test_client_can_view_own_service_orders()
    {
        $technician = User::factory()->create(['role' => 'technician']);
        $client = User::factory()->create(['role' => 'client']);
        
        $order = ServiceOrder::create([
            'title' => 'Ordem do Cliente',
            'description' => 'Descrição da ordem',
            'status' => 'pendente',
            'client_id' => $client->id,
            'technician_id' => $technician->id
        ]);

        $this->actingAs($client);

        $response = $this->get('/ordens');

        $response->assertStatus(200);
        $response->assertSee('Ordem do Cliente');
    }

    public function test_client_cannot_view_other_clients_service_orders()
    {
        $technician = User::factory()->create(['role' => 'technician']);
        $client1 = User::factory()->create(['role' => 'client']);
        $client2 = User::factory()->create(['role' => 'client']);
        
        $order = ServiceOrder::create([
            'title' => 'Ordem do Cliente 1',
            'description' => 'Descrição da ordem',
            'status' => 'pendente',
            'client_id' => $client1->id,
            'technician_id' => $technician->id
        ]);

        $this->actingAs($client2);

        $response = $this->get("/ordens/{$order->id}");

        $response->assertStatus(403);
    }
}
