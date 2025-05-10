<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar um cliente
        User::create([
            'name' => 'Cliente Teste',
            'email' => 'cliente@teste.com',
            'password' => Hash::make('senha123'),
            'role' => 'client',
        ]);

        // Criar um técnico
        User::create([
            'name' => 'Técnico Teste',
            'email' => 'tecnico@teste.com',
            'password' => Hash::make('senha123'),
            'role' => 'technician',
        ]);
    }
}
