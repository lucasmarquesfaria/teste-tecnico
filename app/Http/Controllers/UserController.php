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
    }    
    public function store(Request $request)
    {
        \Log::info('Tentativa de registro de usuário', ['data' => $request->all()]);
        
        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'min:3',
                    'max:255',
                    'regex:/^[A-Za-zÀ-ú ]+$/',   
                ],
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    'unique:users,email',
                ],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    // Corrigido: regex sem conflito de aspas
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-={}[\]:;\\|,.<>\/?]).+$/',
                    'confirmed',
                ],
                'password_confirmation' => [
                    'required',
                ],
                'role' => ['required', Rule::in(['client', 'technician'])],
            ], [
                'name.required' => 'O nome é obrigatório.',
                'name.min' => 'O nome deve ter pelo menos 3 caracteres.',
                'name.regex' => 'O nome deve conter apenas letras e espaços.',
                'email.required' => 'O e-mail é obrigatório.',
                'email.email' => 'Informe um e-mail válido.',
                'email.unique' => 'Este e-mail já está em uso.',
                'password.required' => 'A senha é obrigatória.',
                'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
                'password.regex' => 'A senha deve conter pelo menos uma letra maiúscula, uma minúscula, um número e um símbolo.',
                'password.confirmed' => 'A confirmação de senha não confere.',
                'password_confirmation.required' => 'A confirmação de senha é obrigatória.',
                'role.required' => 'Selecione o perfil do usuário.',
                'role.in' => 'Perfil inválido.',
            ]);
            
            \Log::info('Dados validados, tentando criar usuário', ['validated_data' => array_diff_key($validated, ['password' => ''])]);
            
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);
            
            \Log::info('Usuário criado com sucesso', ['user_id' => $user->id]);
            
            // Redireciona para a tela de login após cadastro
            return redirect()->route('login')->with('success', 'Usuário cadastrado com sucesso! Faça login para continuar.');
        } catch (\Exception $e) {
            \Log::error('Erro ao criar usuário', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return back()->withInput()->with('error', 'Erro ao cadastrar usuário: ' . $e->getMessage());
        }
    }
}
