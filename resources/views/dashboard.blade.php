@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-6xl py-8">
    <h1 class="text-3xl font-bold mb-6">Dashboard</h1>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Total de Ordens</p>
                    <h3 class="text-2xl font-bold">{{ $stats['total'] }}</h3>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Pendentes</p>
                    <h3 class="text-2xl font-bold">{{ $stats['pending'] }}</h3>
                </div>
                <div class="bg-gray-100 p-3 rounded-full">
                    <i class="fas fa-clock text-gray-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Em Andamento</p>
                    <h3 class="text-2xl font-bold">{{ $stats['in_progress'] }}</h3>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-tools text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Concluídas</p>
                    <h3 class="text-2xl font-bold">{{ $stats['completed'] }}</h3>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">        <!-- Dashboard Analítico Card - Apenas para técnicos -->
        @if(auth()->user()->role === 'technician')
        <div class="md:col-span-3 mb-6">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-lg shadow p-5 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold mb-2">Dashboard Analítico</h2>
                        <p class="text-blue-100 mb-4">Visualize estatísticas detalhadas e gráficos das suas ordens de serviço</p>
                        <a href="{{ route('analytics') }}" class="bg-white text-blue-600 hover:bg-blue-100 font-bold py-2 px-4 rounded inline-flex items-center">
                            <i class="fas fa-chart-bar mr-2"></i> Ver Analytics
                        </a>
                    </div>
                    <div class="hidden md:block">
                        <i class="fas fa-chart-line text-6xl opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if(auth()->user()->role === 'technician')
        <div class="md:col-span-3 mb-6">
            <div class="bg-gradient-to-r from-green-500 to-blue-600 rounded-lg shadow-lg p-7 text-white flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <h2 class="text-2xl font-extrabold mb-2 flex items-center">
                        <i class="fas fa-file-alt mr-3 text-3xl"></i> Relatórios de Ordens de Serviço
                    </h2>
                    <p class="text-blue-100 mb-4 max-w-lg">Gere relatórios profissionais em PDF ou Excel das suas ordens de serviço, filtrando por período, status e técnico. Ideal para prestação de contas, auditoria e acompanhamento de resultados.</p>
                    <a href="{{ route('reports.form') }}" class="inline-flex items-center bg-white text-green-700 hover:bg-green-100 font-bold py-2 px-6 rounded shadow transition-all duration-200">
                        <i class="fas fa-file-download mr-2"></i> Gerar Relatório
                    </a>
                </div>
                <div class="hidden md:block">
                    <i class="fas fa-file-excel text-7xl opacity-60 mr-4"></i>
                    <i class="fas fa-file-pdf text-7xl opacity-60"></i>
                </div>
            </div>
        </div>
        @endif

        <div class="md:col-span-2">
            <div class="bg-white rounded-lg shadow p-5">
                <h2 class="text-xl font-bold mb-4">Ordens Recentes</h2>
                
                @if($recentOrders->isEmpty())
                    <p class="text-gray-500">Nenhuma ordem de serviço registrada.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-2 text-left">ID</th>
                                    <th class="px-4 py-2 text-left">Título</th>
                                    <th class="px-4 py-2 text-left">Status</th>
                                    <th class="px-4 py-2 text-left">Data</th>
                                    <th class="px-4 py-2 text-left">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                    <tr class="border-b">
                                        <td class="px-4 py-2">#{{ $order->id }}</td>
                                        <td class="px-4 py-2">{{ $order->title }}</td>
                                        <td class="px-4 py-2">
                                            <span class="px-2 py-1 rounded text-xs {{
                                                $order->status === 'concluida' ? 'bg-green-100 text-green-800' :
                                                ($order->status === 'em_andamento' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-200 text-gray-700')
                                            }}">
                                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2">{{ $order->formatted_created_at }}</td>
                                        <td class="px-4 py-2">
                                            <a href="{{ route('service_orders.show', $order) }}" class="text-blue-700 hover:underline">
                                                <i class="fas fa-eye mr-1"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 text-right">
                        <a href="{{ route('service_orders.index') }}" class="text-blue-700 hover:underline">Ver todas</a>
                    </div>
                @endif
            </div>
        </div>
        
        <div>
            @if(auth()->user()->role === 'technician')
                <div class="bg-white rounded-lg shadow p-5 mb-6">
                    <h2 class="text-xl font-bold mb-4">Clientes Recentes</h2>
                    
                    @if(isset($recentClients) && $recentClients->isNotEmpty())
                        <ul class="space-y-3">
                            @foreach($recentClients as $client)
                                <li class="flex items-center p-2 hover:bg-gray-50 rounded">
                                    <div class="bg-blue-100 p-2 rounded-full mr-3">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold">{{ $client->name }}</p>
                                        <p class="text-gray-500 text-sm">{{ $client->email }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">Nenhum cliente recente.</p>
                    @endif
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-5 mb-6">
                    <h2 class="text-xl font-bold mb-4">Técnicos</h2>
                    
                    @if(isset($technicians) && $technicians->isNotEmpty())
                        <ul class="space-y-3">
                            @foreach($technicians as $tech)
                                <li class="flex items-center p-2 hover:bg-gray-50 rounded">
                                    <div class="bg-green-100 p-2 rounded-full mr-3">
                                        <i class="fas fa-user-cog text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-semibold">{{ $tech->name }}</p>
                                        <p class="text-gray-500 text-sm">{{ $tech->email }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">Nenhum técnico registrado.</p>
                    @endif
                </div>
            @endif
            
            @if(auth()->user()->role === 'technician')
                <div class="text-center">
                    <a href="{{ route('service_orders.create') }}" 
                       class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded shadow inline-block">
                        <i class="fas fa-plus mr-1"></i> Nova Ordem de Serviço
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
