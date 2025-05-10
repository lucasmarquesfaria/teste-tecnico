<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CheckUserTableStructure extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-user-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica a estrutura da tabela users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Verificar se a tabela existe
        if (Schema::hasTable('users')) {
            $this->info('A tabela users existe.');
            
            // Colunas da tabela
            $columns = Schema::getColumnListing('users');
            $this->info('Colunas na tabela users:');
            foreach ($columns as $column) {
                $this->line("- " . $column);
            }
            
            // Se temos a coluna role
            $hasRole = Schema::hasColumn('users', 'role');
            $this->info('A coluna role ' . ($hasRole ? 'existe' : 'não existe') . ' na tabela users.');
            
            // Verificar com PRAGMA (SQLite)
            $columns = DB::select('PRAGMA table_info(users)');
            $this->info('Informações detalhadas da tabela users:');
            foreach ($columns as $column) {
                $this->line("- " . $column->name . " (" . $column->type . ")");
            }
        } else {
            $this->error('A tabela users não existe.');
        }
    }
}
