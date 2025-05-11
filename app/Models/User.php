<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modelo para Usuário do sistema.
 *
 * Representa um usuário autenticável no sistema, podendo ter função de cliente ou técnico,
 * com relacionamentos para ordens de serviço.
 *
 * @property int $id ID único do usuário
 * @property string $name Nome completo do usuário
 * @property string $email Endereço de e-mail (único)
 * @property string $password Senha criptografada
 * @property string $role Função do usuário ('client', 'technician')
 * @property \Carbon\Carbon|null $email_verified_at Data de verificação do e-mail
 * @property string|null $remember_token Token para "lembrar-me"
 * @property \Carbon\Carbon $created_at Data de criação
 * @property \Carbon\Carbon $updated_at Data da última atualização
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ServiceOrder[] $serviceOrdersAsClient Ordens de serviço onde o usuário é cliente
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ServiceOrder[] $serviceOrdersAsTechnician Ordens de serviço onde o usuário é técnico
 * @property-read int|null $service_orders_as_client_count Contador de ordens como cliente
 * @property-read int|null $service_orders_as_technician_count Contador de ordens como técnico
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * Os atributos que devem ser escondidos nas serializações.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Obtém todas as ordens de serviço onde o usuário é o cliente.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function serviceOrdersAsClient(): HasMany
    {
        return $this->hasMany(ServiceOrder::class, 'client_id');
    }

    /**
     * Obtém todas as ordens de serviço onde o usuário é o técnico.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function serviceOrdersAsTechnician(): HasMany
    {
        return $this->hasMany(ServiceOrder::class, 'technician_id');
    }
}
