@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-lg py-10">
    <h1 class="text-2xl font-bold mb-6">Nova Ordem de Serviço</h1>
    <form method="POST" action="{{ route('service_orders.store') }}" class="space-y-5 bg-white shadow rounded-lg p-8">
        @csrf
        <div>
            <label for="title" class="block font-semibold">Título</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" class="w-full border rounded p-2 @error('title') border-red-500 @enderror" required>
            @error('title')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label for="description" class="block font-semibold">Descrição</label>
            <textarea name="description" id="description" rows="4" class="w-full border rounded p-2 @error('description') border-red-500 @enderror" required>{{ old('description') }}</textarea>
            @error('description')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div>
            <label for="client_id" class="block font-semibold">Cliente</label>
            <select name="client_id" id="client_id" class="w-full border rounded p-2 @error('client_id') border-red-500 @enderror" required>
                <option value="">Selecione um cliente</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}" @if(old('client_id') == $client->id) selected @endif>{{ $client->name }} ({{ $client->email }})</option>
                @endforeach
            </select>
            @error('client_id')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
        </div>
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded">Criar OS</button>
        </div>
    </form>
</div>
@endsection
