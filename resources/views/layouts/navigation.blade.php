<!-- Navegación Principal -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <!-- Logo y Marca -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <div class="me-2">
                <i class="fas fa-home"></i>
            </div>
            <div>
                <span class="fw-bold">Reservalo</span>
                <small class="d-block text-white-50" style="font-size: 0.7rem;">Tu lugar perfecto</small>
            </div>
        </a>

        <!-- Botón hamburguesa para móvil -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Contenido de la navegación -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <!-- Menú izquierdo -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                        <i class="fas fa-home me-1"></i>Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('properties.*') ? 'active' : '' }}" href="{{ route('properties.index') }}">
                        <i class="fas fa-search me-1"></i>Propiedades
                    </a>
                </li>
                
                @auth
                    <!-- Dashboard para usuarios autenticados -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    
                    <!-- Mis Reservas para usuarios autenticados -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reservations.*') ? 'active' : '' }}" href="{{ route('reservations.index') }}">
                            <i class="fas fa-calendar-check me-1"></i>Mis Reservas
                        </a>
                    </li>
                @endauth
                
                <!-- Dropdown Destinos -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-map-marker-alt me-1"></i>Destinos
                    </a>
                    <ul class="dropdown-menu">
                        <li><h6 class="dropdown-header text-primary fw-bold">🌍 Regiones Populares</h6></li>
                        @php
                            $popularDepartments = \App\Models\Department::active()->take(8)->get();
                        @endphp
                        @foreach($popularDepartments as $dept)
                            <li>
                                <a class="dropdown-item" href="{{ route('properties.index', ['department' => $dept->id]) }}">
                                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                    {{ $dept->name }}
                                </a>
                            </li>
                        @endforeach
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-primary fw-bold" href="{{ route('properties.index') }}">
                                <i class="fas fa-globe me-2"></i>Ver todos los destinos
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Dropdown Tipos -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-building me-1"></i>Tipos
                    </a>
                    <ul class="dropdown-menu">
                        <li><h6 class="dropdown-header text-primary fw-bold">🏠 Tipos de Propiedades</h6></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('properties.index', ['type' => 'casa']) }}">
                                <i class="fas fa-home me-2 text-success"></i>Casa
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('properties.index', ['type' => 'apartamento']) }}">
                                <i class="fas fa-building me-2 text-info"></i>Apartamento
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('properties.index', ['type' => 'cabaña']) }}">
                                <i class="fas fa-tree me-2 text-warning"></i>Cabaña
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('properties.index', ['type' => 'hotel']) }}">
                                <i class="fas fa-hotel me-2 text-danger"></i>Hotel
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('properties.index', ['type' => 'finca']) }}">
                                <i class="fas fa-seedling me-2 text-success"></i>Finca
                            </a>
                        </li>
                    </ul>
                </li>

                @auth
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-1"></i>Admin
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>

            <!-- Menú derecho -->
            <ul class="navbar-nav ms-auto">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-2" href="{{ route('register') }}">
                            <i class="fas fa-user-plus me-1"></i>Registrarse
                        </a>
                    </li>
                @else
                    <!-- Notificaciones para usuarios autenticados -->
                    <li class="nav-item dropdown me-2">
                        <a class="nav-link position-relative" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bell me-1"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                0
                            </span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header text-primary fw-bold">🔔 Notificaciones</h6></li>
                            <li>
                                <div class="dropdown-item text-center py-3">
                                    <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                    <p class="text-muted small mb-0">No tienes notificaciones</p>
                                </div>
                            </li>
                        </ul>
                    </li>
                    
                    <!-- Perfil del usuario -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-white bg-opacity-25 d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                                <span class="d-none d-md-inline">{{ Auth::user()->name }}</span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <div class="dropdown-header text-primary fw-bold">
                                    <i class="fas fa-user-circle me-2"></i>{{ Auth::user()->name }}
                                </div>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2 text-primary"></i>Dashboard
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-user-edit me-2 text-info"></i>Mi Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('reservations.index') }}">
                                    <i class="fas fa-calendar-check me-2 text-success"></i>Mis Reservas
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
