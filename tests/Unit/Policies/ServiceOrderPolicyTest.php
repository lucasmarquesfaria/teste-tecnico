<?php

namespace Tests\Unit\Policies;

use App\Models\ServiceOrder;
use App\Models\User;
use App\Policies\ServiceOrderPolicy;
use Tests\DatabaseMigrations;
use Tests\TestCase;

class ServiceOrderPolicyTest extends TestCase
{
    use DatabaseMigrations;
    
    private $policy;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new ServiceOrderPolicy();
    }
    
    public function test_client_can_view_own_order()
    {
        $client = User::factory()->client()->create();
        $technician = User::factory()->technician()->create();
        
        $order = ServiceOrder::factory()
            ->comUsuarios($client, $technician)
            ->create();
            
        $this->assertTrue($this->policy->view($client, $order));
    }
    
    public function test_client_cannot_view_other_clients_order()
    {
        $client1 = User::factory()->client()->create();
        $client2 = User::factory()->client()->create();
        $technician = User::factory()->technician()->create();
        
        $order = ServiceOrder::factory()
            ->comUsuarios($client1, $technician)
            ->create();
            
        $this->assertFalse($this->policy->view($client2, $order));
    }
    
    public function test_technician_can_view_own_order()
    {
        $client = User::factory()->client()->create();
        $technician = User::factory()->technician()->create();
        
        $order = ServiceOrder::factory()
            ->comUsuarios($client, $technician)
            ->create();
            
        $this->assertTrue($this->policy->view($technician, $order));
    }
    
    public function test_technician_cannot_view_other_technicians_order()
    {
        $client = User::factory()->client()->create();
        $technician1 = User::factory()->technician()->create();
        $technician2 = User::factory()->technician()->create();
        
        $order = ServiceOrder::factory()
            ->comUsuarios($client, $technician1)
            ->create();
            
        $this->assertFalse($this->policy->view($technician2, $order));
    }
    
    public function test_technician_can_update_own_order()
    {
        $client = User::factory()->client()->create();
        $technician = User::factory()->technician()->create();
        
        $order = ServiceOrder::factory()
            ->comUsuarios($client, $technician)
            ->create();
            
        $this->assertTrue($this->policy->update($technician, $order));
    }
    
    public function test_technician_cannot_update_other_technicians_order()
    {
        $client = User::factory()->client()->create();
        $technician1 = User::factory()->technician()->create();
        $technician2 = User::factory()->technician()->create();
        
        $order = ServiceOrder::factory()
            ->comUsuarios($client, $technician1)
            ->create();
            
        $this->assertFalse($this->policy->update($technician2, $order));
    }
    
    public function test_client_cannot_update_any_order()
    {
        $client = User::factory()->client()->create();
        $technician = User::factory()->technician()->create();
        
        $order = ServiceOrder::factory()
            ->comUsuarios($client, $technician)
            ->create();
            
        $this->assertFalse($this->policy->update($client, $order));
    }
}
