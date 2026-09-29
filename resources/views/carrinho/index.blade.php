@extends('layouts.app')

@section('title', 'Carrinho - PlanHome')

@section('content')

<div class="ph-topo">
    <div class="container">
        <h1>Meu carrinho</h1>
        <p>Confira os itens antes de finalizar</p>
    </div>
</div>

<div class="container py-4">
    <div class="row g-4">

        <div class="col-lg-8">
            @forelse($itens as $item)
                <div class="ph-card ph-card-sm ph-item mb-3">

                    <div class="ph-item-info">
                        <div class="ph-item-nome">{{ $item['produto']->nome }}</div>
                        <div class="ph-item-detalhe">
                            R$ {{ number_format($item['produto']->preco, 2, ',', '.') }} por unidade
                        </div>
                    </div>

                    <div class="ph-qtd">
                        <form action="{{ route('carrinho.atualizar', $item['produto']) }}" method="POST" class="m-0">
                            @csrf
                            <input type="hidden" name="acao" value="diminuir">
                            <button type="submit" class="ph-icone-btn" title="Diminuir"><i class="bi bi-dash-circle"></i></button>
                        </form>
                        <span class="ph-qtd-num">{{ $item['quantidade'] }}</span>
                        <form action="{{ route('carrinho.atualizar', $item['produto']) }}" method="POST" class="m-0">
                            @csrf
                            <input type="hidden" name="acao" value="aumentar">
                            <button type="submit" class="ph-icone-btn" title="Aumentar"><i class="bi bi-plus-circle"></i></button>
                        </form>
                    </div>

                    <div class="ph-item-subtotal">
                        R$ {{ number_format($item['subtotal'], 2, ',', '.') }}
                    </div>

                    <form action="{{ route('carrinho.remover', $item['produto']) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="ph-icone-btn ph-icone-perigo" title="Remover">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>

                </div>
            @empty
                <div class="ph-card">
                    <div class="ph-vazio">
                        <i class="bi bi-cart3"></i>
                        Seu carrinho está vazio
                    </div>
                </div>
            @endforelse
        </div>

        <div class="col-lg-4">
            <div class="ph-card">

                <h5 class="ph-secao-titulo mb-3">Resumo do pedido</h5>

                <div class="ph-resumo-linha">
                    <span>Subtotal</span>
                    <span>R$ {{ number_format($total, 2, ',', '.') }}</span>
                </div>

                <div class="ph-resumo-linha">
                    <span>Frete</span>
                    <span style="color: #2F5A29;">Grátis</span>
                </div>

                <div class="ph-resumo-total">
                    <span>Total</span>
                    <strong>R$ {{ number_format($total, 2, ',', '.') }}</strong>
                </div>

                <form action="{{ route('carrinho.finalizar') }}" method="POST">
                    @csrf
                    <button type="submit" class="ph-btn ph-btn-primario ph-btn-bloco" {{ empty($itens) ? 'disabled' : '' }}>
                        Finalizar compra
                    </button>
                </form>

                <a href="{{ route('produtos') }}" class="ph-btn ph-btn-contorno ph-btn-bloco mt-2">
                    Continuar comprando
                </a>

            </div>
        </div>

    </div>
</div>

@endsection
