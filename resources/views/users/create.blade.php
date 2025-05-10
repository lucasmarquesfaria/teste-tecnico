@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen" style="background: #F6F0F0;">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8 border border-gray-200">
        <div class="flex flex-col items-center mb-6">
            <i class="fa-solid fa-user-plus text-5xl text-blue-700 mb-2"></i>
            <h2 class="text-2xl font-bold text-blue-800 mb-1">Criar Conta</h2>
            <p class="text-gray-500 text-sm">Preencha os dados para se cadastrar</p>
        </div>        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-2 rounded mb-4 text-center">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-2 rounded mb-4 text-center">{{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
            @csrf
            <input type="text" name="name" id="name" placeholder="Nome completo" value="{{ old('name') }}" class="w-full border border-gray-300 rounded p-2 focus:ring-2 focus:ring-blue-400 @error('name') border-red-500 @enderror" required>
            @error('name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            <input type="email" name="email" id="email" placeholder="E-mail" value="{{ old('email') }}" class="w-full border border-gray-300 rounded p-2 focus:ring-2 focus:ring-blue-400 @error('email') border-red-500 @enderror" required>
            @error('email')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            <input type="password" name="password" id="password" placeholder="Senha" class="w-full border border-gray-300 rounded p-2 focus:ring-2 focus:ring-blue-400 @error('password') border-red-500 @enderror" required>
            @error('password')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirme a senha" class="w-full border border-gray-300 rounded p-2 focus:ring-2 focus:ring-blue-400" required>
            <div>
                <label for="role" class="block font-semibold text-gray-700 mb-1">Perfil</label>
                <select name="role" id="role" class="w-full border border-gray-300 rounded p-2 focus:ring-2 focus:ring-blue-400 @error('role') border-red-500 @enderror" required>
                    <option value="">Selecione...</option>
                    <option value="client" @if(old('role')=='client') selected @endif>Cliente</option>
                    <option value="technician" @if(old('role')=='technician') selected @endif>Técnico</option>
                </select>
                @error('role')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            </div>
            <div class="flex items-center justify-between mt-4">
                <a href="/" class="text-blue-600 text-sm hover:underline">Já tem conta? Entrar</a>
                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold px-6 py-2 rounded shadow">Cadastrar</button>
            </div>
        </form>
    </div>
</div>
@endsection
