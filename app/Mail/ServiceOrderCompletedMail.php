<?php

namespace App\Mail;

use App\Models\ServiceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceOrderCompletedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $serviceOrder;

    public function __construct(ServiceOrder $serviceOrder)
    {
        $this->serviceOrder = $serviceOrder;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ordem de Serviço #' . $this->serviceOrder->id . ' - Concluída',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.service-order-completed',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
