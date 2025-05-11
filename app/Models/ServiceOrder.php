<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para Ordem de Serviço.
 *
 * Representa uma ordem de serviço no sistema, contendo informações como
 * título, descrição, status e relacionamentos com clientes e técnicos.
 *
 * @property int $id ID único da ordem de serviço
 * @property string $title Título da ordem de serviço
 * @property string $description Descrição detalhada do serviço
 * @property string $status Status atual ('pendente', 'em_andamento', 'concluida')
 * @property int $client_id ID do cliente associado
 * @property int $technician_id ID do técnico responsável
 * @property \Carbon\Carbon $created_at Data de criação
 * @property \Carbon\Carbon $updated_at Data da última atualização
 * @property-read \App\Models\User $client Cliente associado à ordem de serviço
 * @property-read \App\Models\User $technician Técnico responsável pela ordem de serviço
 */
class ServiceOrder extends Model
{
    use HasFactory;

    /**
     * Os atributos que podem ser preenchidos em massa.
     *
     * @var array<string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'client_id',
        'technician_id',
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Obtém o cliente associado à ordem de serviço.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Obtém o técnico responsável pela ordem de serviço.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }
}
