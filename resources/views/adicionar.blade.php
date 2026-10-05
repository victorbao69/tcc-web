@extends('layouts.app')

@section('title', 'Adicionar Produto - PlanHome')

@section('content')

<div class="ph-topo">
    <div class="container">
        <h1>Adicionar produto</h1>
        <p>Preencha as informações do novo produto</p>
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

                <form method="POST" action="{{ route('adicionar.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="ph-rotulo" for="nome">Nome do produto</label>
                        <input id="nome" type="text" name="nome" class="ph-input" value="{{ old('nome') }}" placeholder="Ex: Sofá Conforto" required>
                    </div>

                    <div class="mb-3">
                        <label class="ph-rotulo" for="descricao">Descrição</label>
                        <textarea id="descricao" name="descricao" class="ph-input" rows="3" maxlength="100"
                                  placeholder="Descreva o produto...">{{ old('descricao') }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="ph-rotulo" for="preco">Preço (R$)</label>
                            <input id="preco" type="number" name="preco" class="ph-input" value="{{ old('preco') }}" placeholder="0,00" step="0.01" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="ph-rotulo" for="categoria">Categoria</label>
                            <select id="categoria" name="categoria" class="ph-input" required>
                                <option value="">Selecione...</option>
                                <option value="sofa" {{ old('categoria') == 'sofa' ? 'selected' : '' }}>Sofá</option>
                                <option value="mesa" {{ old('categoria') == 'mesa' ? 'selected' : '' }}>Mesa</option>
                                <option value="cadeira" {{ old('categoria') == 'cadeira' ? 'selected' : '' }}>Cadeira</option>
                                <option value="cama" {{ old('categoria') == 'cama' ? 'selected' : '' }}>Cama</option>
                                <option value="armario" {{ old('categoria') == 'armario' ? 'selected' : '' }}>Armário</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="ph-rotulo" for="imagem">Imagem do produto</label>
                        <input id="imagem" type="file" name="imagem" class="ph-input" accept="image/png,image/jpeg,image/webp">
                        <small style="color: var(--cinza-texto);">JPG, PNG ou WEBP, até 4 MB.</small>
                        <img id="previaImagem" alt="Prévia da imagem" class="ph-produto-img mt-2" style="display: none; max-width: 240px;">
                    </div>

                    <div class="mb-3">
                        <label class="ph-rotulo" for="urlimagem">Ou use o link de uma imagem (opcional)</label>
                        <input id="urlimagem" type="text" name="urlimagem" class="ph-input" value="{{ old('urlimagem') }}" placeholder="https://...">
                    </div>

                    <div class="mb-4">
                        <label class="ph-rotulo" for="status">Status</label>
                        <select id="status" name="status" class="ph-input">
                            <option value="ativo">Ativo</option>
                            <option value="vendido">Vendido</option>
                        </select>
                    </div>

                    <button type="submit" class="ph-btn ph-btn-primario ph-btn-bloco mb-2">
                        Adicionar produto
                    </button>
                </form>

                <a href="{{ route('empresas') }}" class="ph-btn ph-btn-contorno ph-btn-bloco">
                    Voltar
                </a>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Mostra a prévia da imagem escolhida antes de enviar
    document.getElementById('imagem').addEventListener('change', function () {
        var previa = document.getElementById('previaImagem');
        if (this.files && this.files[0]) {
            previa.src = URL.createObjectURL(this.files[0]);
            previa.style.display = 'block';
        } else {
            previa.style.display = 'none';
        }
    });
</script>
@endpush

@endsection
