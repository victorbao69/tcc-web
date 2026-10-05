<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produto;
use Illuminate\Http\Request;

/**
 * Catálogo para o app: os mesmos produtos "ativos" que aparecem no site.
 *
 *  GET /api/v1/produtos                  lista todos
 *  GET /api/v1/produtos?busca=sofa       filtra pelo nome
 *  GET /api/v1/produtos?categoria=mesa   filtra pela categoria
 *  GET /api/v1/produtos/{id}             mostra um produto
 */
class ProdutoApiController extends Controller
{
    public function index(Request $request)
    {
        $consulta = Produto::with('empresa')->where('status', 'ativo');

        if ($request->filled('categoria')) {
            $consulta->where('categoria', $request->input('categoria'));
        }

        if ($request->filled('busca')) {
            $consulta->where('nome', 'like', '%' . $request->input('busca') . '%');
        }

        $produtos = $consulta->latest()->get()
            ->map(fn (Produto $produto) => $this->formatar($produto));

        return response()->json($produtos);
    }

    public function show(Produto $produto)
    {
        // Produto inativo não aparece no catálogo, então para a API ele "não existe"
        if ($produto->status !== 'ativo') {
            return response()->json(['message' => 'Produto não encontrado.'], 404);
        }

        return response()->json($this->formatar($produto->load('empresa')));
    }

    private function formatar(Produto $produto): array
    {
        return [
            'id' => $produto->id,
            'nome' => $produto->nome,
            'descricao' => $produto->descricao,
            'preco' => (float) $produto->preco,
            'categoria' => $produto->categoria,
            'imagem_url' => $produto->imagem_url,
            'vendedor' => $produto->empresa->name ?? '',
        ];
    }
}
