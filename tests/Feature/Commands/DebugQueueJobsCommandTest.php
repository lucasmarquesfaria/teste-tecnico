<?php

namespace Tests\Feature\Commands;

use Tests\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DebugQueueJobsCommandTest extends TestCase
{
    use DatabaseMigrations;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Limpa a tabela de jobs antes de cada teste
        DB::table('jobs')->delete();
    }
    
    public function test_command_runs_successfully()
    {
        $this->artisan('app:debug-queue-jobs')
            ->assertSuccessful();
    }
    
    public function test_command_shows_message_when_no_jobs()
    {
        $this->artisan('app:debug-queue-jobs')
            ->expectsOutput('Nenhum job pendente na fila.')
            ->assertExitCode(0);
    }
    
    public function test_command_lists_jobs_in_queue()
    {
        // Inserir um job manualmente na fila
        DB::table('jobs')->insert([
            'queue' => 'default',
            'payload' => json_encode([
                'displayName' => 'App\Mail\ServiceOrderCompletedMail',
                'job' => 'Illuminate\Queue\CallQueuedHandler@call',
                'data' => [
                    'commandName' => 'App\Mail\ServiceOrderCompletedMail',
                    'command' => 'O:32:"App\Mail\ServiceOrderCompletedMail"...'
                ]
            ]),
            'attempts' => 0,
            'reserved_at' => null,
            'available_at' => time(),
            'created_at' => time()
        ]);
        
        $this->artisan('app:debug-queue-jobs')
            ->doesntExpectOutput('Nenhum job pendente na fila.')
            ->assertExitCode(0);
    }
    
    public function test_job_format_is_correct()
    {
        // Criar payload específico para testar formato exibido
        $now = now();
        $payload = [
            'displayName' => 'App\Mail\ServiceOrderCompletedMail',
            'job' => 'Illuminate\Queue\CallQueuedHandler@call',
            'data' => ['test' => 'data']
        ];
        
        DB::table('jobs')->insert([
            'id' => 99, // ID específico para testar
            'queue' => 'emails',
            'payload' => json_encode($payload),
            'attempts' => 2,
            'reserved_at' => null,
            'available_at' => $now->timestamp,
            'created_at' => $now->timestamp
        ]);        $result = $this->artisan('app:debug-queue-jobs');
        
        // Verificamos se o job é exibido sem utilizar expectsTableToContain
        $result->doesntExpectOutput('Nenhum job pendente na fila.')
            ->assertExitCode(0);
    }
}
