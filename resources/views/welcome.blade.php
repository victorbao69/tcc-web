@extends('layouts.app')

@section('title', 'Login - PlanHome')

@section('content')

<div class="ph-auth">
    <div class="ph-auth-cartao">

        <div class="ph-auth-topo">
            <span class="ph-logo ph-logo-grande"></span>
            <h1>Bem-vindo<br>de volta.</h1>
            <p>Acesse sua conta para continuar</p>
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

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <label class="ph-rotulo" for="email">E-mail</label>
                <div class="ph-campo mb-3">
                    <i class="bi bi-envelope"></i>
                    <input id="email" class="ph-input" type="email" name="email"
                           value="{{ old('email') }}" placeholder="seu@email.com" required>
                </div>

                <label class="ph-rotulo" for="password">Senha</label>
                <div class="ph-campo mb-4">
                    <i class="bi bi-lock"></i>
                    <input id="password" class="ph-input" type="password" name="password"
                           placeholder="••••••••" required>
                </div>

                <button type="submit" class="ph-btn ph-btn-primario ph-btn-bloco">Entrar</button>
            </form>

            <div class="mt-3 text-center">
                <a href="{{ route('cadastro') }}">Não tem conta? Cadastre-se</a>
            </div>

        </div>
    </div>
</div>

@endsection
