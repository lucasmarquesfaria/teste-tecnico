@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-5xl py-10">
    <h1 class="text-2xl font-bold mb-6">Minhas Ordens de Serviço</h1>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <form method="get" action="{{ route('service_orders.index') }}" class="space-y-4">
            <h2 class="text-lg font-semibold mb-2">Filtros</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="status" class="block text-sm mb-1">Status</label>
                    <select name="status" id="status" class="w-full border rounded p-2">
                        <option value="">Todos</option>
                        <option value="pendente" {{ ($filters['status'] ?? '') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="em_andamento" {{ ($filters['status'] ?? '') == 'em_andamento' ? 'selected' : '' }}>Em andamento</option>
                        <option value="concluida" {{ ($filters['status'] ?? '') == 'concluida' ? 'selected' : '' }}>Concluída</option>
                    </select>
                </div>
                <div>
                    <label for="date_from" class="block text-sm mb-1">Data inicial</label>
                    <input type="date" name="date_from" id="date_from" value="{{ $filters['date_from'] ?? '' }}" class="w-full border rounded p-2">
                </div>
                <div>
                    <label for="date_to" class="block text-sm mb-1">Data final</label>
                    <input type="date" name="date_to" id="date_to" value="{{ $filters['date_to'] ?? '' }}" class="w-full border rounded p-2">
                </div>
                <div>
                    <label for="search" class="block text-sm mb-1">Pesquisar</label>
                    <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}" placeholder="Título ou descrição" class="w-full border rounded p-2">
                </div>
            </div>
            <div class="flex justify-between">
                <a href="{{ route('service_orders.index') }}" class="px-4 py-2 border rounded hover:bg-gray-50"><i class="fas fa-times mr-1"></i> Limpar</a>
                <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded"><i class="fas fa-filter mr-1"></i> Filtrar</button>
            </div>
        </form>
    </div>
    
    <div class="flex justify-between items-center mb-4">
        <div>
            <span class="text-gray-600">{{ $orders->count() }} resultado(s) encontrado(s)</span>
        </div>
        <div>
            @if(auth()->user()->role === 'technician')
                <a href="{{ route('service_orders.create') }}" class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded">
                    <i class="fas fa-plus mr-1"></i> Nova Ordem de Serviço
                </a>
            @endif
        </div>
    </div>
    <div class="bg-white shadow rounded-lg overflow-x-auto">        <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Título</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Data de Criação</th>
                    <th class="px-4 py-2">Cliente</th>
                    <th class="px-4 py-2">Técnico</th>
                    <th class="px-4 py-2">Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr class="border-b">
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
                        <td class="px-4 py-2">{{ $order->client->name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $order->technician->name ?? '-' }}</td>                        <td class="px-4 py-2">
                            <div class="flex space-x-3">
                                <a href="{{ route('service_orders.show', $order) }}" class="text-green-700 hover:underline">
                                    <i class="fas fa-eye mr-1"></i> Ver
                                </a>
                                @can('update', $order)
                                    <a href="{{ route('service_orders.edit', $order) }}" class="text-blue-700 hover:underline">
                                        <i class="fas fa-edit mr-1"></i> Editar
                                    </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4">Nenhuma ordem encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
