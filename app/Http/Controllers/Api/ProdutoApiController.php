<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produto;

/**
 * Catálogo para o app: os mesmos produtos "ativos" que aparecem no site.
 */
class ProdutoApiController extends Controller
{
    public function index()
    {
        $produtos = Produto::with('empresa')
            ->where('status', 'ativo')
            ->latest()
            ->get()
            ->map(fn (Produto $produto) => [
                'id' => $produto->id,
                'nome' => $produto->nome,
                'descricao' => $produto->descricao,
                'preco' => (float) $produto->preco,
                'categoria' => $produto->categoria,
                'imagem_url' => $produto->imagem_url,
                'vendedor' => $produto->empresa->name ?? '',
            ]);

        return response()->json($produtos);
    }
}
