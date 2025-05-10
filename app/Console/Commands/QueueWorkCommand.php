<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class QueueWorkCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:queue-work';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicia o processamento das filas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando processamento da fila...');
        $this->info('Use Ctrl+C para parar o processamento.');
        
        Artisan::call('queue:work', [
            '--tries' => 3,
            '--sleep' => 3,
        ]);
    }
}
