<?php

namespace App\Notifications;

use App\Models\ServiceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServiceOrderCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $serviceOrder;

    public function __construct(ServiceOrder $serviceOrder)
    {
        $this->serviceOrder = $serviceOrder;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $order = $this->serviceOrder;
        $technician = $order->technician;
        return (new MailMessage)
            ->subject('Ordem de Serviço #' . $order->id . ' - Concluída')
            ->greeting('Olá ' . $order->client->name . ',')
            ->line('Sua ordem de serviço foi concluída com sucesso!')
            ->line('Título: ' . $order->title)
            ->line('Descrição: ' . $order->description)
            ->line('Técnico Responsável: ' . ($technician ? $technician->name : '-'))
            ->line('Data de Conclusão: ' . $order->updated_at->format('d/m/Y H:i'))
            ->line('Agradecemos por confiar em nossos serviços. Caso tenha alguma dúvida ou feedback, estamos à disposição.')
            ->salutation('Atenciosamente, Equipe de Suporte');
    }
}
