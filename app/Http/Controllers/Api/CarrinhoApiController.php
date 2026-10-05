<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarrinhoItem;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Carrinho de compras do cliente, guardado no servidor (então o cliente
 * vê o mesmo carrinho em qualquer aparelho). O item é identificado pelo
 * produto: PUT /carrinho/5 muda a quantidade do produto 5 no carrinho.
 */
class CarrinhoApiController extends Controller
{
    // GET /carrinho -> itens do carrinho e o total
    public function index(Request $request)
    {
        return response()->json($this->montarCarrinho($request->user()->id));
    }

    // POST /carrinho  {"produto_id": 1, "quantidade": 2} -> coloca no carrinho
    public function store(Request $request)
    {
        $dados = $request->validate([
            'produto_id' => ['required', 'integer', 'exists:produto,id'],
            'quantidade' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $produto = Produto::find($dados['produto_id']);

        if ($produto->status !== 'ativo') {
            return response()->json([
                'message' => "O produto \"{$produto->nome}\" não está disponível.",
            ], 422);
        }

        // Se o produto já está no carrinho, somamos a quantidade
        $item = CarrinhoItem::firstOrNew([
            'user_id' => $request->user()->id,
            'produto_id' => $produto->id,
        ]);

        $criado = ! $item->exists;
        $novaQuantidade = ($criado ? 0 : $item->quantidade) + $dados['quantidade'];

        if ($novaQuantidade > 50) {
            return response()->json(['message' => 'O máximo é 50 unidades por produto.'], 422);
        }

        $item->quantidade = $novaQuantidade;
        $item->save();

        // 201 quando o produto entrou agora no carrinho, 200 quando só aumentou a quantidade
        return response()->json($this->formatar($item->load('produto')), $criado ? 201 : 200);
    }

    // PUT /carrinho/{produto}  {"quantidade": 3} -> troca a quantidade
    public function update(Request $request, Produto $produto)
    {
        $dados = $request->validate([
            'quantidade' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $item = CarrinhoItem::where('user_id', $request->user()->id)
            ->where('produto_id', $produto->id)
            ->first();

        if (! $item) {
            return response()->json(['message' => 'Esse produto não está no seu carrinho.'], 404);
        }

        $item->quantidade = $dados['quantidade'];
        $item->save();

        return response()->json($this->formatar($item->load('produto')));
    }

    // DELETE /carrinho/{produto} -> tira um produto do carrinho
    public function destroy(Request $request, Produto $produto)
    {
        $apagados = CarrinhoItem::where('user_id', $request->user()->id)
            ->where('produto_id', $produto->id)
            ->delete();

        if ($apagados === 0) {
            return response()->json(['message' => 'Esse produto não está no seu carrinho.'], 404);
        }

        return response()->json(null, 204);
    }

    // DELETE /carrinho -> esvazia o carrinho
    public function clear(Request $request)
    {
        CarrinhoItem::where('user_id', $request->user()->id)->delete();

        return response()->json(null, 204);
    }

    // POST /carrinho/finalizar -> transforma o carrinho em pedidos (igual ao site)
    public function finalizar(Request $request)
    {
        $userId = $request->user()->id;

        $itens = CarrinhoItem::with('produto')->where('user_id', $userId)->get();

        if ($itens->isEmpty()) {
            return response()->json(['message' => 'Seu carrinho está vazio.'], 422);
        }

        foreach ($itens as $item) {
            if ($item->produto->status !== 'ativo') {
                return response()->json([
                    'message' => "O produto \"{$item->produto->nome}\" não está mais disponível. Remova-o do carrinho.",
                ], 422);
            }
        }

        // Tudo ou nada: se algo falhar no meio, nenhum pedido fica gravado pela metade
        $pedidos = DB::transaction(function () use ($itens, $userId) {
            $criados = [];

            foreach ($itens as $item) {
                // Cada unidade vira uma linha na tabela "pedido", como no site
                for ($i = 0; $i < $item->quantidade; $i++) {
                    $criados[] = Pedido::create([
                        'valor' => $item->produto->preco,
                        'status' => 'pendente',
                        'user_id' => $userId,
                        'produto_id' => $item->produto_id,
                    ]);
                }
            }

            CarrinhoItem::where('user_id', $userId)->delete();

            return $criados;
        });

        $resposta = collect($pedidos)->map(function (Pedido $pedido) {
            $pedido->load('produto');

            return [
                'id' => $pedido->id,
                'valor' => (float) $pedido->valor,
                'status' => $pedido->status,
                'data' => $pedido->created_at->toIso8601String(),
                'produto' => [
                    'id' => $pedido->produto->id,
                    'nome' => $pedido->produto->nome,
                    'imagem_url' => $pedido->produto->imagem_url,
                ],
            ];
        });

        return response()->json($resposta, 201);
    }

    private function montarCarrinho(int $userId): array
    {
        $itens = CarrinhoItem::with('produto')
            ->where('user_id', $userId)
            ->orderBy('id')
            ->get()
            ->map(fn (CarrinhoItem $item) => $this->formatar($item))
            ->values();

        return [
            'itens' => $itens,
            'total' => round($itens->sum('subtotal'), 2),
        ];
    }

    private function formatar(CarrinhoItem $item): array
    {
        $produto = $item->produto;

        return [
            'produto_id' => $produto->id,
            'nome' => $produto->nome,
            'preco_unitario' => (float) $produto->preco,
            'quantidade' => $item->quantidade,
            'subtotal' => round((float) $produto->preco * $item->quantidade, 2),
            'imagem_url' => $produto->imagem_url,
            'disponivel' => $produto->status === 'ativo',
        ];
    }
}
