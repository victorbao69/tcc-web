<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{
    /**
     * Catálogo público de produtos ativos.
     */
    public function index()
    {
        $produtos = Produto::with('empresa')
            ->where('status', 'ativo')
            ->latest()
            ->get();

        return view('produtos.index', compact('produtos'));
    }

    public function create()
    {
        return view('adicionar');
    }

    public function store(Request $request)
    {
        $dados = $this->validarDados($request);
        $dados['empresa_id'] = auth()->user()->empresa->id;

        Produto::create($dados);

        return redirect()->route('adicionar')->with('sucesso', 'Produto adicionado com sucesso!');
    }

    public function edit(Produto $produto)
    {
        $this->verificarDono($produto);

        return view('produtos.edit', compact('produto'));
    }

    public function update(Request $request, Produto $produto)
    {
        $this->verificarDono($produto);

        $dados = $this->validarDados($request);

        $produto->update($dados);

        return redirect('/empresas')->with('sucesso', 'Produto atualizado com sucesso!');
    }

   public function destroy(Produto $produto)
{
    $this->verificarDono($produto);

    if ($produto->pedidos()->exists()) {
        return back()->with('erro', 'Não é possível excluir este produto porque já existem pedidos vinculados a ele. Marque o status como "vendido" em vez de excluir.');
    }

    $produto->delete();

    return redirect('/empresas')->with('sucesso', 'Produto removido.');
}

   private function validarDados(Request $request): array
{
    $dados = $request->validate([
        'nome' => ['required', 'string', 'max:100'],
        'descricao' => ['required', 'string', 'max:100'],
        'preco' => ['required', 'numeric', 'min:0'],
        'categoria' => ['required', 'in:sofa,armario,cama,mesa,cadeira'],
        'status' => ['required', 'in:ativo,vendido'],
        'urlimagem' => ['nullable', 'string'],
    ]);

    $dados['urlimagem'] = $dados['urlimagem'] ?? '';

    return $dados;
}

    private function verificarDono(Produto $produto): void
    {
        if ($produto->empresa_id !== auth()->user()->empresa->id) {
            abort(403, 'Você não pode alterar produtos de outra empresa.');
        }
    }
}
