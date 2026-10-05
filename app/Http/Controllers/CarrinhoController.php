<?php

namespace App\Http\Controllers;

use App\Models\CarrinhoItem;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Carrinho do site. Ele fica guardado no banco (tabela carrinho_item) e não
 * mais na sessão: é o MESMO carrinho que o app mobile usa pela API. O que o
 * cliente coloca no site aparece no app, e o que coloca no app aparece no site.
 */
class CarrinhoController extends Controller
{
    // Mesmo limite da API (CarrinhoApiController)
    private const MAXIMO_POR_PRODUTO = 50;

    public function index()
    {
        $registros = CarrinhoItem::with('produto')
            ->where('user_id', auth()->id())
            ->orderBy('id')
            ->get();

        $itens = [];
        $total = 0;

        foreach ($registros as $registro) {
            $subtotal = $registro->produto->preco * $registro->quantidade;
            $total += $subtotal;

            $itens[] = [
                'produto' => $registro->produto,
                'quantidade' => $registro->quantidade,
                'subtotal' => $subtotal,
            ];
        }

        return view('carrinho.index', compact('itens', 'total'));
    }

    public function adicionar(Produto $produto)
    {
        if ($produto->status !== 'ativo') {
            return back()->with('erro', 'Esse produto não está disponível.');
        }

        // Se o produto já está no carrinho, soma 1; senão cria o item
        $item = CarrinhoItem::firstOrNew([
            'user_id' => auth()->id(),
            'produto_id' => $produto->id,
        ]);

        $novaQuantidade = ($item->exists ? $item->quantidade : 0) + 1;

        if ($novaQuantidade > self::MAXIMO_POR_PRODUTO) {
            return back()->with('erro', 'O máximo é ' . self::MAXIMO_POR_PRODUTO . ' unidades por produto.');
        }

        $item->quantidade = $novaQuantidade;
        $item->save();

        return back()->with('sucesso', "{$produto->nome} adicionado ao carrinho!");
    }

    public function atualizar(Request $request, Produto $produto)
    {
        $item = CarrinhoItem::where('user_id', auth()->id())
            ->where('produto_id', $produto->id)
            ->first();

        if (! $item) {
            return back();
        }

        if ($request->acao === 'aumentar') {
            if ($item->quantidade >= self::MAXIMO_POR_PRODUTO) {
                return back()->with('erro', 'O máximo é ' . self::MAXIMO_POR_PRODUTO . ' unidades por produto.');
            }
            $item->quantidade++;
        } elseif ($request->acao === 'diminuir') {
            $item->quantidade--;
        }

        // Chegou a zero: o produto sai do carrinho
        if ($item->quantidade <= 0) {
            $item->delete();
        } else {
            $item->save();
        }

        return back();
    }

    public function remover(Produto $produto)
    {
        CarrinhoItem::where('user_id', auth()->id())
            ->where('produto_id', $produto->id)
            ->delete();

        return back();
    }

    public function finalizar()
    {
        $userId = auth()->id();

        $itens = CarrinhoItem::with('produto')->where('user_id', $userId)->get();

        if ($itens->isEmpty()) {
            return back()->with('erro', 'Seu carrinho está vazio.');
        }

        foreach ($itens as $item) {
            if ($item->produto->status !== 'ativo') {
                return back()->with('erro', "O produto \"{$item->produto->nome}\" não está mais disponível. Remova-o do carrinho.");
            }
        }

        // Tudo ou nada: se algo falhar no meio, nenhum pedido fica gravado pela metade
        DB::transaction(function () use ($itens, $userId) {
            foreach ($itens as $item) {
                // Cada unidade vira uma linha na tabela "pedido"
                for ($i = 0; $i < $item->quantidade; $i++) {
                    Pedido::create([
                        'valor' => $item->produto->preco,
                        'status' => 'pendente',
                        'user_id' => $userId,
                        'produto_id' => $item->produto_id,
                    ]);
                }
            }

            CarrinhoItem::where('user_id', $userId)->delete();
        });

        return redirect('/pedidos')->with('sucesso', 'Pedido realizado com sucesso!');
    }
}
