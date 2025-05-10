@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl py-10">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Detalhes da Ordem de Serviço</h1>
        <a href="{{ route('service_orders.index') }}" class="text-blue-700 hover:underline flex items-center">
            <i class="fas fa-arrow-left mr-1"></i> Voltar
        </a>
    </div>
    
    <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold text-blue-800">{{ $serviceOrder->title }}</h2>
                <p class="text-sm text-gray-500">OS #{{ $serviceOrder->id }}</p>
            </div>
            <span class="px-3 py-1 rounded-full text-sm font-semibold {{
                $serviceOrder->status === 'concluida' ? 'bg-green-100 text-green-800' :
                ($serviceOrder->status === 'em_andamento' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-200 text-gray-700')
            }}">
                {{ ucfirst(str_replace('_', ' ', $serviceOrder->status)) }}
            </span>
        </div>
        
        <div class="p-6 space-y-6">
            <div>
                <h3 class="text-lg font-semibold mb-2 text-gray-700">Descrição</h3>
                <div class="bg-gray-50 p-4 rounded border border-gray-200">
                    <p class="whitespace-pre-line">{{ $serviceOrder->description }}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-semibold mb-2 text-gray-700">Cliente</h3>
                    <div class="bg-gray-50 p-4 rounded border border-gray-200">
                        <p class="font-semibold">{{ $serviceOrder->client->name }}</p>
                        <p class="text-sm text-gray-600">{{ $serviceOrder->client->email }}</p>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-2 text-gray-700">Técnico</h3>
                    <div class="bg-gray-50 p-4 rounded border border-gray-200">
                        <p class="font-semibold">{{ $serviceOrder->technician->name }}</p>
                        <p class="text-sm text-gray-600">{{ $serviceOrder->technician->email }}</p>
                    </div>
                </div>
            </div>
            
            <div class="space-y-2">
                <div class="flex justify-between items-center text-sm text-gray-600">
                    <span>Criada em: {{ $serviceOrder->created_at->format('d/m/Y H:i') }}</span>
                    <span>Atualizada em: {{ $serviceOrder->updated_at->format('d/m/Y H:i') }}</span>
                </div>
                
                @can('update', $serviceOrder)
                <div class="flex justify-end pt-4">
                    <a href="{{ route('service_orders.edit', $serviceOrder) }}" 
                       class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded shadow">
                        <i class="fas fa-edit mr-1"></i> Editar Ordem
                    </a>
                </div>
                @endcan
            </div>
        </div>
    </div>
</div>
@endsection
