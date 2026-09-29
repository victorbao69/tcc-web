@extends('layouts.app')

@section('title', 'Cadastro - PlanHome')

@section('content')

<div class="ph-auth">
    <div class="ph-auth-cartao">

        <div class="ph-auth-topo">
            <span class="ph-logo ph-logo-grande"></span>
            <h1>Crie sua<br>conta.</h1>
            <p>Cadastre-se para acessar o PlanHome</p>
        </div>

        <div class="ph-auth-corpo">

            @if ($errors->any())
                <div class="ph-alerta ph-alerta-erro">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('cadastro.store') }}">
                @csrf

                <label class="ph-rotulo" for="name">Nome completo</label>
                <div class="ph-campo mb-3">
                    <i class="bi bi-person"></i>
                    <input id="name" class="ph-input" type="text" name="name"
                           value="{{ old('name') }}" placeholder="Seu nome" required>
                </div>

                <label class="ph-rotulo" for="email">E-mail</label>
                <div class="ph-campo mb-3">
                    <i class="bi bi-envelope"></i>
                    <input id="email" class="ph-input" type="email" name="email"
                           value="{{ old('email') }}" placeholder="seu@email.com" required>
                </div>

                <label class="ph-rotulo" for="password">Senha</label>
                <div class="ph-campo mb-3">
                    <i class="bi bi-lock"></i>
                    <input id="password" class="ph-input" type="password" name="password"
                           placeholder="••••••••" required>
                </div>

                <label class="ph-rotulo" for="password_confirmation">Confirmar senha</label>
                <div class="ph-campo mb-3">
                    <i class="bi bi-lock"></i>
                    <input id="password_confirmation" class="ph-input" type="password" name="password_confirmation"
                           placeholder="••••••••" required>
                </div>

                <label class="ph-rotulo" for="tipousuario">Tipo de conta</label>
                <select class="ph-input mb-3" name="tipousuario" id="tipousuario" onchange="mostrarCamposEmpresa()" required>
                    <option value="cliente" {{ old('tipousuario') == 'cliente' ? 'selected' : '' }}>Cliente</option>
                    <option value="empresa" {{ old('tipousuario') == 'empresa' ? 'selected' : '' }}>Empresa</option>
                </select>

                <div id="camposEmpresa" style="display: {{ old('tipousuario') == 'empresa' ? 'block' : 'none' }};">
                    <label class="ph-rotulo" for="empresa_nome">Nome da empresa</label>
                    <div class="ph-campo mb-3">
                        <i class="bi bi-building"></i>
                        <input id="empresa_nome" class="ph-input" type="text" name="empresa_nome"
                               value="{{ old('empresa_nome') }}" placeholder="Nome da empresa">
                    </div>

                    <label class="ph-rotulo" for="empresa_cnpj">CNPJ</label>
                    <div class="ph-campo mb-3">
                        <i class="bi bi-card-text"></i>
                        <input id="empresa_cnpj" class="ph-input" type="text" name="empresa_cnpj"
                               value="{{ old('empresa_cnpj') }}" placeholder="00.000.000/0000-00">
                    </div>
                </div>

                <button type="submit" class="ph-btn ph-btn-primario ph-btn-bloco mt-2">Cadastrar</button>
            </form>

            <div class="mt-3 text-center">
                <a href="{{ route('welcome') }}">Já tenho conta</a>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function mostrarCamposEmpresa() {
    const tipo = document.getElementById('tipousuario').value;
    document.getElementById('camposEmpresa').style.display = tipo === 'empresa' ? 'block' : 'none';
}
</script>
@endpush
