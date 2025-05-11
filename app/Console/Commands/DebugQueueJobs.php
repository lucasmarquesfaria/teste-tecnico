<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DebugQueueJobs extends Command
{
    protected $signature = 'app:debug-queue-jobs';
    protected $description = 'Lista os jobs pendentes na fila (tabela jobs) para debug';

    public function handle()
    {
        $jobs = DB::table('jobs')->orderBy('id')->get();

        if ($jobs->isEmpty()) {
            $this->info('Nenhum job pendente na fila.');
            return 0;
        }

        $this->table(
            ['ID', 'Queue', 'Tentativas', 'Job', 'Criado em'],
            $jobs->map(function ($job) {
                $payload = json_decode($job->payload, true);
                $jobName = $payload['displayName'] ?? ($payload['job'] ?? 'Desconhecido');
                return [
                    $job->id,
                    $job->queue,
                    $job->attempts,
                    $jobName,
                    Carbon::parse($job->created_at)->format('d/m/Y H:i:s'),
                ];
            })->toArray()
        );
        return 0;
    }
}
