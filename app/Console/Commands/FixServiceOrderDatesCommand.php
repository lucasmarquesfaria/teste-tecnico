<?php

namespace App\Console\Commands;

use App\Models\ServiceOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixServiceOrderDatesCommand extends Command
{
    /**
     * O nome e assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'app:fix-service-order-dates';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Corrige os timestamps das ordens de serviço para garantir ordenação correta';

    /**
     * Executa o comando do console.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Verificando ordens de serviço...');
        
        // Buscar todas as ordens de serviço
        $orders = ServiceOrder::all();
        
        if ($orders->isEmpty()) {
            $this->info('Nenhuma ordem de serviço encontrada.');
            return 0;
        }
        
        $this->info('Encontradas ' . $orders->count() . ' ordens de serviço.');
        $this->info('Corrigindo timestamps...');
        
        // Verificar e corrigir timestamps
        $count = 0;
        foreach ($orders as $order) {
            if (!$order->created_at) {
                $order->created_at = now();
                $order->updated_at = now();
                $order->save();
                $count++;
            }
        }
        
        $this->info("Corrigidos timestamps de $count ordens de serviço.");
        $this->info('Verificando a ordem das datas...');
        
        // Verificar se há ordens com mesmos timestamps
        $duplicates = DB::table('service_orders')
            ->select('created_at', DB::raw('COUNT(*) as count'))
            ->groupBy('created_at')
            ->having('count', '>', 1)
            ->get();
        
        if ($duplicates->isEmpty()) {
            $this->info('Não há ordens com timestamps duplicados.');
            return 0;
        }
        
        $this->warn('Encontrados ' . $duplicates->count() . ' timestamps duplicados.');
        $this->info('Corrigindo timestamps duplicados...');
        
        // Corrigir duplicatas
        $count = 0;
        foreach ($duplicates as $duplicate) {
            $duplicateOrders = ServiceOrder::where('created_at', $duplicate->created_at)
                ->orderBy('id')
                ->get();
            
            // Adicionar 1 segundo para cada ordem duplicada após a primeira
            $i = 0;
            foreach ($duplicateOrders as $order) {
                if ($i > 0) {
                    $order->created_at = Carbon::parse($order->created_at)->addSeconds($i);
                    $order->save();
                    $count++;
                }
                $i++;
            }
        }
        
        $this->info("Corrigidos $count ordens com timestamps duplicados.");
        $this->info('Operação concluída com sucesso!');
        
        return 0;
    }
}
