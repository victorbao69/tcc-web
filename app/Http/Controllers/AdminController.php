<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use App\Models\CarrinhoItem;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Tela do administrador: lista todas as contas e todos os produtos
 * e permite excluir. A exclusão é DEFINITIVA (some do banco de dados).
 */
class AdminController extends Controller
{
    public function index()
    {
        $usuarios = User::with('empresa')->orderBy('id')->get();
        $produtos = Produto::with('empresa')->orderBy('id')->get();

        return view('admin.index', compact('usuarios', 'produtos'));
    }

    public function excluirUsuario(User $user)
    {
        if ($user->ehAdmin()) {
            return back()->with('erro', 'Não é possível excluir uma conta de administrador.');
        }

        $nome = $user->name;

        // Tudo ou nada: se algo falhar no meio, nada fica apagado pela metade
        DB::transaction(function () use ($user) {
            // Empresa: apaga cada produto dela (isso também avisa os clientes afetados)
            if ($user->empresa) {
                foreach ($user->empresa->produtos as $produto) {
                    $this->apagarProduto($produto, false);
                }
                $user->empresa->delete();
            }

            // Cliente: apaga os pedidos e o carrinho dele
            // (a tabela pedido não deixa apagar o usuário enquanto houver pedidos)
            Pedido::where('user_id', $user->id)->delete();
            CarrinhoItem::where('user_id', $user->id)->delete();

            // Tokens do app mobile e sessões abertas no site
            $user->tokens()->delete();
            DB::table('sessions')->where('user_id', $user->id)->delete();

            $user->delete();
        });

        return back()->with('sucesso', "Conta \"{$nome}\" excluída do banco de dados.");
    }

    public function excluirProduto(Produto $produto)
    {
        $nome = $produto->nome;

        DB::transaction(function () use ($produto) {
            $this->apagarProduto($produto, true);
        });

        return back()->with('sucesso', "Produto \"{$nome}\" excluído do banco de dados.");
    }

    /**
     * Apaga um produto de verdade e deixa um aviso para quem era afetado:
     * clientes que tinham o produto no carrinho ou em pedidos e a empresa dona.
     */
    private function apagarProduto(Produto $produto, bool $avisarEmpresa): void
    {
        $nome = $produto->nome;

        $clientesNoCarrinho = CarrinhoItem::where('produto_id', $produto->id)->pluck('user_id')->unique();
        $clientesComPedido = Pedido::where('produto_id', $produto->id)->pluck('user_id')->unique();

        foreach ($clientesNoCarrinho as $userId) {
            Aviso::create([
                'user_id' => $userId,
                'mensagem' => "O produto \"{$nome}\" que estava no seu carrinho foi excluído pelo administrador.",
            ]);
        }

        foreach ($clientesComPedido as $userId) {
            Aviso::create([
                'user_id' => $userId,
                'mensagem' => "O pedido do produto \"{$nome}\" foi removido porque o produto foi excluído pelo administrador.",
            ]);
        }

        if ($avisarEmpresa && $produto->empresa) {
            Aviso::create([
                'user_id' => $produto->empresa->users_id,
                'mensagem' => "O seu produto \"{$nome}\" foi excluído pelo administrador.",
            ]);
        }

        // Primeiro o que depende do produto (pedidos e carrinhos), depois o produto
        Pedido::where('produto_id', $produto->id)->delete();
        CarrinhoItem::where('produto_id', $produto->id)->delete();

        // Apaga o arquivo da imagem, mas só se foi enviado por upload
        if (str_starts_with((string) $produto->urlimagem, 'uploads/produtos/')) {
            File::delete(public_path($produto->urlimagem));
        }

        $produto->delete();
    }
}
