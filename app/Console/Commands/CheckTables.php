<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica a estrutura das tabelas no banco de dados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Lista todas as tabelas
        $tables = DB::select('SELECT name FROM sqlite_master WHERE type="table" ORDER BY name');
        
        $this->info('Tabelas no banco de dados:');
        foreach ($tables as $table) {
            $this->line("- {$table->name}");
        }
        
        // Verifica se a tabela users existe
        if (Schema::hasTable('users')) {
            $this->info("\nEstrutura da tabela users:");
            $columns = DB::select('PRAGMA table_info(users)');
            foreach ($columns as $column) {
                $this->line("- {$column->name} ({$column->type})");
            }
        } else {
            $this->error("\nA tabela users não existe!");
        }
        
        // Verifica se a tabela service_orders existe
        if (Schema::hasTable('service_orders')) {
            $this->info("\nEstrutura da tabela service_orders:");
            $columns = DB::select('PRAGMA table_info(service_orders)');
            foreach ($columns as $column) {
                $this->line("- {$column->name} ({$column->type})");
            }
        } else {
            $this->error("\nA tabela service_orders não existe!");
        }
        
        // Verifica se a tabela jobs existe
        if (Schema::hasTable('jobs')) {
            $this->info("\nA tabela jobs existe.");
        } else {
            $this->error("\nA tabela jobs não existe!");
        }
    }
}
