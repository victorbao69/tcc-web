@extends('layouts.app')

@section('title', 'Administração - PlanHome')

@section('content')

<div class="ph-topo">
    <div class="container">
        <h1>Administração</h1>
        <p>Exclua contas e produtos. A exclusão é definitiva: os dados saem do banco de dados.</p>
    </div>
</div>

<section class="py-4">
    <div class="container">

        {{-- ===== Usuários ===== --}}
        <h3 class="mb-3">Usuários ({{ $usuarios->count() }})</h3>
        <div class="table-responsive mb-5">
            <table class="table table-bordered table-sm align-middle bg-white">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Tipo</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usuarios as $usuario)
                        <tr>
                            <td>{{ $usuario->id }}</td>
                            <td>
                                {{ $usuario->name }}
                                @if($usuario->empresa)
                                    <small>(empresa: {{ $usuario->empresa->name }})</small>
                                @endif
                            </td>
                            <td>{{ $usuario->email }}</td>
                            <td>{{ $usuario->tipousuario }}</td>
                            <td>
                                @if($usuario->ehAdmin())
                                    -
                                @else
                                    <form action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST" class="m-0"
                                          onsubmit="return confirm('Excluir esta conta? Os produtos, pedidos e carrinho ligados a ela também serão apagados.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ===== Produtos ===== --}}
        <h3 class="mb-3">Produtos ({{ $produtos->count() }})</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-sm align-middle bg-white">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Categoria</th>
                        <th>Preço</th>
                        <th>Status</th>
                        <th>Empresa</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produtos as $produto)
                        <tr>
                            <td>{{ $produto->id }}</td>
                            <td>{{ $produto->nome }}</td>
                            <td>{{ $produto->categoria }}</td>
                            <td>R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                            <td>{{ $produto->status }}</td>
                            <td>{{ $produto->empresa->name ?? '-' }}</td>
                            <td>
                                <form action="{{ route('admin.produtos.destroy', $produto) }}" method="POST" class="m-0"
                                      onsubmit="return confirm('Excluir este produto? Ele sairá dos carrinhos e dos pedidos, e os clientes serão avisados.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Nenhum produto cadastrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</section>

@endsection
