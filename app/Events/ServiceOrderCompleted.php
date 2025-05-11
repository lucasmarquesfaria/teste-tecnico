<?php

namespace App\Events;

use App\Models\ServiceOrder;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Evento disparado quando uma ordem de serviço é concluída.
 *
 * Este evento é disparado sempre que o status de uma ordem de serviço
 * é alterado para "concluida". Ele carrega a instância da ordem de serviço
 * atualizada para ser utilizada pelos listeners, principalmente para o
 * envio de notificações ao cliente.
 */
class ServiceOrderCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * A instância da ordem de serviço que foi concluída.
     *
     * @var \App\Models\ServiceOrder
     */
    public $serviceOrder;

    /**
     * Cria uma nova instância do evento.
     *
     * @param  \App\Models\ServiceOrder  $serviceOrder A ordem de serviço concluída
     * @return void
     */
    public function __construct(ServiceOrder $serviceOrder)
    {
        $this->serviceOrder = $serviceOrder;
    }

    /**
     * Define os canais para broadcast do evento.
     *
     * @return array<\Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
