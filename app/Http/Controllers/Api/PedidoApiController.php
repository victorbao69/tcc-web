<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Http\Request;

/**
 * Pedidos do cliente. Funciona igual ao site: cada unidade comprada
 * vira uma linha na tabela "pedido" com status "pendente".
 */
class PedidoApiController extends Controller
{
    public function index(Request $request)
    {
        $pedidos = Pedido::with('produto')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get()
            ->map(fn (Pedido $pedido) => $this->formatar($pedido));

        return response()->json($pedidos);
    }

    /**
     * Finaliza a compra. O carrinho fica no app; aqui chega a lista:
     * { "itens": [ { "produto_id": 1, "quantidade": 2 }, ... ] }
     */
    public function store(Request $request)
    {
        $dados = $request->validate([
            'itens' => ['required', 'array', 'min:1'],
            'itens.*.produto_id' => ['required', 'integer', 'exists:produto,id'],
            'itens.*.quantidade' => ['required', 'integer', 'min:1', 'max:50'],
        ]);

        $criados = [];

        foreach ($dados['itens'] as $item) {
            $produto = Produto::find($item['produto_id']);

            if ($produto->status !== 'ativo') {
                return response()->json([
                    'message' => "O produto \"{$produto->nome}\" não está mais disponível.",
                ], 422);
            }

            for ($i = 0; $i < $item['quantidade']; $i++) {
                $pedido = Pedido::create([
                    'valor' => $produto->preco,
                    'status' => 'pendente',
                    'user_id' => $request->user()->id,
                    'produto_id' => $produto->id,
                ]);

                $criados[] = $this->formatar($pedido->load('produto'));
            }
        }

        return response()->json($criados, 201);
    }

    /**
     * Cancelar pedido: só o dono, e só enquanto estiver pendente (mesma regra do site).
     */
    public function destroy(Request $request, Pedido $pedido)
    {
        if ($pedido->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Esse pedido não é seu.'], 403);
        }

        if ($pedido->status !== 'pendente') {
            return response()->json(['message' => 'Só é possível cancelar pedidos pendentes.'], 403);
        }

        $pedido->delete();

        return response()->json(null, 204);
    }

    private function formatar(Pedido $pedido): array
    {
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
    }
}
