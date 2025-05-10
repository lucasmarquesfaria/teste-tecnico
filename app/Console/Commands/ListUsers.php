<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lista todos os usuários cadastrados no sistema';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->info('Não há usuários cadastrados no sistema.');
            return 0;
        }
        
        $this->table(
            ['ID', 'Nome', 'Email', 'Perfil', 'Criado em'],
            $users->map(function ($user) {
                return [
                    $user->id,
                    $user->name,
                    $user->email,                    $user->role,
                    $user->created_at ? $user->created_at->format('d/m/Y H:i:s') : 'N/A',
                ];
            })
        );
        
        $this->info("Total de usuários: " . $users->count());
        
        return 0;
    }
}
