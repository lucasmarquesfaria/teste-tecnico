<?php

namespace App\Policies;

use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Política de autorização para ordens de serviço.
 *
 * Esta classe define as regras de autorização para as ações
 * relacionadas às ordens de serviço, controlando o acesso
 * com base nas funções dos usuários (cliente/técnico) e
 * nas relações dos usuários com as ordens.
 */
class ServiceOrderPolicy
{
    use HandlesAuthorization;

    /**
     * Determina se o usuário pode visualizar uma ordem de serviço específica.
     *
     * O acesso é permitido apenas se o usuário for o cliente associado
     * ou o técnico responsável pela ordem.
     *
     * @param  \App\Models\User  $user Usuário que tenta acessar
     * @param  \App\Models\ServiceOrder  $serviceOrder Ordem de serviço acessada
     * @return bool
     */
    public function view(User $user, ServiceOrder $serviceOrder): bool
    {
        return $user->id === $serviceOrder->client_id || 
               $user->id === $serviceOrder->technician_id;
    }

    /**
     * Determina se o usuário pode atualizar uma ordem de serviço.
     *
     * Apenas técnicos podem atualizar as ordens de serviço,
     * e somente aquelas pelas quais são responsáveis.
     *
     * @param  \App\Models\User  $user Usuário que tenta atualizar
     * @param  \App\Models\ServiceOrder  $serviceOrder Ordem de serviço a ser atualizada
     * @return bool
     */
    public function update(User $user, ServiceOrder $serviceOrder): bool
    {
        return $user->role === 'technician' && $user->id === $serviceOrder->technician_id;
    }
    
    /**
     * Determina se o usuário pode ver a lista de ordens de serviço.
     *
     * Todos os usuários autenticados podem ver a lista,
     * mas as consultas serão filtradas por outros mecanismos.
     *
     * @param  \App\Models\User  $user Usuário logado
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determina se o usuário pode criar uma nova ordem de serviço.
     *
     * Apenas usuários com função de técnico podem criar novas ordens.
     *
     * @param  \App\Models\User  $user Usuário logado
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->role === 'technician';
    }
}
