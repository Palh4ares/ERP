<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Caderneta Digital')</title>
    
    <!-- Bootstrap 5 + Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
        }
        
        body {
            font-family: 'Nunito', 'Segoe UI', sans-serif;
            background-color: #f8f9fc;
        }
        
        .navbar {
            height: 100px;
            background: linear-gradient(35deg, #ffffff 0%, #9e9e9e 100%);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        #userDropdown {
            color: #000 !important;
            font-weight: 500;
        }
        
        .sidebar {
            min-height: calc(100vh - 56px);
            background: white;
            box-shadow: 2px 0 5px rgba(0,0,0,0.05);
        }
        
        .sidebar .nav-link {
            color: #333;
            padding: 0.75rem 1rem;
            transition: all 0.3s;
            border-radius: 8px;
            margin: 0 10px 5px 10px;
        }
        
        .sidebar .nav-link:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateX(5px);
        }
        
        .sidebar .nav-link.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar .nav-header {
            padding: 0.75rem 1rem;
            margin: 0 10px 5px 10px;
            font-size: 0.8rem;
            text-transform: uppercase;
            font-weight: bold;
            color: #666;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .card-stats {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s;
            overflow: hidden;
        }
        
        .card-stats:hover {
            transform: translateY(-5px);
        }
        
        .card-stats .card-body {
            padding: 1.5rem;
        }
        
        .card-stats i {
            font-size: 2.5rem;
            opacity: 0.3;
        }
        
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            color: white;
        }
        
        .table-responsive {
            border-radius: 15px;
            overflow: hidden;
        }
        
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 1rem;
        }
        
        .badge-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .progress {
            height: 8px;
            border-radius: 4px;
        }
        
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -280px;
                transition: all 0.3s;
                z-index: 1000;
                width: 280px;
            }
            
            .sidebar.show {
                left: 0;
            }
            
            main {
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
    @auth
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <button class="btn btn-link text-white me-2 d-md-none" type="button" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                
                <img src="{{ asset('images/logo.png') }}" alt="CredFácil" height="80">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            @if(auth()->user() && auth()->user()->avatar)
                                <img src="{{ asset('storage/' . auth()->user()->avatar) }}" class="avatar me-1">
                            @else
                                <i class="bi bi-person-circle"></i>
                            @endif
                            {{ auth()->user()->name ?? 'Usuário' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('perfil.edit') }}"><i class="bi bi-person"></i> Meu Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="bi bi-box-arrow-right"></i> Sair
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-md-block sidebar p-0" id="sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <!-- DASHBOARD -->
                        <li class="nav-header">NAVEGAÇÃO</li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        
                        <!-- CLIENTES -->
                        <li class="nav-header">CADASTROS</li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('clientes.index') ? 'active' : '' }}" href="{{ route('clientes.index') }}">
                                <i class="bi bi-people"></i> Listar Clientes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('clientes.create') ? 'active' : '' }}" href="{{ route('clientes.create') }}">
                                <i class="bi bi-person-plus"></i> Novo Cliente
                            </a>
                        </li>
                        
                        <!-- VENDAS -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('vendas.index') ? 'active' : '' }}" href="{{ route('vendas.index') }}">
                                <i class="bi bi-cart"></i> Listar Vendas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('vendas.create') ? 'active' : '' }}" href="{{ route('vendas.create') }}">
                                <i class="bi bi-cart-plus"></i> Nova Venda
                            </a>
                        </li>
                        
                        <!-- PAGAMENTOS -->
                        <li class="nav-header">FINANCEIRO</li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#submenuPagamentos">
                                <i class="bi bi-cash-stack"></i> Pagamentos
                                <i class="bi bi-chevron-down float-end"></i>
                            </a>
                            <div class="collapse" id="submenuPagamentos">
                                <ul class="nav flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link" href="{{ route('relatorios.pagamentos') }}">
                                            <i class="bi bi-graph-up"></i> Histórico de Pagamentos
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                        <!-- RELATÓRIOS -->
                        <li class="nav-header">RELATÓRIOS</li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#submenuRelatorios">
                                <i class="bi bi-graph-up"></i> Relatórios
                                <i class="bi bi-chevron-down float-end"></i>
                            </a>
                            <div class="collapse" id="submenuRelatorios">
                                <ul class="nav flex-column ms-3">
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('relatorios.vendas') ? 'active' : '' }}" href="{{ route('relatorios.vendas') }}">
                                            <i class="bi bi-cart-check"></i> Relatório de Vendas
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('relatorios.clientes') ? 'active' : '' }}" href="{{ route('relatorios.clientes') }}">
                                            <i class="bi bi-people"></i> Relatório de Clientes
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link {{ request()->routeIs('relatorios.pagamentos') ? 'active' : '' }}" href="{{ route('relatorios.pagamentos') }}">
                                            <i class="bi bi-cash-stack"></i> Relatório de Pagamentos
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        
                        <!-- CONFIGURAÇÕES -->
                        <li class="nav-header">CONTA</li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('perfil.edit') ? 'active' : '' }}" href="{{ route('perfil.edit') }}">
                                <i class="bi bi-person"></i> Meu Perfil
                            </a>
                        </li>
                        
                        <!-- @if(auth()->user() && auth()->user()->tipo == 'admin')
                        <li class="nav-header">ADMINISTRAÇÃO</li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-people"></i> Usuários
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <i class="bi bi-sliders"></i> Configurações
                            </a>
                        </li>
                        @endif -->
                        
                        <hr class="my-3">
                        
                        <!-- SAIR -->
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                @csrf
                                <button type="submit" class="nav-link text-danger" style="background: none; border: none; width: 100%; text-align: left;">
                                    <i class="bi bi-box-arrow-right"></i> Sair do Sistema
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <!-- Main Content -->
            <main class="col-md-10 ms-sm-auto px-md-4">
                <div class="pt-3 pb-2 mb-3 border-bottom">
                    @yield('header')
                </div>
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                
                <div class="fade-in">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    @else
        <!-- Páginas de autenticação (login, register) não precisam do menu -->
        <div class="container mt-5">
            @yield('content')
        </div>
    @endauth
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        // Tooltips automáticos
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
        
        // Sidebar toggle para mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('show');
            });
        }
        
        // Fechar sidebar ao clicar fora (mobile)
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebarToggle');
            if (window.innerWidth < 768 && sidebar && toggle && sidebar.classList.contains('show')) {
                if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                    sidebar.classList.remove('show');
                }
            }
        });
        
        // Confirmação de exclusão
        window.confirmDelete = function(formId, message = 'Tem certeza que deseja excluir este registro?') {
            Swal.fire({
                title: 'Confirmar exclusão',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, excluir!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
        
        // Inicializar tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>