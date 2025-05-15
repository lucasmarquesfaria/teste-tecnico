<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

/**
 * Controller para gerenciamento de Ordens de Serviço.
 *
 * Este controller gerencia todas as operações relacionadas às ordens de serviço,
 * incluindo listagem, criação, visualização, edição e atualização.
 * Também é responsável por disparar eventos quando uma ordem é concluída.
 */
class ServiceOrderController extends Controller
{    
    /**
     * Exibe uma lista de ordens de serviço filtradas.
     *
     * Mostra apenas as ordens de serviço associadas ao usuário atual:
     * - Para técnicos: ordens onde eles são os responsáveis
     * - Para clientes: ordens onde eles são os clientes
     *
     * Permite filtrar por status, data e termos de busca.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */    public function index(Request $request)
    {
        $user = Auth::user();
        $query = null;
        
        if ($user->role === 'technician') {
            $query = ServiceOrder::orderedByLatest()->where('technician_id', $user->id)->with('client');
        } else {
            $query = ServiceOrder::orderedByLatest()->where('client_id', $user->id)->with('technician');
        }
        
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);     
        }
        
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }        if ($request->has('search') && $request->search) {
            $search = '%' . $request->search . '%';
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }
          // Obter os resultados (ordenação já aplicada por orderedByLatest)
        $orders = $query->get();

        // Alerta visual para técnicos sobre OSs atrasadas
        $alertAtrasadas = null;
        if (auth()->user()->role === 'technician') {
            $qAtrasadas = $orders->filter(function($order) {
                return $order->sla_due_at && $order->status !== 'concluida' && \Carbon\Carbon::parse($order->sla_due_at)->isPast();
            });
            if ($qAtrasadas->count() > 0) {
                $alertAtrasadas = 'Atenção: Existem ' . $qAtrasadas->count() . ' ordem(ns) de serviço atrasada(s)!';
            }
        }
        
        return view('service_orders.index', [
            'orders' => $orders,
            'filters' => $request->only(['status', 'date_from', 'date_to', 'search']),
            'alertAtrasadas' => $alertAtrasadas,
        ]);
    }

    /**
     * Exibe o formulário para criar uma nova ordem de serviço.
     *
     * Acessível apenas para usuários com função de técnico.
     *
     * @return \Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create()
    {
        if (auth()->user()->role !== 'technician') {
            abort(403, 'Apenas técnicos podem criar ordens de serviço.');
        }
        $clients = User::where('role', 'client')->get();
        return view('service_orders.create', compact('clients'));
    }    
    
    /**
     * Armazena uma nova ordem de serviço no banco de dados.
     *
     * Valida os dados da requisição e cria uma nova ordem com
     * status inicial "pendente" e o técnico atual como responsável.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'technician') {
            abort(403, 'Apenas técnicos podem criar ordens de serviço.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'client_id' => ['required', Rule::exists('users', 'id')->where('role', 'client')],
        ]);
        
        $slaPrazoDias = 3; // Valor padrão, pode ser configurável depois
        $order = ServiceOrder::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pendente',
            'client_id' => $request->client_id,
            'technician_id' => auth()->id(),
            'sla_due_at' => Carbon::now()->addDays($slaPrazoDias),
        ]);
        
        return redirect()->route('service_orders.index')->with('success', 'Ordem de serviço criada com sucesso!');
    }

    /**
     * Exibe o formulário para editar uma ordem de serviço existente.
     *
     * Acessível apenas para o técnico responsável pela ordem.
     *
     * @param  \App\Models\ServiceOrder  $serviceOrder
     * @return \Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(ServiceOrder $serviceOrder)
    {
        if (auth()->user()->role !== 'technician' || auth()->id() !== $serviceOrder->technician_id) {
            abort(403, 'Apenas o técnico responsável pode editar esta ordem.');
        }
        
        return view('service_orders.edit', compact('serviceOrder'));
    }

    /**
     * Atualiza uma ordem de serviço existente.
     *
     * Se a ordem for concluída (status alterado para "concluida"),
     * dispara o evento ServiceOrderCompleted para notificar o cliente.
     * Utiliza cache para evitar disparos duplicados do evento.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceOrder  $serviceOrder
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(Request $request, ServiceOrder $serviceOrder)
    {
        if (auth()->user()->role !== 'technician' || auth()->id() !== $serviceOrder->technician_id) {
            abort(403, 'Apenas o técnico responsável pode atualizar esta ordem.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => ['required', Rule::in(['pendente', 'em_andamento', 'concluida'])],
        ]);
        
        $originalStatus = $serviceOrder->status;
        $serviceOrder->update($request->only(['title', 'description', 'status']));          if ($request->status === 'concluida' && $originalStatus !== 'concluida') {
            $serviceOrder = $serviceOrder->fresh()->load(['client', 'technician']);
            
            // Gera uma chave única para esta ordem de serviço
            $cacheKey = 'order_completed_event_' . $serviceOrder->id;
            
            // Verifica se o evento já foi disparado (usando remember para evitar race conditions)
            $eventDispatched = \Illuminate\Support\Facades\Cache::remember(
                $cacheKey, 
                now()->addDays(1), 
                function() use ($serviceOrder) {
                    // Dispara o evento apenas na primeira vez
                    event(new \App\Events\ServiceOrderCompleted($serviceOrder));
                    return true;
                }
            );
        }
        
        return redirect()->route('service_orders.index')->with('success', 'Ordem de serviço atualizada!');
    }
    
    /**
     * Exibe os detalhes de uma ordem de serviço específica.
     *
     * Acessível apenas para o técnico responsável ou o cliente associado.
     *
     * @param  \App\Models\ServiceOrder  $serviceOrder
     * @return \Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function show(ServiceOrder $serviceOrder)
    {
        $user = auth()->user();
        if (
            ($user->role === 'technician' && $serviceOrder->technician_id !== $user->id) ||
            ($user->role === 'client' && $serviceOrder->client_id !== $user->id)
        ) {
            abort(403, 'Você não tem permissão para visualizar esta ordem de serviço.');
        }
        
        $serviceOrder->load(['client', 'technician']);
        return view('service_orders.show', compact('serviceOrder'));
    }
}
