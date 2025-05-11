<?php

// Arquivo renomeado para NotifyClientOfCompletion.php
// Arquivo desativado: toda a lógica de notificação está em NotifyClientOfCompletion.php
// Esta classe não deve ser registrada no EventServiceProvider.
// Para garantir que apenas o modelo customizado seja enviado, remova qualquer referência a este listener.

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
    // public function handle(ServiceOrderCompleted $event): void
    // {
    //     // NÃO USAR: modelo de e-mail customizado está em NotifyClientOfCompletion
    // }
}
