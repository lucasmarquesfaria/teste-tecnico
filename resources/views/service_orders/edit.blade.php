@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-lg py-10">
    <h1 class="text-2xl font-bold mb-6">Editar Ordem de Serviço</h1>
    <form method="POST" action="{{ route('service_orders.update', $serviceOrder) }}" class="space-y-5 bg-white shadow rounded-lg p-8">
        @csrf
        @method('PUT')
        <div>
            <label for="title" class="block font-semibold">Título</label>
            <input type="text" name="title" id="title" value="{{ old('title', $serviceOrder->title) }}" class="w-full border rounded p-2 @error('title') border-red-500 @enderror" required>
            @error('title')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label for="description" class="block font-semibold">Descrição</label>
            <textarea name="description" id="description" rows="4" class="w-full border rounded p-2 @error('description') border-red-500 @enderror" required>{{ old('description', $serviceOrder->description) }}</textarea>
            @error('description')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label for="status" class="block font-semibold">Status</label>
            <select name="status" id="status" class="w-full border rounded p-2 @error('status') border-red-500 @enderror" required>
                <option value="pendente" @if(old('status', $serviceOrder->status) == 'pendente') selected @endif>Pendente</option>
                <option value="em_andamento" @if(old('status', $serviceOrder->status) == 'em_andamento') selected @endif>Em andamento</option>
                <option value="concluida" @if(old('status', $serviceOrder->status) == 'concluida') selected @endif>Concluída</option>
            </select>
            @error('status')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded">Salvar</button>
        </div>
    </form>
</div>
@endsection
