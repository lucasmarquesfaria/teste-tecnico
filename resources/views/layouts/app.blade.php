<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de OS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { background:rgb(231, 231, 231) !important; }
        header { box-shadow: 0 2px 8px 0 rgba(30, 64, 175, 0.10); }
        @media (max-width: 768px) {
            header nav { flex-direction: column; align-items: stretch; }
            header .space-x-8 { flex-direction: column; gap: 0.5rem; }
            header .ml-8 { margin-left: 0 !important; }
            header .hidden.md\:flex { display: flex !important; flex-direction: column; gap: 0.5rem; }
            header .w-full.md\:w-auto { width: 100% !important; }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    @auth
    <header class="bg-gradient-to-r from-blue-900 via-blue-800 to-blue-700 text-white shadow-lg sticky top-0 z-30">
        <nav class="container mx-auto py-3 px-4 flex flex-col md:flex-row justify-between items-center">
            <div class="flex items-center space-x-8 w-full md:w-auto mb-2 md:mb-0">
                <div class="font-extrabold text-2xl tracking-tight flex items-center">
                    <i class="fas fa-tools mr-2 text-yellow-400 text-3xl"></i>
                    <span class="drop-shadow-lg">Sistema de <span class="text-yellow-300">OS</span></span>
                </div>
                <div class="hidden md:flex space-x-6 ml-8">
                    <a href="{{ route('dashboard') }}" class="hover:text-yellow-300 font-semibold transition flex items-center">
                        <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                    </a>
                    @if(auth()->user()->role === 'technician')
                    <a href="{{ route('analytics') }}" class="hover:text-yellow-300 font-semibold transition flex items-center">
                        <i class="fas fa-chart-bar mr-1"></i> Analytics
                    </a>
                    <a href="{{ route('reports.form') }}" class="hover:text-yellow-300 font-semibold transition flex items-center">
                        <i class="fas fa-file-alt mr-1"></i> Relatórios
                    </a>
                    @endif
                    <a href="{{ route('service_orders.index') }}" class="hover:text-yellow-300 font-semibold transition flex items-center">
                        <i class="fas fa-clipboard-list mr-1"></i> Ordens de Serviço
                    </a>
                    @if(auth()->user()->role === 'technician')
                    <a href="{{ route('service_orders.create') }}" class="hover:text-yellow-300 font-semibold transition flex items-center">
                        <i class="fas fa-plus-circle mr-1"></i> Nova OS
                    </a>
                    <a href="{{ route('service_orders.fix_dates') }}" class="hover:text-yellow-300 font-semibold transition flex items-center">
                        <i class="fas fa-wrench mr-1"></i> Corrigir Datas
                    </a>
                    @endif
                </div>
            </div>
            <div class="flex items-center space-x-4 w-full md:w-auto justify-end mt-2 md:mt-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-yellow-400 text-blue-900 flex items-center justify-center font-bold text-lg shadow border-2 border-white">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="text-sm text-right flex flex-col md:flex-row md:items-center gap-1 md:gap-2">
                        <span class="hidden md:inline mr-2">Olá,</span>
                        <span class="font-bold text-lg bg-white text-blue-900 px-2 py-1 rounded shadow-sm">{{ auth()->user()->name }}</span>
                        <span class="ml-2 px-2 py-1 bg-yellow-400 text-blue-900 rounded-full text-xs font-bold shadow">
                            {{ auth()->user()->role === 'technician' ? 'Técnico' : 'Cliente' }}
                        </span>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-white hover:text-yellow-300 font-semibold transition flex items-center group focus:outline-none focus:ring-2 focus:ring-yellow-300">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="hidden md:inline ml-1">Sair</span>
                        <span class="ml-2 hidden group-focus:inline animate-spin"><i class="fas fa-spinner"></i></span>
                    </button>
                </form>
            </div>
        </nav>
        <!-- Breadcrumbs -->
        <div class="container mx-auto px-4 mt-2 mb-0">
            @hasSection('breadcrumbs')
                <nav class="text-xs text-gray-200 mb-2" aria-label="breadcrumb">
                    @yield('breadcrumbs')
                </nav>
            @endif
        </div>
    </header>
    @endauth
    <!-- Toast de notificação -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded shadow-lg z-50 animate-fade-in" style="display: none;" x-cloak>
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="fixed top-4 right-4 bg-red-600 text-white px-4 py-2 rounded shadow-lg z-50 animate-fade-in" style="display: none;" x-cloak>
            <i class="fas fa-times-circle mr-2"></i> {{ session('error') }}
        </div>
    @endif
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Scripts -->
    @yield('scripts')
    <!-- Adicione Alpine.js para o toast -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        [x-cloak] { display: none !important; }
        .animate-fade-in { animation: fadeIn 0.5s; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-10px);} to { opacity: 1; transform: none; } }
    </style>
</body>
</html>
