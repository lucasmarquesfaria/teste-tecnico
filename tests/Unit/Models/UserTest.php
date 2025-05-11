<?php

namespace Tests\Unit\Models;

use App\Models\ServiceOrder;
use App\Models\User;
use Tests\DatabaseMigrations;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    public function test_user_has_valid_fields()
    {
        $user = User::create([
            'name' => 'Usuário Teste',
            'email' => 'usuario@teste.com',
            'password' => bcrypt('senha123'),
            'role' => 'client',
        ]);

        $this->assertEquals('Usuário Teste', $user->name);
        $this->assertEquals('usuario@teste.com', $user->email);
        $this->assertEquals('client', $user->role);
    }

    public function test_client_has_service_orders()
    {
        $client = User::factory()->create(['role' => 'client']);
        $technician = User::factory()->create(['role' => 'technician']);

        $order = ServiceOrder::create([
            'title' => 'Ordem do Cliente',
            'description' => 'Testando ordens do cliente',
            'status' => 'pendente',
            'client_id' => $client->id,
            'technician_id' => $technician->id,
        ]);

        $this->assertCount(1, $client->serviceOrdersAsClient);
        $this->assertEquals($order->id, $client->serviceOrdersAsClient->first()->id);
    }

    public function test_technician_has_service_orders()
    {
        $client = User::factory()->create(['role' => 'client']);
        $technician = User::factory()->create(['role' => 'technician']);

        $order = ServiceOrder::create([
            'title' => 'Ordem do Técnico',
            'description' => 'Testando ordens do técnico',
            'status' => 'pendente',
            'client_id' => $client->id,
            'technician_id' => $technician->id,
        ]);

        $this->assertCount(1, $technician->serviceOrdersAsTechnician);
        $this->assertEquals($order->id, $technician->serviceOrdersAsTechnician->first()->id);
    }

    public function test_user_roles_are_valid()
    {
        $client = User::factory()->create(['role' => 'client']);
        $technician = User::factory()->create(['role' => 'technician']);

        $this->assertEquals('client', $client->role);
        $this->assertEquals('technician', $technician->role);
    }
}
