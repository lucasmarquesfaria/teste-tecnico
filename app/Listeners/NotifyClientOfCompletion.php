<?php

namespace App\Listeners;

use App\Events\ServiceOrderCompleted;
use App\Notifications\ServiceOrderCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Listener responsável por notificar o cliente quando uma ordem de serviço é concluída.
 *
 * Este listener é executado como um job na fila quando o evento ServiceOrderCompleted
 * é disparado, enviando uma notificação ao cliente da ordem de serviço.
 * Implementa mecanismos para evitar o envio duplicado de notificações.
 */
class NotifyClientOfCompletion implements ShouldQueue
{
    use InteractsWithQueue;
    
    /**
     * Número máximo de tentativas para processar este job.
     *
     * @var int
     */
    public $tries = 3;
    /**
     * Manipula o evento de ordem de serviço concluída.
     *
     * Recebe o evento disparado quando uma ordem de serviço é marcada como concluída,
     * verifica se a notificação já foi enviada usando cache para evitar duplicações,
     * e então envia uma notificação ao cliente informando sobre a conclusão da ordem.
     *
     * @param  \App\Events\ServiceOrderCompleted  $event
     * @return void
     */
    public function handle(ServiceOrderCompleted $event): void
    {
        $serviceOrder = $event->serviceOrder;
        
        // Verificar se esta notificação já foi enviada (para evitar duplicações)
        $cacheKey = 'order_completed_notification_' . $serviceOrder->id;
        if (Cache::has($cacheKey)) {
            Log::info('Notification for order #' . $serviceOrder->id . ' already sent. Skipping.');
            return;
        }
        
        // Carregar relacionamentos se ainda não estiverem carregados
        if (!$serviceOrder->relationLoaded('client')) {
            $serviceOrder->load('client');
        }
        
        if (!$serviceOrder->relationLoaded('technician')) {
            $serviceOrder->load('technician');
        }
        
        // Enviar notificação ao cliente
        Log::info('Enviando notificação para ordem #' . $serviceOrder->id . ' para o cliente: ' . $serviceOrder->client->email);
        $serviceOrder->client->notify(new ServiceOrderCompletedNotification($serviceOrder));
        
        // Marcar como enviado (expira após 1 hora)
        Cache::put($cacheKey, true, 3600);
    }
}
