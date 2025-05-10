<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ServiceOrderController extends Controller
{    
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = null;
        
        if ($user->role === 'technician') {
            $query = ServiceOrder::where('technician_id', $user->id)->with('client');
        } else {
            $query = ServiceOrder::where('client_id', $user->id)->with('technician');
        }
        
        // Filtrar por status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);     
        }
        
        // Filtrar por data
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        // Pesquisa por título ou descrição
        if ($request->has('search') && $request->search) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }
        
        $orders = $query->latest()->get();
        
        return view('service_orders.index', [
            'orders' => $orders,
            'filters' => $request->only(['status', 'date_from', 'date_to', 'search']),
        ]);
    }

    public function create()
    {
        // RF02: Apenas técnicos podem criar OS
        if (auth()->user()->role !== 'technician') {
            abort(403, 'Apenas técnicos podem criar ordens de serviço.');
        }
        $clients = User::where('role', 'client')->get();
        return view('service_orders.create', compact('clients'));
    }

    public function store(Request $request)
    {
        // RF02: Apenas técnicos podem criar OS
        if (auth()->user()->role !== 'technician') {
            abort(403, 'Apenas técnicos podem criar ordens de serviço.');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'client_id' => ['required', Rule::exists('users', 'id')->where('role', 'client')],
        ]);
        $order = ServiceOrder::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pendente',
            'client_id' => $request->client_id,
            'technician_id' => auth()->id(),
        ]);
        return redirect()->route('service_orders.index')->with('success', 'Ordem de serviço criada com sucesso!');
    }

    public function edit(ServiceOrder $serviceOrder)
    {
        // RF03: Apenas o técnico responsável pode editar
        if (auth()->user()->role !== 'technician' || auth()->id() !== $serviceOrder->technician_id) {
            abort(403, 'Apenas o técnico responsável pode editar esta ordem.');
        }
        return view('service_orders.edit', compact('serviceOrder'));
    }

    public function update(Request $request, ServiceOrder $serviceOrder)
    {
        // RF03: Apenas o técnico responsável pode atualizar
        if (auth()->user()->role !== 'technician' || auth()->id() !== $serviceOrder->technician_id) {
            abort(403, 'Apenas o técnico responsável pode atualizar esta ordem.');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => ['required', Rule::in(['pendente', 'em_andamento', 'concluida'])],
        ]);
        $originalStatus = $serviceOrder->status;
        $serviceOrder->update($request->only(['title', 'description', 'status']));
        // RF04: Disparar evento se status mudou para concluída
        if ($request->status === 'concluida' && $originalStatus !== 'concluida') {
            event(new \App\Events\ServiceOrderCompleted($serviceOrder));
        }
        return redirect()->route('service_orders.index')->with('success', 'Ordem de serviço atualizada!');
    }
    
    public function show(ServiceOrder $serviceOrder)
    {
        // RF: Garantir que apenas o técnico responsável ou o cliente possam visualizar
        $user = auth()->user();
        if (
            ($user->role === 'technician' && $serviceOrder->technician_id !== $user->id) ||
            ($user->role === 'client' && $serviceOrder->client_id !== $user->id)
        ) {
            abort(403, 'Você não tem permissão para visualizar esta ordem de serviço.');
        }
        // Carrega os relacionamentos
        $serviceOrder->load(['client', 'technician']);
        return view('service_orders.show', compact('serviceOrder'));
    }
}
