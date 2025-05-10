<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ServiceOrderController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'technician') {
            $orders = ServiceOrder::where('technician_id', $user->id)->with('client')->get();
        } else {
            $orders = ServiceOrder::where('client_id', $user->id)->with('technician')->get();
        }
        return view('service_orders.index', compact('orders'));
    }

    public function create()
    {
        $clients = User::where('role', 'client')->get();
        return view('service_orders.create', compact('clients'));
    }

    public function store(Request $request)
    {
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
            'technician_id' => Auth::id(),
        ]);
        return redirect()->route('service_orders.index')->with('success', 'Ordem de serviço criada com sucesso!');
    }

    public function edit(ServiceOrder $serviceOrder)
    {
        $this->authorize('update', $serviceOrder);
        return view('service_orders.edit', compact('serviceOrder'));
    }

    public function update(Request $request, ServiceOrder $serviceOrder)
    {
        $this->authorize('update', $serviceOrder);
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => ['required', Rule::in(['pendente', 'em_andamento', 'concluida'])],
        ]);        $serviceOrder->update($request->only(['title', 'description', 'status']));
        
        // Dispara o evento quando o status é alterado para concluída
        if ($serviceOrder->status === 'concluida' && $serviceOrder->getOriginal('status') !== 'concluida') {
            event(new \App\Events\ServiceOrderCompleted($serviceOrder));
        }
        return redirect()->route('service_orders.index')->with('success', 'Ordem de serviço atualizada!');
    }
}
