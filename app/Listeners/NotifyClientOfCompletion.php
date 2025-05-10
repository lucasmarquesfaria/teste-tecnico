<?php

namespace App\Listeners;

use App\Events\ServiceOrderCompleted;
use App\Mail\ServiceOrderCompletedMail;
use App\Notifications\ServiceOrderCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class NotifyClientOfCompletion implements ShouldQueue
{
    use InteractsWithQueue;
    public $tries = 3;
    public function __construct() {}
    public function handle(ServiceOrderCompleted $event): void
    {
        $serviceOrder = $event->serviceOrder;
        if (!$serviceOrder->relationLoaded('client')) {
            $serviceOrder->load('client');
        }
        if (!$serviceOrder->relationLoaded('technician')) {
            $serviceOrder->load('technician');
        }
        // Enviar notificação nativa Laravel para o cliente
        $serviceOrder->client->notify(new ServiceOrderCompletedNotification($serviceOrder));
    }
}
