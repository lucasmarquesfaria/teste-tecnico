@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-5xl py-10">
    <h1 class="text-2xl font-bold mb-6">Corrigir Ordenação das Ordens de Serviço</h1>
    
    <div class="bg-white shadow rounded-lg p-4 mb-6">
        <p class="mb-4">Esta página permite verificar e corrigir a ordenação das ordens de serviço por data de criação.</p>
        
        <form method="post" action="{{ route('service_orders.apply_fix') }}" class="mb-4">
            @csrf
            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded">
                <i class="fas fa-wrench mr-1"></i> Aplicar Correção
            </button>
        </form>
    </div>
    
    <div class="bg-white shadow rounded-lg overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2">ID</th>
                    <th class="px-4 py-2">Título</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Data de Criação</th>
                    <th class="px-4 py-2">Cliente</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
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
                        <td class="px-4 py-2">{{ $order->client->name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4">Nenhuma ordem encontrada.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
