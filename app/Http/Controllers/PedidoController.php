<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    /**
     * Cliente vê os próprios pedidos; empresa vê os pedidos dos seus produtos.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->ehEmpresa()) {
            $pedidos = Pedido::with(['produto', 'user'])
                ->whereHas('produto', function ($query) use ($user) {
                    $query->where('empresa_id', $user->empresa->id);
                })
                ->latest()
                ->get();
        } else {
            $pedidos = Pedido::with('produto')
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        return view('pedidos.index', compact('pedidos'));
    }

    public function update(Request $request, Pedido $pedido)
    {
        $user = auth()->user();

        if (! $user->ehEmpresa() || $pedido->produto->empresa_id !== $user->empresa->id) {
            abort(403);
        }

        $request->validate([
            'status' => ['required', 'in:pendente,pago,enviado,concluido'],
        ]);

        $pedido->update(['status' => $request->status]);

        return back()->with('sucesso', 'Status do pedido atualizado!');
    }

    public function destroy(Pedido $pedido)
    {
        $user = auth()->user();

        if ($pedido->user_id !== $user->id || $pedido->status !== 'pendente') {
            abort(403);
        }

        $pedido->delete();

        return back()->with('sucesso', 'Pedido cancelado.');
    }
}
