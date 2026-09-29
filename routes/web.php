<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProdutoController;
use Illuminate\Support\Facades\Route;

// Páginas públicas de autenticação
Route::get('/', fn () => view('welcome'))->name('home');
Route::get('/welcome', fn () => view('welcome'))->name('welcome');
Route::get('/cadastro', fn () => view('cadastro'))->name('cadastro');

Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/cadastro', [AuthController::class, 'register'])->name('cadastro.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Catálogo de produtos (público)
Route::get('/produtos', [ProdutoController::class, 'index'])->name('produtos');

// Rotas que exigem usuário logado
Route::middleware('auth')->group(function () {

    // Carrinho e pedidos (clientes e empresas logados)
    Route::get('/carrinho', [CarrinhoController::class, 'index'])->name('carrinho');
    Route::post('/carrinho/adicionar/{produto}', [CarrinhoController::class, 'adicionar'])->name('carrinho.adicionar');
    Route::post('/carrinho/atualizar/{produto}', [CarrinhoController::class, 'atualizar'])->name('carrinho.atualizar');
    Route::post('/carrinho/remover/{produto}', [CarrinhoController::class, 'remover'])->name('carrinho.remover');
    Route::post('/carrinho/finalizar', [CarrinhoController::class, 'finalizar'])->name('carrinho.finalizar');

    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos');
    Route::put('/pedidos/{pedido}', [PedidoController::class, 'update'])->name('pedidos.update');
    Route::delete('/pedidos/{pedido}', [PedidoController::class, 'destroy'])->name('pedidos.destroy');

    // Rotas restritas a usuários do tipo "empresa"
    Route::middleware('empresa')->group(function () {
        Route::get('/empresas', [EmpresaController::class, 'index'])->name('empresas');
        Route::get('/empresas/editar', [EmpresaController::class, 'edit'])->name('empresas.edit');
        Route::put('/empresas', [EmpresaController::class, 'update'])->name('empresas.update');

        Route::get('/adicionar', [ProdutoController::class, 'create'])->name('adicionar');
        Route::post('/adicionar', [ProdutoController::class, 'store'])->name('adicionar.store');
        Route::get('/produtos/{produto}/editar', [ProdutoController::class, 'edit'])->name('produtos.edit');
        Route::put('/produtos/{produto}', [ProdutoController::class, 'update'])->name('produtos.update');
        Route::delete('/produtos/{produto}', [ProdutoController::class, 'destroy'])->name('produtos.destroy');
    });
});
