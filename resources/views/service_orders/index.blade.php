@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-3xl py-10">
    <h1 class="text-2xl font-bold mb-6">Minhas Ordens de Serviço</h1>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    <div class="mb-6 flex justify-end">
        @if(auth()->user()->role === 'technician')
            <a href="{{ route('service_orders.create') }}" class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded">Nova Ordem de Serviço</a>
        @endif
    </div>
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">Título</th>
                    <th class="px-4 py-2">Status</th>
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
                        <td class="px-4 py-2">{{ $order->client->name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $order->technician->name ?? '-' }}</td>
                        <td class="px-4 py-2">
                            @can('update', $order)
                                <a href="{{ route('service_orders.edit', $order) }}" class="text-blue-700 hover:underline">Editar</a>
                            @endcan
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
