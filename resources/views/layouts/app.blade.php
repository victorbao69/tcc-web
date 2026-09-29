<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PlanHome')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    {{-- Tema visual do PlanHome (cores e formas do app mobile). Vem por último para sobrescrever o Bootstrap. --}}
    <link href="{{ asset('css/planhome.css') }}" rel="stylesheet">
</head>

<body id="page-top">

    {{-- Quantidade de produtos diferentes no carrinho (a bolinha do menu) --}}
    @php $qtdCarrinho = count(session('carrinho', [])); @endphp

    <nav class="navbar navbar-expand-lg navbar-dark ph-navbar">
        <div class="container">

            <a class="ph-marca" href="{{ route('produtos') }}">
                <span class="ph-logo"></span> PlanHome
            </a>

            <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-lg-center gap-1">

                    <li class="nav-item">
                        <a class="ph-link {{ request()->routeIs('produtos') ? 'ativo' : '' }}" href="{{ route('produtos') }}">
                            <i class="bi bi-shop"></i> Produtos
                        </a>
                    </li>

                    @auth
                        @if(auth()->user()->ehEmpresa())
                            <li class="nav-item">
                                <a class="ph-link {{ request()->routeIs('empresas*', 'adicionar*', 'produtos.edit') ? 'ativo' : '' }}" href="{{ route('empresas') }}">
                                    <i class="bi bi-speedometer2"></i> Painel
                                </a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="ph-link {{ request()->routeIs('pedidos') ? 'ativo' : '' }}" href="{{ route('pedidos') }}">
                                    <i class="bi bi-receipt"></i> Meus Pedidos
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="ph-link {{ request()->routeIs('carrinho') ? 'ativo' : '' }}" href="{{ route('carrinho') }}">
                                    <i class="bi bi-cart3"></i> Carrinho
                                    @if($qtdCarrinho > 0)
                                        <span class="ph-contador">{{ $qtdCarrinho }}</span>
                                    @endif
                                </a>
                            </li>
                        @endif

                        <li class="nav-item">
                            <span class="ph-usuario">{{ auth()->user()->name }}</span>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="ph-link">
                                    <i class="bi bi-box-arrow-right"></i> Sair
                                </button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="ph-link {{ request()->routeIs('welcome', 'home') ? 'ativo' : '' }}" href="{{ route('welcome') }}">Entrar</a>
                        </li>
                        <li class="nav-item">
                            <a class="ph-link {{ request()->routeIs('cadastro') ? 'ativo' : '' }}" href="{{ route('cadastro') }}">Criar conta</a>
                        </li>
                    @endauth

                </ul>
            </div>

        </div>
    </nav>

    <div class="ph-main">

        @if(session('sucesso'))
            <div class="container mt-3">
                <div class="ph-alerta ph-alerta-sucesso mb-0">
                    <i class="bi bi-check-circle"></i> {{ session('sucesso') }}
                </div>
            </div>
        @endif

        @if(session('erro'))
            <div class="container mt-3">
                <div class="ph-alerta ph-alerta-erro mb-0">
                    <i class="bi bi-exclamation-circle"></i> {{ session('erro') }}
                </div>
            </div>
        @endif

        @yield('content')
    </div>

    <footer class="ph-rodape">
        <div class="container">
            © {{ date('Y') }} PlanHome · Móveis &amp; Decoração
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

</body>
</html>
