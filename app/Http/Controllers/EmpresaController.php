<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    /**
     * Painel da empresa logada: produtos e pedidos recebidos.
     */
    public function index()
    {
        $empresa = auth()->user()->empresa;

        $produtos = $empresa->produtos()->latest()->get();

        $pedidos = Pedido::with(['produto', 'user'])
            ->whereHas('produto', function ($query) use ($empresa) {
                $query->where('empresa_id', $empresa->id);
            })
            ->latest()
            ->get();

        $totalVendas = $pedidos->whereIn('status', ['pago', 'enviado', 'concluido'])->sum('valor');
        $totalPedidos = $pedidos->count();
        $totalConcluidos = $pedidos->where('status', 'concluido')->count();

        return view('empresas.index', compact('empresa', 'produtos', 'pedidos', 'totalVendas', 'totalPedidos', 'totalConcluidos'));
    }

    public function edit()
    {
        $empresa = auth()->user()->empresa;

        return view('empresas.edit', compact('empresa'));
    }

    public function update(Request $request)
    {
        $empresa = auth()->user()->empresa;

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cnpj' => ['required', 'string', 'max:20'],
        ]);

        $empresa->update($request->only('name', 'cnpj'));

        return redirect('/empresas')->with('sucesso', 'Dados da empresa atualizados!');
    }
}
