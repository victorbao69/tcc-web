<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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

        // Se enviou um arquivo, ele vale mais do que o link digitado
        if ($request->hasFile('imagem')) {
            $dados['urlimagem'] = $this->salvarImagem($request->file('imagem'));
        }

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

        if ($request->hasFile('imagem')) {
            // Troca a imagem: apaga o arquivo antigo (se ele foi enviado por upload)
            $this->apagarImagem($produto);
            $dados['urlimagem'] = $this->salvarImagem($request->file('imagem'));
        } elseif ($request->boolean('remover_imagem')) {
            $this->apagarImagem($produto);
            $dados['urlimagem'] = '';
        } elseif (empty($dados['urlimagem'])) {
            // Nada novo foi informado: mantém a imagem que o produto já tinha
            $dados['urlimagem'] = $produto->urlimagem;
        } elseif ($dados['urlimagem'] !== $produto->urlimagem) {
            // Trocou por um link novo: o arquivo antigo (se havia) não serve mais
            $this->apagarImagem($produto);
        }

        $produto->update($dados);

        return redirect('/empresas')->with('sucesso', 'Produto atualizado com sucesso!');
    }

   public function destroy(Produto $produto)
{
    $this->verificarDono($produto);

    if ($produto->pedidos()->exists()) {
        return back()->with('erro', 'Não é possível excluir este produto porque já existem pedidos vinculados a ele. Marque o status como "vendido" em vez de excluir.');
    }

    $this->apagarImagem($produto);
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
        'imagem' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
    ], [
        'imagem.image' => 'O arquivo enviado precisa ser uma imagem.',
        'imagem.mimes' => 'A imagem deve ser JPG, PNG ou WEBP.',
        'imagem.max' => 'A imagem pode ter no máximo 4 MB.',
        'imagem.uploaded' => 'Não foi possível enviar a imagem (talvez ela seja grande demais).',
    ]);

    // o arquivo é tratado à parte (salvarImagem), não vai direto para o banco
    unset($dados['imagem']);

    $dados['urlimagem'] = $dados['urlimagem'] ?? '';

    return $dados;
}

    private function verificarDono(Produto $produto): void
    {
        if ($produto->empresa_id !== auth()->user()->empresa->id) {
            abort(403, 'Você não pode alterar produtos de outra empresa.');
        }
    }

    /**
     * Guarda o arquivo em public/uploads/produtos e devolve o caminho
     * que será salvo na coluna urlimagem (ex: uploads/produtos/abc.jpg).
     */
    private function salvarImagem($arquivo): string
    {
        $nome = Str::uuid() . '.' . $arquivo->extension();
        $arquivo->move(public_path('uploads/produtos'), $nome);

        return 'uploads/produtos/' . $nome;
    }

    /**
     * Apaga o arquivo da imagem, mas só se ele foi enviado por upload
     * (links da internet não são arquivos nossos, então ficam como estão).
     */
    private function apagarImagem(Produto $produto): void
    {
        if (str_starts_with((string) $produto->urlimagem, 'uploads/produtos/')) {
            File::delete(public_path($produto->urlimagem));
        }
    }
}
