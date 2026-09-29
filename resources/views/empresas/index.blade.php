@extends('layouts.app')

@section('title', 'Painel Empresa - PlanHome')

@section('content')

<div class="ph-topo">
    <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1>Painel da empresa</h1>
            <p>{{ $empresa->name }} · CNPJ: {{ $empresa->cnpj }}</p>
        </div>
        <a href="{{ route('empresas.edit') }}" class="ph-btn ph-btn-claro">
            <i class="bi bi-pencil"></i> Editar dados da empresa
        </a>
    </div>
</div>

<div class="container py-4">

    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="ph-card ph-estat">
                <div class="ph-estat-icone"><i class="bi bi-cash-stack"></i></div>
                <div>
                    <div class="ph-estat-rotulo">Total em vendas</div>
                    <div class="ph-estat-valor">R$ {{ number_format($totalVendas, 2, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="ph-card ph-estat">
                <div class="ph-estat-icone"><i class="bi bi-bag-check"></i></div>
                <div>
                    <div class="ph-estat-rotulo">Total de pedidos</div>
                    <div class="ph-estat-valor">{{ $totalPedidos }}</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="ph-card ph-estat">
                <div class="ph-estat-icone"><i class="bi bi-truck"></i></div>
                <div>
                    <div class="ph-estat-rotulo">Pedidos concluídos</div>
                    <div class="ph-estat-valor">{{ $totalConcluidos }}</div>
                </div>
            </div>
        </div>

    </div>

    <div class="ph-card mb-4">

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <h5 class="ph-secao-titulo">Meus produtos</h5>
            <a href="{{ route('adicionar') }}" class="ph-btn ph-btn-primario ph-btn-pequeno">
                + Adicionar produto
            </a>
        </div>

        <div class="table-responsive">
            <table class="ph-tabela">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produtos as $produto)
                        <tr>
                            <td class="fw-bold">{{ $produto->nome }}</td>
                            <td>{{ ucfirst($produto->categoria) }}</td>
                            <td>R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                            <td>
                                <span class="ph-selo ph-selo-{{ $produto->status }}">{{ ucfirst($produto->status) }}</span>
                            </td>
                            <td>
                                <a href="{{ route('produtos.edit', $produto) }}" class="ph-btn ph-btn-contorno ph-btn-pequeno">Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="color: var(--cinza-texto);">Nenhum produto cadastrado ainda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <div class="ph-card">

        <h5 class="ph-secao-titulo mb-3">Pedidos recebidos</h5>

        <div class="table-responsive">
            <table class="ph-tabela">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Produto</th>
                        <th>Cliente</th>
                        <th>Valor</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pedidos as $pedido)
                        <tr>
                            <td style="color: var(--cinza-texto);">{{ $pedido->id }}</td>
                            <td class="fw-bold">{{ $pedido->produto->nome }}</td>
                            <td>{{ $pedido->user->name }}</td>
                            <td style="color: var(--marrom-escuro); font-weight: 600;">
                                R$ {{ number_format($pedido->valor, 2, ',', '.') }}
                            </td>
                            <td>
                                <form action="{{ route('pedidos.update', $pedido) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="ph-input ph-input-pequeno" onchange="this.form.submit()">
                                        @foreach(['pendente', 'pago', 'enviado', 'concluido'] as $status)
                                            <option value="{{ $status }}" {{ $pedido->status == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="color: var(--cinza-texto);">Nenhum pedido recebido ainda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
@endsection
