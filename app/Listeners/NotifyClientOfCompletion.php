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
     */    public function handle(ServiceOrderCompleted $event): void
    {
        $serviceOrder = $event->serviceOrder;
        
        // Usar cache->remember para evitar race conditions e garantir que o email só seja enviado uma vez
        $cacheKey = 'order_completed_notification_' . $serviceOrder->id;
        
        // O método remember só executará o callback se a chave não existir no cache
        $notificationSent = Cache::remember(
            $cacheKey,
            now()->addDays(1),  // Cache válido por 1 dia para maior segurança
            function() use ($serviceOrder) {
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
                
                return now()->toDateTimeString(); // Armazena quando foi enviado
            }
        );
        
        // Se chegou aqui e o valor retornado não é do momento atual, significa que já existia no cache
        if ($notificationSent !== now()->toDateTimeString()) {
            Log::info('Notificação para ordem #' . $serviceOrder->id . ' já foi enviada em: ' . $notificationSent . '. Ignorando.');
        }
    }
}
