<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function create()
    {
        return view('users.create');
    }    public function store(Request $request)
    {
        \Log::info('Tentativa de registro de usuário', ['data' => $request->all()]);
        
        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'role' => ['required', Rule::in(['client', 'technician'])],
            ]);
            
            \Log::info('Dados validados, tentando criar usuário', ['validated_data' => array_diff_key($validated, ['password' => ''])]);
            
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);
            
            \Log::info('Usuário criado com sucesso', ['user_id' => $user->id]);
            
            return redirect()->route('users.create')->with('success', 'Usuário cadastrado com sucesso!');
        } catch (\Exception $e) {
            \Log::error('Erro ao criar usuário', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()->with('error', 'Erro ao cadastrar usuário: ' . $e->getMessage());
        }
    }
}
