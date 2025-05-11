<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    // A verificação de acesso é feita pelo middleware CheckTechnician
       public function index()
    {
        try {
            $user = Auth::user();
            
            // Garantir que temos um usuário autenticado e que é um técnico
            if (!$user || $user->role !== 'technician') {
                return redirect()->route('dashboard')
                    ->with('error', 'Acesso restrito. Apenas a área técnica pode visualizar o dashboard analítico.');
            }
            
            $statusData = $this->getStatusChartData($user);
            $completionTimeData = $this->getCompletionTimeData($user);
            $ordersPerMonthData = $this->getOrdersPerMonthData($user);
            $performanceData = $this->getTechnicianPerformanceData($user->id);
            $stats = $this->getGeneralStats($user);
                          
            // Garantir que todos os dados são serializáveis para JSON
            return view('analytics.index', [
                'statusData' => json_encode($statusData, JSON_NUMERIC_CHECK),
                'completionTimeData' => json_encode($completionTimeData, JSON_NUMERIC_CHECK),
                'ordersPerMonthData' => json_encode($ordersPerMonthData, JSON_NUMERIC_CHECK),
                'performanceData' => json_encode($performanceData, JSON_NUMERIC_CHECK),
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao gerar dashboard analítico: ' . $e->getMessage());
            
            return view('analytics.index', [
                'error' => 'Ocorreu um erro ao processar os dados analíticos. Por favor, tente novamente mais tarde.'
            ]);
        }
    }    private function getStatusChartData($user)
    {
        try {
            $query = ServiceOrder::query()->where('technician_id', $user->id);
            
            $statusCounts = $query->select('status', DB::raw('count(*) as total'))
                                ->groupBy('status')
                                ->pluck('total', 'status')
                                ->toArray();
                                
            $statuses = ['pendente', 'em_andamento', 'concluida'];
            $labels = ['Pendente', 'Em Andamento', 'Concluída'];
            $colors = ['#9CA3AF', '#FBBF24', '#10B981'];
            
            $data = [];
            foreach ($statuses as $index => $status) {
                $count = isset($statusCounts[$status]) ? (int)$statusCounts[$status] : 0;
                
                $data[] = [
                    'status' => $labels[$index],
                    'count' => $count,
                    'color' => $colors[$index]
                ];
            }
            
            return $data;
            
        } catch (\Exception $e) {
            Log::error('Erro ao gerar dados de status: ' . $e->getMessage());
            
            // Retornar dados vazios mas válidos
            $statuses = ['pendente', 'em_andamento', 'concluida'];
            $labels = ['Pendente', 'Em Andamento', 'Concluída'];
            $colors = ['#9CA3AF', '#FBBF24', '#10B981'];
            
            $data = [];
            foreach ($statuses as $index => $status) {
                $data[] = [
                    'status' => $labels[$index],
                    'count' => 0,
                    'color' => $colors[$index]
                ];
            }
            
            return $data;
        }
    }
    
    /**
     * Obtém dados para o gráfico de tempo médio de conclusão
     *
     * @param \App\Models\User $user
     * @return array
     */    private function getCompletionTimeData($user)
    {
        $query = ServiceOrder::where('status', 'concluida')
                ->where('technician_id', $user->id);
          $stats = $query->select([
            DB::raw('COUNT(*) as count'),
            DB::raw('AVG(TIMESTAMPDIFF(DAY, created_at, updated_at)) as avg_time'),
            DB::raw('MIN(TIMESTAMPDIFF(DAY, created_at, updated_at)) as min_time'),
            DB::raw('MAX(TIMESTAMPDIFF(DAY, created_at, updated_at)) as max_time')
        ])->first();
        
        if (!$stats || $stats->count == 0) {
            return [
                'avg' => 0,
                'min' => 0,
                'max' => 0,
                'count' => 0
            ];
        }
        
        return [
            'avg' => $stats->avg_time ? round($stats->avg_time, 1) : 0,
            'min' => $stats->min_time ? (int)$stats->min_time : 0,
            'max' => $stats->max_time ? (int)$stats->max_time : 0,
            'count' => (int)$stats->count
        ];
    }    private function getOrdersPerMonthData($user)
    {
        try {
            $months = [
                'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun',
                'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'
            ];
            
            // Inicializar array com zeros para todos os meses
            $monthCounts = array_fill(0, 12, 0);
            
            // Construir a query para obter os dados do banco
            $query = ServiceOrder::select(
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('COUNT(*) as count')
                )
                ->whereYear('created_at', date('Y'))
                ->where('technician_id', $user->id);
            
            $results = $query->groupBy('month')
                    ->orderBy('month')
                    ->get();
                    
            // Preencher os dados com os resultados da consulta
            foreach ($results as $result) {
                // Validar se o mês está no intervalo válido (1-12)
                if (isset($result->month) && $result->month >= 1 && $result->month <= 12) {
                    // Mês é baseado em 1, então subtraímos 1 para corresponder ao índice do array (0-11)
                    $monthIndex = $result->month - 1;
                    $monthCounts[$monthIndex] = (int)$result->count;
                }
            }
            
            // Converter para valores inteiros
            $monthCounts = array_map(function($value) {
                return (int)$value;
            }, $monthCounts);
            
            return [
                'labels' => $months,
                'data' => $monthCounts
            ];
            
        } catch (\Exception $e) {
            Log::error('Erro ao gerar dados de ordens por mês: ' . $e->getMessage());
            // Retornar um conjunto de dados vazio mas válido
            return [
                'labels' => $months,
                'data' => array_fill(0, 12, 0)
            ];
        }
    }    private function getTechnicianPerformanceData($technicianId)
    {
        $labels = [];
        $completionData = [];
        $countData = [];

        for ($i = 8; $i >= 1; $i--) {
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
            $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();
            $labels[] = "Semana " . $weekStart->format('d/m');
            
            try {
                $weekStats = ServiceOrder::where('technician_id', $technicianId)
                    ->where('status', 'concluida')
                    ->whereBetween('updated_at', [$weekStart, $weekEnd])
                    ->select([
                        DB::raw('COUNT(*) as completed_count'),
                        DB::raw('AVG(TIMESTAMPDIFF(DAY, created_at, updated_at)) as avg_completion_time')
                    ])
                    ->first();
                
                $completionData[] = $weekStats && $weekStats->avg_completion_time ? round($weekStats->avg_completion_time, 1) : 0;
                $countData[] = $weekStats && isset($weekStats->completed_count) ? (int)$weekStats->completed_count : 0;
                
            } catch (\Exception $e) {
                // Registrar erro mas continuar a execução
                Log::error('Erro ao calcular performance semanal: ' . $e->getMessage());
                $completionData[] = 0;
                $countData[] = 0;
            }
        }
        
        // Verificar se há pelo menos alguns dados válidos
        $hasData = false;
        foreach ($countData as $count) {
            if ($count > 0) {
                $hasData = true;
                break;
            }
        }
        
        // Mesmo sem dados, retornamos um array vazio em vez de null
        // para evitar problemas com json_encode na view
        if (!$hasData) {
            return [
                'labels' => $labels,
                'completion_time' => array_fill(0, count($labels), 0),
                'completed_count' => array_fill(0, count($labels), 0)
            ];
        }
        
        return [
            'labels' => $labels,
            'completion_time' => $completionData,
            'completed_count' => $countData
        ];
    }    private function getGeneralStats($user)
    {
        $query = ServiceOrder::query()->where('technician_id', $user->id);
        
        $total = $query->count();
        
        $completedQuery = clone $query;
        $completed = $completedQuery->where('status', 'concluida')->count();
        
        $completionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
        
        // Buscar o pedido mais antigo DESTE técnico para cálculos de estatísticas
        $oldestOrderQuery = clone $query;
        $oldestOrder = $oldestOrderQuery->orderBy('created_at', 'asc')->first();
        $monthsActive = 1;
          
        if ($oldestOrder) {
            $firstDate = Carbon::parse($oldestOrder->created_at);
            $monthsActive = $firstDate->diffInMonths(Carbon::now()) + 1;
            if ($monthsActive < 1) {
                $monthsActive = 1;
            }
        }
        
        $avgOrdersPerMonth = $total > 0 ? round($total / $monthsActive, 1) : 0;
        
        $lastMonthQuery = clone $query;
        $ordersLastMonth = $lastMonthQuery->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();
            
        $currentMonthQuery = clone $query;
        $ordersThisMonth = $currentMonthQuery->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
            
        $growth = $ordersLastMonth > 0 
            ? round((($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100, 1)
            : ($ordersThisMonth > 0 ? 100 : 0);
        
        return [
            'total_orders' => $total,
            'completed_orders' => $completed,
            'completion_rate' => $completionRate,
            'avg_orders_per_month' => $avgOrdersPerMonth,
            'growth_percentage' => $growth,
            'orders_this_month' => $ordersThisMonth
        ];
    }
}
