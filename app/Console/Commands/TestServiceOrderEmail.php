<?php

namespace App\Console\Commands;

use App\Events\ServiceOrderCompleted;
use App\Models\ServiceOrder;
use Illuminate\Console\Command;

class TestServiceOrderEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-email {order_id? : ID da ordem de serviço para enviar o e-mail}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia um e-mail de teste para uma ordem de serviço concluída';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orderId = $this->argument('order_id');
        
        if (!$orderId) {
            // Se não foi informado um ID, listamos as ordens disponíveis
            $orders = ServiceOrder::with(['client', 'technician'])->get();
            
            if ($orders->isEmpty()) {
                $this->error('Não há ordens de serviço cadastradas.');
                return 1;
            }
            
            $this->info('Ordens de serviço disponíveis:');
            $headers = ['ID', 'Título', 'Status', 'Cliente', 'Técnico'];
            $rows = [];
            
            foreach ($orders as $order) {
                $rows[] = [
                    $order->id,
                    $order->title,
                    $order->status,
                    $order->client->name,
                    $order->technician->name,
                ];
            }
            
            $this->table($headers, $rows);
            $orderId = $this->ask('Digite o ID da ordem de serviço para enviar o e-mail');
        }
        
        // Busca a ordem pelo ID
        $order = ServiceOrder::find($orderId);
        
        if (!$order) {
            $this->error('Ordem de serviço não encontrada.');
            return 1;
        }
        
        // Garante que as relações estão carregadas
        $order->load(['client', 'technician']);
        
        // Dispara o evento
        event(new ServiceOrderCompleted($order));
        
        $this->info("E-mail de notificação de conclusão enviado para {$order->client->email}");
        $this->line("Verifique a fila de processamento com 'php artisan queue:work'");
        
        return 0;
    }
}
