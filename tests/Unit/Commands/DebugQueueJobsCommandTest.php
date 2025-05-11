<?php

namespace Tests\Unit\Commands;

use App\Console\Commands\DebugQueueJobs;
use Tests\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DebugQueueJobsCommandTest extends TestCase
{
    use DatabaseMigrations;
    
    public function test_command_reports_no_jobs_when_queue_is_empty()
    {
        // Garantir que não existem jobs
        DB::table('jobs')->delete();
        
        $this->artisan('app:debug-queue-jobs')
            ->expectsOutput('Nenhum job pendente na fila.')
            ->assertExitCode(0);
    }
      public function test_command_shows_jobs_when_queue_has_items()
    {
        // Limpar tabela de jobs
        DB::table('jobs')->delete();
        
        // Inserir um job manualmente
        $jobData = [
            'queue' => 'default',
            'payload' => json_encode([
                'displayName' => 'TestJob',
                'job' => 'App\Jobs\TestJob',
                'data' => ['foo' => 'bar']
            ]),
            'attempts' => 0,
            'created_at' => now()->timestamp,
            'available_at' => now()->timestamp
        ];
        DB::table('jobs')->insert($jobData);
          // Verificar se o comando exibe o job corretamente
        $this->artisan('app:debug-queue-jobs')
            ->doesntExpectOutput('Nenhum job pendente na fila.')
            ->assertExitCode(0);
    }
}
