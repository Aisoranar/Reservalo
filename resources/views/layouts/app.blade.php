<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="user-authenticated" content="{{ auth()->check() ? 'true' : 'false' }}">

        <title>@yield('title', \App\Models\SystemSetting::get('site_name', config('app.name', 'Laravel')))</title>

        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <!-- Estilos personalizados básicos -->
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background-color: #f8f9fa;
            }
            
            .navbar {
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                padding: 0.5rem 0;
            }
            
            .navbar-brand {
                font-weight: bold;
                font-size: 1.2rem;
                color: white !important;
                transition: all 0.3s ease;
            }
            
            .navbar-brand:hover {
                transform: scale(1.02);
                text-shadow: 0 0 15px rgba(255,255,255,0.4);
            }
            
            .navbar-nav .nav-link {
                color: rgba(255,255,255,0.9) !important;
                font-weight: 500;
                padding: 0.5rem 0.75rem !important;
                border-radius: 6px;
                margin: 0 0.15rem;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
                font-size: 0.9rem;
            }
            
            .navbar-nav .nav-link::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transition: left 0.5s;
            }
            
            .navbar-nav .nav-link:hover::before {
                left: 100%;
            }
            
            .navbar-nav .nav-link:hover {
                color: white !important;
                background-color: rgba(255,255,255,0.2);
                transform: translateY(-1px);
                box-shadow: 0 3px 10px rgba(0,0,0,0.15);
            }
            
            .navbar-nav .nav-link.active {
                color: white !important;
                background-color: rgba(255,255,255,0.3);
                font-weight: 600;
                box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            }
            
            .dropdown-menu {
                border: none;
                box-shadow: 0 8px 25px rgba(0,0,0,0.12);
                border-radius: 10px;
                padding: 0.4rem 0;
                margin-top: 0.3rem;
                animation: fadeInDown 0.3s ease;
                min-width: 200px;
            }
            
            /* Estilos para submenús desplegables */
            .dropdown-submenu {
                position: relative;
            }
            
            .dropdown-submenu .dropdown-menu {
                top: 0;
                left: 100%;
                margin-top: -1px;
                margin-left: 0.125rem;
                min-width: 200px;
                border-radius: 8px;
                box-shadow: 0 8px 25px rgba(0,0,0,0.15);
                background: rgba(255,255,255,0.98);
                backdrop-filter: blur(15px);
                border: 1px solid rgba(0,0,0,0.08);
            }
            
            .dropdown-submenu:hover .dropdown-menu {
                display: block;
            }
            
            .dropdown-submenu .dropdown-toggle::after {
                transform: rotate(-90deg);
                margin-left: 0.5rem;
            }
            
            /* Estilos especiales para el menú de Super Admin */
            .navbar-nav .nav-link[href="#"]:hover {
                background-color: rgba(255,255,255,0.15);
            }
            
            .dropdown-menu .dropdown-header {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                font-weight: 600;
                padding: 0.75rem 1rem;
                margin: -0.4rem 0 0.5rem 0;
                border-radius: 8px 8px 0 0;
            }
            
            .dropdown-menu .dropdown-item {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
                transition: all 0.2s ease;
                border-radius: 4px;
                margin: 0.1rem 0.5rem;
            }
            
            .dropdown-menu .dropdown-item:hover {
                background-color: rgba(102, 126, 234, 0.1);
                color: #667eea;
                transform: translateX(5px);
            }
            
            .dropdown-menu .dropdown-item i {
                width: 20px;
                text-align: center;
            }
            
            @keyframes fadeInDown {
                from {
                    opacity: 0;
                    transform: translateY(-8px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .dropdown-item {
                padding: 0.5rem 1.2rem;
                transition: all 0.2s ease;
                border-radius: 6px;
                margin: 0 0.3rem;
                position: relative;
                font-size: 0.9rem;
            }
            
            .dropdown-item:hover {
                background-color: #f8f9fa;
                transform: translateX(3px);
                box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            }
            
            .dropdown-header {
                font-weight: 600;
                color: #667eea !important;
                padding: 0.5rem 1.2rem;
                border-bottom: 1px solid #e9ecef;
                font-size: 0.8rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }
            
            .btn-primary {
                background: rgba(255,255,255,0.2);
                border: 1px solid rgba(255,255,255,0.3);
                color: white;
                font-weight: 500;
                padding: 0.5rem 1.2rem;
                border-radius: 6px;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
                font-size: 0.9rem;
            }
            
            .btn-primary::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
                transition: left 0.5s;
            }
            
            .btn-primary:hover::before {
                left: 100%;
            }
            
            .btn-primary:hover {
                background: rgba(255,255,255,0.3);
                border-color: rgba(255,255,255,0.5);
                transform: translateY(-1px);
                box-shadow: 0 3px 10px rgba(0,0,0,0.15);
            }
            
            .navbar-toggler {
                border: 1px solid rgba(255,255,255,0.3);
                color: white;
                transition: all 0.3s ease;
                padding: 0.25rem 0.5rem;
            }
            
            .navbar-toggler:hover {
                border-color: rgba(255,255,255,0.6);
                transform: scale(1.02);
            }
            
            .navbar-toggler:focus {
                box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.25);
            }
            
            /* Responsive mejorado */
            @media (max-width: 991.98px) {
                .navbar-collapse {
                    background: rgba(255,255,255,0.1);
                    backdrop-filter: blur(10px);
                    border-radius: 10px;
                    padding: 0.8rem;
                    margin-top: 0.8rem;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
                }
                
                .navbar-nav .nav-link {
                    margin: 0.2rem 0;
                    text-align: center;
                    border-radius: 6px;
                }
                
                .dropdown-menu {
                    background: rgba(255,255,255,0.95);
                    margin: 0.3rem 0;
                    box-shadow: none;
                }
                
                .navbar-brand {
                    font-size: 1.1rem;
                }
                
                .navbar-brand small {
                    display: none;
                }
            }
            
            /* Animaciones para elementos activos */
            .nav-link.active::after {
                content: '';
                position: absolute;
                bottom: 0;
                left: 50%;
                transform: translateX(-50%);
                width: 20px;
                height: 2px;
                background: white;
                border-radius: 1px;
                animation: slideIn 0.3s ease;
            }
            
            @keyframes slideIn {
                from {
                    width: 0;
                }
                to {
                    width: 20px;
                }
            }
            
            /* Mejoras para el logo */
            .navbar-brand i {
                transition: all 0.3s ease;
                font-size: 1.5rem;
            }
            
            .navbar-brand:hover i {
                transform: rotate(3deg) scale(1.05);
            }
            
            /* Ajustes adicionales para compactar */
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            .navbar-nav {
                align-items: center;
            }
            
            .dropdown-toggle::after {
                margin-left: 0.3rem;
            }
        </style>

        <style>
        /* Estilos para el Dashboard del Admin */
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .admin-header .header-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .admin-header h1 {
            color: white;
            margin: 0;
        }

        .admin-header p {
            color: rgba(255, 255, 255, 0.8);
            margin: 0;
        }

        .header-actions .btn {
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            transition: all 0.3s ease;
        }

        .header-actions .btn:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid #f8f9fa;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .stat-card .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            margin-bottom: 1rem;
        }

        .stat-card .stat-icon.users {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-card .stat-icon.properties {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-card .stat-icon.reservations {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stat-card .stat-icon.pending {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .stat-card .stat-icon.warning {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .stat-card .stat-icon.info {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
            color: #333;
        }

        .stat-card .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .stat-card .stat-label {
            color: #718096;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .deactivated-users-section .stat-card {
            border-left: 4px solid #f6ad55;
        }

        .quick-action-card {
            display: block;
            text-decoration: none;
            color: inherit;
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            height: 100%;
        }

        .quick-action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: #cbd5e0;
            text-decoration: none;
            color: inherit;
        }

        .quick-action-card .action-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            margin-bottom: 1rem;
        }

        .quick-action-card h6 {
            color: #2d3748;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .quick-action-card p {
            color: #718096;
            font-size: 0.85rem;
            margin: 0;
        }

        .nav-links {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .nav-links .nav-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            background: #f7fafc;
            border-radius: 8px;
            color: #4a5568;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
        }

        .nav-links .nav-link:hover {
            background: #edf2f7;
            color: #2d3748;
            text-decoration: none;
            transform: translateX(5px);
        }

        .nav-links .nav-link i {
            width: 20px;
            margin-right: 0.75rem;
            color: #667eea;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .admin-header {
                padding: 1.5rem;
                text-align: center;
            }

            .admin-header .header-actions {
                margin-top: 1rem;
                text-align: center;
            }

            .stat-card {
                padding: 1rem;
            }

            .stat-card .stat-number {
                font-size: 1.5rem;
            }

            .quick-action-card {
                padding: 1rem;
                text-align: center;
            }
        }
    </style>
</head>
    <body>
        @include('layouts.navigation')

        <!-- Mensajes de alerta -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show m-3" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Error:</strong> Por favor corrige los siguientes errores:
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="container py-3">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        <!-- jQuery -->
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
        
        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        
        <!-- Location Selector JS -->
        <script src="{{ asset('js/location-selector.js') }}"></script>
        
        <!-- Scripts personalizados -->
        @stack('scripts')
        
        <!-- Estilos personalizados -->
        @stack('styles')
        
        <!-- Script para submenús desplegables -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Manejar submenús desplegables
                const submenuToggles = document.querySelectorAll('.dropdown-submenu .dropdown-toggle');
                
                submenuToggles.forEach(function(toggle) {
                    toggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const submenu = this.nextElementSibling;
                        const isOpen = submenu.style.display === 'block';
                        
                        // Cerrar todos los submenús
                        document.querySelectorAll('.dropdown-submenu .dropdown-menu').forEach(function(menu) {
                            menu.style.display = 'none';
                        });
                        
                        // Abrir/cerrar el submenú actual
                        if (!isOpen) {
                            submenu.style.display = 'block';
                        }
                    });
                });
                
                // Cerrar submenús al hacer clic fuera
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('.dropdown-submenu')) {
                        document.querySelectorAll('.dropdown-submenu .dropdown-menu').forEach(function(menu) {
                            menu.style.display = 'none';
                        });
                    }
                });
            });
        </script>
    </body>
</html>
