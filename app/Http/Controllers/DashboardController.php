<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $stats = [];
          if ($user->role === 'technician') {
            // Estatísticas para técnicos
            $stats['total'] = ServiceOrder::where('technician_id', $user->id)->count();
            $stats['pending'] = ServiceOrder::where('technician_id', $user->id)
                ->where('status', 'pendente')->count();
            $stats['in_progress'] = ServiceOrder::where('technician_id', $user->id)
                ->where('status', 'em_andamento')->count();
            $stats['completed'] = ServiceOrder::where('technician_id', $user->id)
                ->where('status', 'concluida')->count();            // Clientes recentes atendidos pelo técnico
            $recentClients = ServiceOrder::orderedByLatest()
                ->where('technician_id', $user->id)
                ->with('client')
                ->take(5)
                ->get()
                ->pluck('client')
                ->unique('id');
                
            // Ordens recentes
            $recentOrders = ServiceOrder::orderedByLatest()
                ->where('technician_id', $user->id)
                ->take(5)
                ->get();
                
        } else {
            // Estatísticas para clientes
            $stats['total'] = ServiceOrder::where('client_id', $user->id)->count();
            $stats['pending'] = ServiceOrder::where('client_id', $user->id)
                ->where('status', 'pendente')->count();
            $stats['in_progress'] = ServiceOrder::where('client_id', $user->id)
                ->where('status', 'em_andamento')->count();
            $stats['completed'] = ServiceOrder::where('client_id', $user->id)                ->where('status', 'concluida')->count();            // Técnicos que atenderam o cliente
            $technicians = ServiceOrder::orderedByLatest()
                ->where('client_id', $user->id)
                ->with('technician')
                ->get()
                ->pluck('technician')
                ->unique('id');
                
            // Ordens recentes
            $recentOrders = ServiceOrder::orderedByLatest()
                ->where('client_id', $user->id)
                ->take(5)
                ->get();
        }
        
        return view('dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'technicians' => $technicians ?? null,
            'recentClients' => $recentClients ?? null,
        ]);
    }
}
