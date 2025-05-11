<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Comando para depurar jobs na fila.
 *
 * Este comando exibe informações sobre os jobs pendentes na fila,
 * incluindo o tipo de job, quando foi criado e os dados associados.
 * É uma ferramenta útil para verificar o estado atual das filas
 * e diagnosticar problemas com o processamento de jobs.
 */
class DebugQueueJobsCommand extends Command
{
    /**
     * O nome e assinatura do comando do console.
     *
     * @var string
     */
    protected $signature = 'app:debug-queue-jobs';

    /**
     * A descrição do comando do console.
     *
     * @var string
     */
    protected $description = 'Mostra os jobs pendentes na fila para depuração';

    /**
     * Executa o comando do console.
     *
     * @return int
     */    public function handle()
    {
        $jobs = DB::table('jobs')->get();

        if ($jobs->isEmpty()) {
            $this->info('Nenhum job pendente na fila.');
            return 0;
        }

        $this->info('Jobs pendentes na fila: ' . $jobs->count());
        $this->line('');

        $headers = ['ID', 'Fila', 'Tentativas', 'Tipo de Job', 'Criado em'];
        $rows = [];

        foreach ($jobs as $job) {
            $payload = json_decode($job->payload);
            $jobType = isset($payload->displayName) ? $payload->displayName : 'Desconhecido';
            
            $rows[] = [
                $job->id,
                $job->queue,
                $job->attempts,
                $jobType,
                date('Y-m-d H:i:s', $job->created_at),
            ];
        }

        $this->table($headers, $rows);
        
        $this->line('');
        $this->info('Para processar os jobs, execute: php artisan queue:work');

        return 0;
    }
}
