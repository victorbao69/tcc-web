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

    {{-- Avisos de sucesso/erro: ficam flutuando no topo da tela, então não empurram nem quebram o layout --}}
    @if(session('sucesso') || session('erro'))
        <div class="ph-toast-area">
            @if(session('sucesso'))
                <div class="ph-toast ph-toast-sucesso" role="alert">
                    <i class="bi bi-check-circle"></i>
                    <span>{{ session('sucesso') }}</span>
                    <button type="button" class="ph-toast-fechar" aria-label="Fechar">&times;</button>
                </div>
            @endif
            @if(session('erro'))
                <div class="ph-toast ph-toast-erro" role="alert">
                    <i class="bi bi-exclamation-circle"></i>
                    <span>{{ session('erro') }}</span>
                    <button type="button" class="ph-toast-fechar" aria-label="Fechar">&times;</button>
                </div>
            @endif
        </div>
    @endif

    <div class="ph-main">

        @yield('content')
    </div>

    <footer class="ph-rodape">
        <div class="container">
            © {{ date('Y') }} PlanHome · Móveis &amp; Decoração
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fecha os avisos no botão X ou sozinho depois de 5 segundos
        document.querySelectorAll('.ph-toast').forEach(function (aviso) {
            function fechar() { aviso.classList.add('ph-toast-saindo'); setTimeout(function () { aviso.remove(); }, 300); }
            aviso.querySelector('.ph-toast-fechar').addEventListener('click', fechar);
            setTimeout(fechar, 5000);
        });
    </script>
    @stack('scripts')

</body>
</html>
