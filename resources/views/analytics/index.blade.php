@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-7xl py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold">Dashboard Analítico</h1>
        <a href="{{ route('dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Voltar ao Dashboard
        </a>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Total de Ordens</p>
                    <h3 class="text-2xl font-bold">{{ $stats['total_orders'] }}</h3>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Taxa de Conclusão</p>
                    <h3 class="text-2xl font-bold">{{ $stats['completion_rate'] }}%</h3>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Média Mensal</p>
                    <h3 class="text-2xl font-bold">{{ $stats['avg_orders_per_month'] }}</h3>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-calendar-alt text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-gray-500 text-sm">Crescimento</p>
                    <h3 class="text-2xl font-bold flex items-center">
                        {{ $stats['growth_percentage'] }}%
                        @if($stats['growth_percentage'] > 0)
                            <i class="fas fa-arrow-up ml-2 text-green-500 text-sm"></i>
                        @elseif($stats['growth_percentage'] < 0)
                            <i class="fas fa-arrow-down ml-2 text-red-500 text-sm"></i>
                        @endif
                    </h3>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-chart-line text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Gráfico de Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Distribuição de Status</h2>
            <div class="w-full h-64">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
        
        <!-- Gráfico de Ordens por Mês -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Ordens por Mês ({{ date('Y') }})</h2>
            <div class="w-full h-64">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Tempo Médio de Conclusão -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Tempo Médio de Conclusão</h2>
            @if(!empty($completionTimeData))
                <div class="flex items-center justify-center h-64">
                    <div class="text-center">
                        <div class="text-5xl font-bold text-blue-600 mb-2">{{ json_decode($completionTimeData)->avg }} dias</div>
                        <p class="text-gray-500">Baseado em {{ json_decode($completionTimeData)->count }} ordens concluídas</p>
                        <div class="mt-4 grid grid-cols-2 gap-4">
                            <div class="border rounded p-3">
                                <p class="text-sm text-gray-500">Mínimo</p>
                                <p class="font-bold text-lg">{{ json_decode($completionTimeData)->min }} dias</p>
                            </div>
                            <div class="border rounded p-3">
                                <p class="text-sm text-gray-500">Máximo</p>
                                <p class="font-bold text-lg">{{ json_decode($completionTimeData)->max }} dias</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex items-center justify-center h-64">
                    <p class="text-gray-500">Nenhuma ordem concluída encontrada</p>
                </div>
            @endif
        </div>
        
        <!-- Desempenho (apenas para técnicos) -->
        @if(auth()->user()->role === 'technician' && $performanceData)
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Desempenho Semanal</h2>
            <div class="w-full h-64">
                <canvas id="performanceChart"></canvas>
            </div>
        </div>
        @else
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Estatísticas Adicionais</h2>
            <div class="h-64 flex flex-col justify-center">
                <div class="grid grid-cols-2 gap-4">
                    <div class="border rounded-lg p-4 text-center">
                        <p class="text-gray-500 mb-1">Ordens Concluídas</p>
                        <p class="text-2xl font-bold">{{ $stats['completed_orders'] }}</p>
                    </div>
                    <div class="border rounded-lg p-4 text-center">
                        <p class="text-gray-500 mb-1">Ordens Este Mês</p>
                        <p class="text-2xl font-bold">{{ $stats['orders_this_month'] }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dados do gráfico de status
        const statusData = @json(json_decode($statusData));
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusData.map(item => item.status),
                datasets: [{
                    data: statusData.map(item => item.count),
                    backgroundColor: statusData.map(item => item.color),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right'
                    }
                }
            }
        });
        
        // Dados do gráfico mensal
        const monthlyData = @json(json_decode($ordersPerMonthData));
        const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: monthlyData.labels,
                datasets: [{
                    label: 'Ordens de Serviço',
                    data: monthlyData.data,
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        
        // Gráfico de desempenho (apenas para técnicos)
        @if(auth()->user()->role === 'technician' && $performanceData)
        const performanceData = @json(json_decode($performanceData));
        const performanceCtx = document.getElementById('performanceChart').getContext('2d');
        new Chart(performanceCtx, {
            type: 'line',
            data: {
                labels: performanceData.labels,
                datasets: [
                    {
                        label: 'Tempo Médio (dias)',
                        data: performanceData.completion_time,
                        yAxisID: 'y',
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Ordens Concluídas',
                        data: performanceData.completed_count,
                        yAxisID: 'y1',
                        backgroundColor: 'rgba(16, 185, 129, 0.2)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        type: 'bar'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Tempo Médio (dias)'
                        },
                        min: 0
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Ordens Concluídas'
                        },
                        min: 0,
                        grid: {
                            drawOnChartArea: false
                        },
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });
        @endif
    });
</script>
@endsection
