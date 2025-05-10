<?php

namespace App\Policies;

use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ServiceOrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ServiceOrder $serviceOrder): bool
    {
        return $user->id === $serviceOrder->client_id || 
               $user->id === $serviceOrder->technician_id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ServiceOrder $serviceOrder): bool
    {
        // Apenas técnicos podem atualizar as ordens de serviço
        return $user->role === 'technician' && $user->id === $serviceOrder->technician_id;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Qualquer usuário autenticado pode ver suas ordens
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'technician'; // Apenas técnicos podem criar ordens
    }
}
