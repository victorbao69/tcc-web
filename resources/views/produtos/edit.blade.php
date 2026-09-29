@extends('layouts.app')

@section('title', 'Editar Produto - PlanHome')

@section('content')

<div class="ph-topo">
    <div class="container">
        <h1>Editar produto</h1>
        <p>Altere as informações e salve</p>
    </div>
</div>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="ph-card">

                @if ($errors->any())
                    <div class="ph-alerta ph-alerta-erro">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('produtos.update', $produto) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="ph-rotulo" for="nome">Nome do produto</label>
                        <input id="nome" type="text" name="nome" class="ph-input" value="{{ old('nome', $produto->nome) }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="ph-rotulo" for="descricao">Descrição</label>
                        <textarea id="descricao" name="descricao" class="ph-input" rows="3" maxlength="100">{{ old('descricao', $produto->descricao) }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="ph-rotulo" for="preco">Preço (R$)</label>
                            <input id="preco" type="number" name="preco" class="ph-input" value="{{ old('preco', $produto->preco) }}" step="0.01" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="ph-rotulo" for="categoria">Categoria</label>
                            <select id="categoria" name="categoria" class="ph-input" required>
                                @foreach(['sofa' => 'Sofá', 'mesa' => 'Mesa', 'cadeira' => 'Cadeira', 'cama' => 'Cama', 'armario' => 'Armário'] as $valor => $label)
                                    <option value="{{ $valor }}" {{ old('categoria', $produto->categoria) == $valor ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="ph-rotulo" for="urlimagem">URL da imagem</label>
                        <input id="urlimagem" type="text" name="urlimagem" class="ph-input" value="{{ old('urlimagem', $produto->urlimagem) }}">
                    </div>

                    <div class="mb-4">
                        <label class="ph-rotulo" for="status">Status</label>
                        <select id="status" name="status" class="ph-input">
                            <option value="ativo" {{ old('status', $produto->status) == 'ativo' ? 'selected' : '' }}>Ativo</option>
                            <option value="vendido" {{ old('status', $produto->status) == 'vendido' ? 'selected' : '' }}>Vendido</option>
                        </select>
                    </div>

                    <button type="submit" class="ph-btn ph-btn-primario ph-btn-bloco mb-2">
                        Salvar alterações
                    </button>
                </form>

                <form method="POST" action="{{ route('produtos.destroy', $produto) }}" onsubmit="return confirm('Remover este produto?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="ph-btn ph-btn-perigo ph-btn-bloco mb-2">
                        <i class="bi bi-trash"></i> Excluir produto
                    </button>
                </form>

                <a href="{{ route('empresas') }}" class="ph-btn ph-btn-contorno ph-btn-bloco">
                    Voltar
                </a>

            </div>
        </div>
    </div>
</div>

@endsection
