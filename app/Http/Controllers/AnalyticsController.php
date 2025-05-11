<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Exibe o dashboard analítico com gráficos
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $analytics = [];
        
        // Dados para o gráfico de status das ordens
        $statusData = $this->getStatusChartData($user);
        
        // Dados para o gráfico de tempo médio de conclusão
        $completionTimeData = $this->getCompletionTimeData($user);
        
        // Dados para o gráfico de ordens por mês
        $ordersPerMonthData = $this->getOrdersPerMonthData($user);
        
        // Dados para o gráfico de desempenho (apenas para técnicos)
        $performanceData = $user->role === 'technician' ? $this->getTechnicianPerformanceData($user->id) : null;
        
        // Estatísticas gerais
        $stats = $this->getGeneralStats($user);
        
        return view('analytics.index', [
            'statusData' => json_encode($statusData),
            'completionTimeData' => json_encode($completionTimeData),
            'ordersPerMonthData' => json_encode($ordersPerMonthData),
            'performanceData' => $performanceData ? json_encode($performanceData) : null,
            'stats' => $stats
        ]);
    }
    
    /**
     * Obtém dados para o gráfico de status das ordens
     *
     * @param \App\Models\User $user
     * @return array
     */
    private function getStatusChartData($user)
    {
        $query = ServiceOrder::query();
        
        if ($user->role === 'technician') {
            $query->where('technician_id', $user->id);
        } else {
            $query->where('client_id', $user->id);
        }
        
        $statusCounts = $query->select('status', DB::raw('count(*) as total'))
                            ->groupBy('status')
                            ->pluck('total', 'status')
                            ->toArray();
        
        // Garantir que todos os status estejam presentes
        $statuses = ['pendente', 'em_andamento', 'concluida'];
        $labels = ['Pendente', 'Em Andamento', 'Concluída'];
        $colors = ['#9CA3AF', '#FBBF24', '#10B981'];
        
        $data = [];
        foreach ($statuses as $index => $status) {
            $data[] = [
                'status' => $labels[$index],
                'count' => $statusCounts[$status] ?? 0,
                'color' => $colors[$index]
            ];
        }
        
        return $data;
    }
    
    /**
     * Obtém dados para o gráfico de tempo médio de conclusão
     *
     * @param \App\Models\User $user
     * @return array
     */
    private function getCompletionTimeData($user)
    {
        $query = ServiceOrder::where('status', 'concluida');
        
        if ($user->role === 'technician') {
            $query->where('technician_id', $user->id);
        } else {
            $query->where('client_id', $user->id);
        }
        
        // Calcular o tempo médio (em dias) entre a criação e a última atualização para ordens concluídas
        $orders = $query->get();
        $completionTimes = [];
        
        foreach ($orders as $order) {
            $created = Carbon::parse($order->created_at);
            $updated = Carbon::parse($order->updated_at);
            $diffInDays = $created->diffInDays($updated);
            $completionTimes[] = $diffInDays;
        }
        
        // Se não houver dados, retornar um array vazio
        if (empty($completionTimes)) {
            return [];
        }
        
        // Calcular estatísticas básicas
        $avgCompletionTime = array_sum($completionTimes) / count($completionTimes);
        $minCompletionTime = min($completionTimes);
        $maxCompletionTime = max($completionTimes);
        
        return [
            'avg' => round($avgCompletionTime, 1),
            'min' => $minCompletionTime,
            'max' => $maxCompletionTime,
            'count' => count($completionTimes)
        ];
    }
    
    /**
     * Obtém dados para o gráfico de ordens por mês
     *
     * @param \App\Models\User $user
     * @return array
     */
    private function getOrdersPerMonthData($user)
    {
        $query = ServiceOrder::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as count')
            );
            
        if ($user->role === 'technician') {
            $query->where('technician_id', $user->id);
        } else {
            $query->where('client_id', $user->id);
        }
        
        $results = $query->whereYear('created_at', date('Y'))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();
                
        $months = [
            'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun',
            'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'
        ];
        
        $data = [
            'labels' => [],
            'data' => array_fill(0, 12, 0)
        ];
        
        foreach ($results as $result) {
            // Mês é baseado em 1, então subtraímos 1 para corresponder ao índice
            $monthIndex = $result->month - 1;
            $data['data'][$monthIndex] = $result->count;
        }
        
        $data['labels'] = $months;
        
        return $data;
    }
    
    /**
     * Obtém dados de desempenho para técnicos
     *
     * @param int $technicianId
     * @return array|null
     */
    private function getTechnicianPerformanceData($technicianId)
    {
        // Obter tempo médio de conclusão por semana das últimas 8 semanas
        $startDate = Carbon::now()->subWeeks(8)->startOfWeek();
        
        $weeklyData = ServiceOrder::where('technician_id', $technicianId)
            ->where('status', 'concluida')
            ->where('updated_at', '>=', $startDate)
            ->select(
                DB::raw('YEARWEEK(updated_at) as yearweek'),
                DB::raw('COUNT(*) as completed_count'),
                DB::raw('AVG(TIMESTAMPDIFF(DAY, created_at, updated_at)) as avg_completion_time')
            )
            ->groupBy('yearweek')
            ->orderBy('yearweek')
            ->get();
        
        if ($weeklyData->isEmpty()) {
            return null;
        }
        
        $labels = [];
        $completionData = [];
        $countData = [];
        
        // Preencher com as últimas 8 semanas
        $currentWeek = Carbon::now()->startOfWeek();
        for ($i = 8; $i >= 1; $i--) {
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek()->format('d/m');
            $labels[] = "Semana $weekStart";
            $yearweek = Carbon::now()->subWeeks($i)->startOfWeek()->format('YW');
            
            $weekRecord = $weeklyData->firstWhere('yearweek', $yearweek);
            
            $completionData[] = $weekRecord ? round($weekRecord->avg_completion_time, 1) : 0;
            $countData[] = $weekRecord ? $weekRecord->completed_count : 0;
        }
        
        return [
            'labels' => $labels,
            'completion_time' => $completionData,
            'completed_count' => $countData
        ];
    }
    
    /**
     * Obtém estatísticas gerais
     *
     * @param \App\Models\User $user
     * @return array
     */
    private function getGeneralStats($user)
    {
        $query = ServiceOrder::query();
        
        if ($user->role === 'technician') {
            $query->where('technician_id', $user->id);
        } else {
            $query->where('client_id', $user->id);
        }
        
        $total = $query->count();
        
        $completedQuery = clone $query;
        $completed = $completedQuery->where('status', 'concluida')->count();
        
        $completionRate = $total > 0 ? round(($completed / $total) * 100, 1) : 0;
        
        // Média de ordens por mês
        $oldestOrder = ServiceOrder::orderBy('created_at', 'asc')->first();
        $monthsActive = 1; // Mínimo de 1 mês
        
        if ($oldestOrder) {
            $firstDate = Carbon::parse($oldestOrder->created_at);
            $monthsActive = $firstDate->diffInMonths(Carbon::now()) + 1; // +1 para incluir o mês atual
            if ($monthsActive < 1) {
                $monthsActive = 1;
            }
        }
        
        $avgOrdersPerMonth = $total > 0 ? round($total / $monthsActive, 1) : 0;
        
        // Crescimento do último mês
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
