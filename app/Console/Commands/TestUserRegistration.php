<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class TestUserRegistration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test-registration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testa o processo de registro de usuário';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Tentando criar um novo usuário através do model...');
            
            $user = User::create([
                'name' => 'Usuário Teste',
                'email' => 'teste'.time().'@example.com', // Garante email único
                'password' => Hash::make('password123'),
                'role' => 'client',
            ]);
            
            $this->info('Usuário criado com sucesso!');
            $this->line("ID: {$user->id} | Nome: {$user->name} | Email: {$user->email} | Role: {$user->role}");
            
            // Conta quantos usuários existem
            $count = User::count();
            $this->info("Total de usuários na tabela: {$count}");
            
            // Lista os últimos 5 usuários
            $this->info('Últimos 5 usuários:');
            User::latest()->take(5)->get()->each(function($user) {
                $this->line("ID: {$user->id} | Nome: {$user->name} | Email: {$user->email} | Role: {$user->role}");
            });
        } catch (\Exception $e) {
            $this->error('Erro ao tentar criar usuário:');
            $this->error($e->getMessage());
            
            // Mostra informações adicionais em caso de erro
            $this->line("\nInformações adicionais:");
            $this->line("Class: " . get_class($e));
            $this->line("File: " . $e->getFile() . ":" . $e->getLine());
            
            // Em caso de erro de banco, verifica conexão
            $this->line("\nVerificando conexão com o banco...");
            try {
                $db = \DB::connection();
                $this->info("Conexão com o banco: " . $db->getName());
                $this->info("Banco de dados: " . $db->getDatabaseName());
            } catch (\Exception $dbError) {
                $this->error("Erro de conexão com o banco: " . $dbError->getMessage());
            }
        }
    }
}
