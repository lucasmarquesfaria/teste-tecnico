<?php

namespace Tests\Unit\Models;

use App\Models\ServiceOrder;
use App\Models\User;
use Tests\DatabaseMigrations;
use Tests\TestCase;

class ServiceOrderTest extends TestCase
{
    use DatabaseMigrations;public function test_service_order_has_valid_fields()
    {
        $client = User::factory()->client()->create();
        $technician = User::factory()->technician()->create();

        $order = ServiceOrder::factory()
            ->pendente()
            ->comUsuarios($client, $technician)
            ->create([
                'title' => 'Teste de Ordem de Serviço',
                'description' => 'Descrição da ordem de serviço para teste',
            ]);

        $this->assertEquals('Teste de Ordem de Serviço', $order->title);
        $this->assertEquals('Descrição da ordem de serviço para teste', $order->description);
        $this->assertEquals('pendente', $order->status);
        $this->assertEquals($client->id, $order->client_id);
        $this->assertEquals($technician->id, $order->technician_id);
    }

    public function test_service_order_belongs_to_client()
    {
        $client = User::factory()->create(['role' => 'client']);
        $technician = User::factory()->create(['role' => 'technician']);

        $order = ServiceOrder::create([
            'title' => 'Teste de Relacionamento',
            'description' => 'Testando relacionamento com cliente',
            'status' => 'pendente',
            'client_id' => $client->id,
            'technician_id' => $technician->id,
        ]);

        $this->assertInstanceOf(User::class, $order->client);
        $this->assertEquals($client->id, $order->client->id);
    }

    public function test_service_order_belongs_to_technician()
    {
        $client = User::factory()->create(['role' => 'client']);
        $technician = User::factory()->create(['role' => 'technician']);

        $order = ServiceOrder::create([
            'title' => 'Teste de Relacionamento',
            'description' => 'Testando relacionamento com técnico',
            'status' => 'pendente',
            'client_id' => $client->id,
            'technician_id' => $technician->id,
        ]);

        $this->assertInstanceOf(User::class, $order->technician);
        $this->assertEquals($technician->id, $order->technician->id);
    }
}
