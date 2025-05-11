<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (Auth::user()->role !== 'technician') {
                return redirect()->route('dashboard')
                    ->with('error', 'Acesso restrito. Apenas a área técnica pode visualizar o dashboard analítico.');
            }
            return $next($request);
        });
    }
     
    public function index()
    {
        try {
            $user = Auth::user();
            
            $statusData = $this->getStatusChartData($user);
            $completionTimeData = $this->getCompletionTimeData($user);
            $ordersPerMonthData = $this->getOrdersPerMonthData($user);
            $performanceData = $this->getTechnicianPerformanceData($user->id);
            $stats = $this->getGeneralStats($user);
              return view('analytics.index', [
                'statusData' => json_encode($statusData),
                'completionTimeData' => json_encode($completionTimeData),
                'ordersPerMonthData' => json_encode($ordersPerMonthData),
                'performanceData' => $performanceData ? json_encode($performanceData) : null,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao gerar dashboard analítico: ' . $e->getMessage());
            
            return view('analytics.index', [
                'error' => 'Ocorreu um erro ao processar os dados analíticos. Por favor, tente novamente mais tarde.'
            ]);
        }
    }
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
     */    private function getCompletionTimeData($user)
    {
        $query = ServiceOrder::where('status', 'concluida');
        
        if ($user->role === 'technician') {
            $query->where('technician_id', $user->id);
        } else {
            $query->where('client_id', $user->id);
        }
          $stats = $query->select([
            DB::raw('COUNT(*) as count'),
            DB::raw('AVG(TIMESTAMPDIFF(DAY, created_at, updated_at)) as avg_time'),
            DB::raw('MIN(TIMESTAMPDIFF(DAY, created_at, updated_at)) as min_time'),
            DB::raw('MAX(TIMESTAMPDIFF(DAY, created_at, updated_at)) as max_time')
        ])->first();
        
        if (!$stats || $stats->count == 0) {
            return [];
        }
        
        return [
            'avg' => round($stats->avg_time, 1),
            'min' => (int)$stats->min_time,
            'max' => (int)$stats->max_time,
            'count' => $stats->count
        ];
    }
      private function getOrdersPerMonthData($user)
    {
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
            ->whereYear('created_at', date('Y'));
            
        if ($user->role === 'technician') {
            $query->where('technician_id', $user->id);
        } else {
            $query->where('client_id', $user->id);
        }
        
        $results = $query->groupBy('month')
                ->orderBy('month')
                ->get();
                
        // Preencher os dados com os resultados da consulta
        foreach ($results as $result) {
            // Mês é baseado em 1, então subtraímos 1 para corresponder ao índice do array (0-11)
            $monthIndex = $result->month - 1;
            $monthCounts[$monthIndex] = (int)$result->count;
        }
        
        return [
            'labels' => $months,
            'data' => $monthCounts
        ];
    }
      private function getTechnicianPerformanceData($technicianId)
    {
        $labels = [];
        $completionData = [];
        $countData = [];

        for ($i = 8; $i >= 1; $i--) {
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
            $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();
            $labels[] = "Semana " . $weekStart->format('d/m');
            $weekStats = ServiceOrder::where('technician_id', $technicianId)
                ->where('status', 'concluida')
                ->whereBetween('updated_at', [$weekStart, $weekEnd])
                ->select([
                    DB::raw('COUNT(*) as completed_count'),
                    DB::raw('AVG(TIMESTAMPDIFF(DAY, created_at, updated_at)) as avg_completion_time')
                ])
                ->first();
            
            $completionData[] = $weekStats->avg_completion_time ? round($weekStats->avg_completion_time, 1) : 0;
            $countData[] = $weekStats->completed_count ?: 0;
        }
        
        // Verificar se há pelo menos alguns dados válidos
        $hasData = false;
        foreach ($countData as $count) {
            if ($count > 0) {
                $hasData = true;
                break;
            }
        }
        
        if (!$hasData) {
            return null;
        }
        
        return [
            'labels' => $labels,
            'completion_time' => $completionData,
            'completed_count' => $countData
        ];
    }
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
        
        $oldestOrder = ServiceOrder::orderBy('created_at', 'asc')->first();
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
