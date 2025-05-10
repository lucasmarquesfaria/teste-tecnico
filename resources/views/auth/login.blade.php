@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen" style="background: #F6F0F0;">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8 border border-gray-200">
        <div class="flex flex-col items-center mb-6">
            <i class="fa-solid fa-user-shield text-5xl text-blue-700 mb-2"></i>
            <h2 class="text-2xl font-bold text-blue-800 mb-1">Bem-vindo ao Sistema de OS</h2>
            <p class="text-gray-500 text-sm">Acesse sua conta para gerenciar ordens de serviço</p>
        </div>
        @if(session('error'))
            <div class="bg-red-100 text-red-800 p-2 rounded mb-4 text-center">{{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <input type="email" name="email" id="email" placeholder="E-mail" value="{{ old('email') }}" class="w-full border border-gray-300 rounded p-2 focus:ring-2 focus:ring-blue-400 @error('email') border-red-500 @enderror" required autofocus>
            @error('email')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            <input type="password" name="password" id="password" placeholder="Senha" class="w-full border border-gray-300 rounded p-2 focus:ring-2 focus:ring-blue-400 @error('password') border-red-500 @enderror" required>
            @error('password')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
            <div class="flex items-center mb-2">
                <input type="checkbox" name="remember" id="remember" class="mr-2">
                <label for="remember" class="text-sm">Lembrar de mim</label>
            </div>
            <div class="flex items-center justify-between mb-2">
                <a href="#" class="text-blue-600 text-sm hover:underline">Esqueceu a senha?</a>
                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white font-bold px-6 py-2 rounded shadow">Entrar</button>
            </div>
        </form>
        <div class="text-center mt-4">
            <a href="{{ route('users.create') }}" class="text-blue-700 hover:underline font-semibold">Registre-se</a>
        </div>
    </div>
</div>
@endsection
