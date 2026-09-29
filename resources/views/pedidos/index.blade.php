@extends('layouts.app')

@section('title', 'Meus Pedidos - PlanHome')

@section('content')

<div class="ph-topo">
    <div class="container">
        <h1>{{ auth()->user()->ehEmpresa() ? 'Pedidos recebidos' : 'Meus pedidos' }}</h1>
        <p>{{ auth()->user()->ehEmpresa() ? 'Acompanhe as compras dos seus produtos' : 'Acompanhe suas compras' }}</p>
    </div>
</div>

<div class="container py-4">

    @forelse($pedidos as $pedido)
        <div class="ph-card ph-card-sm ph-item mb-3">

            <div class="ph-item-info">
                <div class="ph-item-nome">Pedido #{{ $pedido->id }}</div>
                <div class="ph-item-detalhe">
                    {{ $pedido->produto->nome }}
                    @if(auth()->user()->ehEmpresa())
                        · Cliente: {{ $pedido->user->name }}
                    @endif
                </div>
            </div>

            <span class="ph-selo ph-selo-{{ $pedido->status }}">{{ ucfirst($pedido->status) }}</span>

            <div class="ph-item-subtotal">
                R$ {{ number_format($pedido->valor, 2, ',', '.') }}
            </div>

            @if(! auth()->user()->ehEmpresa() && $pedido->status === 'pendente')
                <form action="{{ route('pedidos.destroy', $pedido) }}" method="POST" class="m-0" onsubmit="return confirm('Cancelar este pedido?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="ph-btn ph-btn-perigo ph-btn-pequeno">Cancelar</button>
                </form>
            @endif

        </div>
    @empty
        <div class="ph-card">
            <div class="ph-vazio">
                <i class="bi bi-receipt"></i>
                Nenhum pedido encontrado.
            </div>
        </div>
    @endforelse

</div>

@endsection
