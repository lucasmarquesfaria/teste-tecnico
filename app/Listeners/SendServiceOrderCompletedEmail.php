<?php

namespace App\Listeners;

use App\Events\ServiceOrderCompleted;
use App\Mail\ServiceOrderCompletedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendServiceOrderCompletedEmail implements ShouldQueue
{
    use InteractsWithQueue;
    
    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;
    
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ServiceOrderCompleted $event): void
    {
        $serviceOrder = $event->serviceOrder;
        
        // Carrega as relações necessárias caso não estejam
        if (!$serviceOrder->relationLoaded('client')) {
            $serviceOrder->load('client');
        }
        
        if (!$serviceOrder->relationLoaded('technician')) {
            $serviceOrder->load('technician');
        }
        
        // Envia o e-mail para o cliente
        Mail::to($serviceOrder->client->email)
            ->send(new ServiceOrderCompletedMail($serviceOrder));
    }
}
