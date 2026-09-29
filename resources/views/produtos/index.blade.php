@extends('layouts.app')

@section('title', 'Produtos - PlanHome')

@section('content')

<div class="ph-topo">
    <div class="container">
        <h1>Nossos móveis</h1>
        <p>Escolha os melhores produtos para sua casa</p>
    </div>
</div>

<section class="py-4">
    <div class="container">
        <div class="row g-3">

            @forelse($produtos as $produto)
                <div class="col-6 col-md-4 col-lg-3">
                    <div class="ph-produto">

                        <img class="ph-produto-img" alt="{{ $produto->nome }}"
                             src="{{ $produto->urlimagem ?: 'https://placehold.co/400x300/F7E9D4/5F3A1A?text=' . urlencode($produto->nome) }}">

                        <h5 class="ph-produto-nome">{{ $produto->nome }}</h5>
                        <p class="ph-produto-desc">{{ Str::limit($produto->descricao, 80) }}</p>
                        <p class="ph-produto-vendedor">Vendido por: {{ $produto->empresa->name }}</p>
                        <p class="ph-preco">R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>

                        <div class="ph-produto-acoes">
                            <button type="button" class="ph-btn ph-btn-contorno ph-btn-pequeno"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalProduto{{ $produto->id }}">
                                Detalhes
                            </button>

                            @auth
                                @if(! auth()->user()->ehEmpresa())
                                    <form action="{{ route('carrinho.adicionar', $produto) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="ph-btn ph-btn-primario ph-btn-pequeno">
                                            + Adicionar
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('welcome') }}" class="ph-btn ph-btn-primario ph-btn-pequeno">
                                    Entrar para comprar
                                </a>
                            @endauth
                        </div>

                    </div>
                </div>

                <div class="modal fade ph-modal" id="modalProduto{{ $produto->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">{{ $produto->nome }}</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body p-4">
                                <img class="ph-modal-img" alt="{{ $produto->nome }}"
                                     src="{{ $produto->urlimagem ?: 'https://placehold.co/400x300/F7E9D4/5F3A1A?text=' . urlencode($produto->nome) }}">

                                <span class="ph-selo mb-3">{{ ucfirst($produto->categoria) }}</span>

                                <p class="mt-2" style="color: var(--cinza-texto); line-height: 1.5;">{{ $produto->descricao }}</p>

                                <div class="d-flex align-items-center justify-content-between mt-3">
                                    <span class="ph-preco m-0" style="font-size: 1.5rem;">
                                        R$ {{ number_format($produto->preco, 2, ',', '.') }}
                                    </span>

                                    @auth
                                        @if(! auth()->user()->ehEmpresa())
                                            <form action="{{ route('carrinho.adicionar', $produto) }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="ph-btn ph-btn-primario">
                                                    <i class="bi bi-cart3"></i> Adicionar ao carrinho
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <a href="{{ route('welcome') }}" class="ph-btn ph-btn-primario">Entrar para comprar</a>
                                    @endauth
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="ph-vazio">
                        <i class="bi bi-inbox"></i>
                        Nenhum produto disponível no momento.
                    </div>
                </div>
            @endforelse

        </div>
    </div>
</section>

@endsection
