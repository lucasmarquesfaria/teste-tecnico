@extends('layouts.app')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center bg-gray-100 py-8 px-2">
    <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 w-full max-w-xl p-4 sm:p-6 md:p-10 lg:p-12">
        <div class="flex flex-col sm:flex-row items-center mb-8 gap-4">
            <div class="bg-blue-100 p-4 rounded-full flex-shrink-0">
                <i class="fas fa-file-alt text-blue-600 text-3xl"></i>
            </div>
            <div class="text-center sm:text-left">
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 mb-1">Relatórios de Ordens de Serviço</h1>
                <p class="text-gray-500 text-sm md:text-base">Gere relatórios profissionais em PDF ou Excel, filtrando por período, status e técnico. Ideal para auditoria, prestação de contas e acompanhamento de resultados.</p>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <form method="POST" action="{{ route('reports.pdf') }}" target="_blank" class="col-span-1 flex flex-col gap-4">
                @csrf
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Status:</label>
                    <select name="status" class="form-select w-full rounded border-gray-300 text-sm py-2">
                        <option value="">Todos</option>
                        <option value="pendente">Pendente</option>
                        <option value="em_andamento">Em Andamento</option>
                        <option value="concluida">Concluída</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 font-semibold mb-1">Técnico:</label>
                    <select name="technician_id" class="form-select w-full rounded border-gray-300 text-sm py-2">
                        <option value="">Todos</option>
                        @foreach($tecnicos as $tecnico)
                            <option value="{{ $tecnico->id }}">{{ $tecnico->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="flex-1">
                        <label class="block text-gray-700 font-semibold mb-1">Data início:</label>
                        <input type="date" name="date_start" class="form-input w-full rounded border-gray-300 text-sm py-2">
                    </div>
                    <div class="flex-1">
                        <label class="block text-gray-700 font-semibold mb-1">Data fim:</label>
                        <input type="date" name="date_end" class="form-input w-full rounded border-gray-300 text-sm py-2">
                    </div>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded shadow flex items-center justify-center transition-all duration-200 text-base mt-2 w-full">
                    <i class="fas fa-file-pdf mr-2 text-lg"></i> Gerar PDF
                </button>
            </form>
            <form method="POST" action="{{ route('reports.excel') }}" target="_blank" class="col-span-1 flex flex-col justify-end gap-4 mt-4 md:mt-0">
                @csrf
                <input type="hidden" name="status" value="">
                <input type="hidden" name="technician_id" value="">
                <input type="hidden" name="date_start" value="">
                <input type="hidden" name="date_end" value="">
                <div class="h-10"></div>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded shadow flex items-center justify-center transition-all duration-200 text-base mt-2 w-full">
                    <i class="fas fa-file-excel mr-2 text-lg"></i> Gerar Excel
                </button>
            </form>
        </div>
        <div class="mt-8 text-gray-500 text-xs md:text-sm border-t pt-4">
            <ul class="list-disc ml-6 space-y-1">
                <li>Os relatórios PDF e Excel incluem todos os campos principais da OS.</li>
                <li>Você pode filtrar por status, técnico responsável e intervalo de datas.</li>
                <li>Ideal para prestação de contas, auditoria, acompanhamento de produtividade e exportação de dados.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
