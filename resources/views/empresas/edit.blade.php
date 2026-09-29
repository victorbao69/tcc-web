@extends('layouts.app')

@section('title', 'Editar Empresa - PlanHome')

@section('content')

<div class="ph-topo">
    <div class="container">
        <h1>Dados da empresa</h1>
        <p>Atualize o nome e o CNPJ</p>
    </div>
</div>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">
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

                <form method="POST" action="{{ route('empresas.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="ph-rotulo" for="name">Nome da empresa</label>
                        <div class="ph-campo">
                            <i class="bi bi-building"></i>
                            <input id="name" type="text" name="name" class="ph-input" value="{{ old('name', $empresa->name) }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="ph-rotulo" for="cnpj">CNPJ</label>
                        <div class="ph-campo">
                            <i class="bi bi-card-text"></i>
                            <input id="cnpj" type="text" name="cnpj" class="ph-input" value="{{ old('cnpj', $empresa->cnpj) }}" required>
                        </div>
                    </div>

                    <button type="submit" class="ph-btn ph-btn-primario ph-btn-bloco mb-2">
                        Salvar alterações
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
