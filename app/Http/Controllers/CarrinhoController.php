<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Http\Request;

class CarrinhoController extends Controller
{
    public function index()
    {
        $carrinho = session('carrinho', []);

        $itens = [];
        $total = 0;

        foreach ($carrinho as $produtoId => $quantidade) {
            $produto = Produto::find($produtoId);

            if (! $produto) {
                continue;
            }

            $subtotal = $produto->preco * $quantidade;
            $total += $subtotal;

            $itens[] = [
                'produto' => $produto,
                'quantidade' => $quantidade,
                'subtotal' => $subtotal,
            ];
        }

        return view('carrinho.index', compact('itens', 'total'));
    }

    public function adicionar(Produto $produto)
    {
        $carrinho = session('carrinho', []);
        $carrinho[$produto->id] = ($carrinho[$produto->id] ?? 0) + 1;
        session(['carrinho' => $carrinho]);

        return back()->with('sucesso', "{$produto->nome} adicionado ao carrinho!");
    }

    public function atualizar(Request $request, Produto $produto)
    {
        $carrinho = session('carrinho', []);

        if (! isset($carrinho[$produto->id])) {
            return back();
        }

        if ($request->acao === 'aumentar') {
            $carrinho[$produto->id]++;
        } elseif ($request->acao === 'diminuir') {
            $carrinho[$produto->id]--;
        }

        if ($carrinho[$produto->id] <= 0) {
            unset($carrinho[$produto->id]);
        }

        session(['carrinho' => $carrinho]);

        return back();
    }

    public function remover(Produto $produto)
    {
        $carrinho = session('carrinho', []);
        unset($carrinho[$produto->id]);
        session(['carrinho' => $carrinho]);

        return back();
    }

    public function finalizar()
    {
        $carrinho = session('carrinho', []);

        if (empty($carrinho)) {
            return back()->with('erro', 'Seu carrinho está vazio.');
        }

        foreach ($carrinho as $produtoId => $quantidade) {
            $produto = Produto::find($produtoId);

            if (! $produto) {
                continue;
            }

            for ($i = 0; $i < $quantidade; $i++) {
                Pedido::create([
                    'valor' => $produto->preco,
                    'status' => 'pendente',
                    'user_id' => auth()->id(),
                    'produto_id' => $produto->id,
                ]);
            }
        }

        session()->forget('carrinho');

        return redirect('/pedidos')->with('sucesso', 'Pedido realizado com sucesso!');
    }
}
