<!-- Navegación Principal -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <!-- Logo y Marca -->
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            <div class="me-2">
                <i class="fas fa-home"></i>
            </div>
            <div>
                <span class="fw-bold">{{ \App\Models\SystemSetting::get('site_name', 'Reservalo') }}</span>
                <small class="d-block text-white-50" style="font-size: 0.7rem;">{{ \App\Models\SystemSetting::get('site_description', 'Tu lugar perfecto') }}</small>
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
                    
                    <!-- Mis Favoritos para usuarios autenticados -->
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('favorites.*') ? 'active' : '' }}" href="{{ route('favorites.index') }}">
                            <i class="fas fa-heart me-1"></i>Mis Favoritos
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
                    @if(auth()->user()->isSuperAdmin())
                        <!-- Menú especial para Super Administrador -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-crown me-1 text-warning"></i>Super Admin
                            </a>
                            <ul class="dropdown-menu dropdown-menu-lg-end">
                                <li><h6 class="dropdown-header text-warning fw-bold">👑 Panel de Super Administración</h6></li>
                                
                                <!-- Gestión del Sistema -->
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-cogs me-2 text-primary"></i>Sistema
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('superadmin.dashboard') }}">
                                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('superadmin.settings') }}">
                                            <i class="fas fa-sliders-h me-2"></i>Configuraciones
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('superadmin.audit-logs') }}">
                                            <i class="fas fa-history me-2"></i>Auditoría
                                        </a></li>
                                    </ul>
                                </li>
                                
                                <!-- Gestión de Usuarios -->
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-users me-2 text-success"></i>Usuarios
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('superadmin.users') }}">
                                            <i class="fas fa-list me-2"></i>Lista de Usuarios
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('superadmin.roles') }}">
                                            <i class="fas fa-user-tag me-2"></i>Roles
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('superadmin.permissions') }}">
                                            <i class="fas fa-key me-2"></i>Permisos
                                        </a></li>
                                    </ul>
                                </li>
                                
                                <!-- Gestión de Reservas -->
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-calendar-check me-2 text-warning"></i>Reservas
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('superadmin.reservations') }}">
                                            <i class="fas fa-list me-2"></i>Todas las Reservas
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('superadmin.reservations.create') }}">
                                            <i class="fas fa-plus me-2"></i>Crear Reserva
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('superadmin.reservations.pending') }}">
                                            <i class="fas fa-clock me-2"></i>Pendientes
                                            @if(\App\Models\Reservation::where('status', 'pending')->count() > 0)
                                                <span class="badge bg-danger ms-1">{{ \App\Models\Reservation::where('status', 'pending')->count() }}</span>
                                            @endif
                                        </a></li>
                                    </ul>
                                </li>
                                
                                <!-- Gestión de Membresías -->
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-gem me-2 text-info"></i>Membresías
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('superadmin.membership-plans') }}">
                                            <i class="fas fa-list-alt me-2"></i>Planes
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('superadmin.memberships') }}">
                                            <i class="fas fa-users-cog me-2"></i>Gestionar
                                        </a></li>
                                    </ul>
                                </li>
                                
                                <!-- Plantillas de Correo -->
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('superadmin.email-templates*') ? 'active' : '' }}" href="{{ route('superadmin.email-templates') }}">
                                        <i class="fas fa-envelope me-1"></i>Plantillas de Correo
                                    </a>
                                </li>
                                
                                <!-- Reportes -->
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-chart-bar me-2 text-warning"></i>Reportes
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="{{ route('superadmin.reports') }}">
                                            <i class="fas fa-chart-line me-2"></i>Generales
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('superadmin.reports', ['type' => 'users']) }}">
                                            <i class="fas fa-user-chart me-2"></i>Usuarios
                                        </a></li>
                                        <li><a class="dropdown-item" href="{{ route('superadmin.reports', ['type' => 'memberships']) }}">
                                            <i class="fas fa-gem me-2"></i>Membresías
                                        </a></li>
                                    </ul>
                                </li>
                                
                                <li><hr class="dropdown-divider"></li>
                                
                                <!-- Acciones Rápidas -->
                                <li><a class="dropdown-item" href="{{ route('superadmin.toggle-system') }}">
                                    <i class="fas fa-power-off me-2 text-danger"></i>Activar/Desactivar Sistema
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('superadmin.toggle-maintenance') }}">
                                    <i class="fas fa-tools me-2 text-warning"></i>Modo Mantenimiento
                                </a></li>
                            </ul>
                        </li>
                    @elseif(auth()->user()->isAdmin())
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
