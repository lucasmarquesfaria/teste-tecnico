<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de OS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background:rgb(231, 231, 231) !important; }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    @auth
    <header class="bg-blue-800 text-white shadow-md">
        <nav class="container mx-auto py-3 px-4 flex justify-between items-center">
            <div class="flex items-center space-x-8">
                <div class="font-bold text-lg">
                    <i class="fas fa-tools mr-2"></i>
                    <span>Sistema de OS</span>
                </div>                <div class="hidden md:flex space-x-6">
                    <a href="{{ route('dashboard') }}" class="hover:text-blue-200 transition">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                    <a href="{{ route('service_orders.index') }}" class="hover:text-blue-200 transition">
                        <i class="fas fa-clipboard-list mr-1"></i> Ordens de Serviço
                    </a>                    @if(auth()->user()->role === 'technician')
                    <a href="{{ route('service_orders.create') }}" class="hover:text-blue-200 transition">
                        <i class="fas fa-plus-circle mr-1"></i> Nova OS
                    </a>
                    <a href="{{ route('service_orders.fix_dates') }}" class="hover:text-blue-200 transition">
                        <i class="fas fa-wrench mr-1"></i> Corrigir Datas
                    </a>
                    @endif
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-sm">
                    <span class="hidden md:inline mr-2">Olá,</span>
                    <span class="font-semibold">{{ auth()->user()->name }}</span>
                    <span class="ml-2 px-2 py-1 bg-blue-900 rounded-full text-xs">
                        {{ auth()->user()->role === 'technician' ? 'Técnico' : 'Cliente' }}
                    </span>
                </div>                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-white hover:text-blue-200">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="hidden md:inline">Sair</span>
                    </button>
                </form>
            </div>
        </nav>
    </header>
    @endauth

    <main class="flex-grow">
        @yield('content')
    </main>
</body>
</html>
