<?php
namespace App\Notifications;

use App\Models\ServiceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Notificação enviada aos clientes quando uma ordem de serviço é concluída.
 *
 * Esta classe representa uma notificação que é enviada por e-mail ao cliente
 * quando sua ordem de serviço é marcada como concluída pelo técnico.
 * Implementa ShouldQueue para ser processada de forma assíncrona.
 */
class ServiceOrderCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    /**
     * A ordem de serviço que foi concluída.
     *
     * @var \App\Models\ServiceOrder
     */
    public $serviceOrder;
    
    /**
     * Número máximo de tentativas para enviar a notificação.
     *
     * @var int
     */
    public $tries = 3;
      /**
     * Cria uma nova instância da notificação.
     *
     * @param  \App\Models\ServiceOrder  $serviceOrder
     * @return void
     */
    public function __construct(ServiceOrder $serviceOrder)
    {
        $this->serviceOrder = $serviceOrder;
    }
    
    /**
     * Determina quais canais serão utilizados para entregar a notificação.
     *
     * @param  mixed  $notifiable
     * @return array<string>
     */
    public function via($notifiable)
    {
        return ['mail'];
    }
    
    /**
     * Constrói a representação em e-mail da notificação.
     *
     * Utiliza um template Blade personalizado para gerar o conteúdo do e-mail
     * com os detalhes da ordem de serviço concluída.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Ordem de Serviço #' . $this->serviceOrder->id . ' - Concluída')
            ->view('emails.service-order-completed', ['serviceOrder' => $this->serviceOrder]);
    }
}
