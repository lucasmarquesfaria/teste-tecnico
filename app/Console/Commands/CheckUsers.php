<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lista os usuários cadastrados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = DB::table('users')->get();
        
        if ($users->isEmpty()) {
            $this->info('Não há usuários cadastrados.');
            return;
        }
        
        $this->info('Usuários cadastrados:');
        foreach ($users as $user) {
            $this->line("ID: {$user->id} | Nome: {$user->name} | Email: {$user->email} | Perfil: {$user->role}");
        }
    }
}
