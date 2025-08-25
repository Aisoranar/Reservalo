@extends('layouts.app')

@section('title', 'Panel de Administración - Reservalo')

@section('content')
<div class="container-fluid py-4">
    <!-- Header del Panel -->
    <div class="admin-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="header-icon me-3">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div>
                        <h1 class="h2 mb-1">Panel de Administración</h1>
                        <p class="text-muted mb-0">Gestiona tu plataforma de reservas desde un solo lugar</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="header-actions">
                    <a href="{{ route('admin.pricing.index') }}" class="btn btn-outline-success me-2">
                        <i class="fas fa-dollar-sign me-2"></i>Precios
                    </a>
                    <a href="{{ route('admin.reports') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-chart-bar me-2"></i>Reportes
                    </a>
                    <a href="{{ route('admin.settings') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-cog me-2"></i>Configuración
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas Principales -->
    <div class="stats-section mb-4">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon users">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['total_users'] }}</div>
                        <div class="stat-label">Usuarios Activos</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon properties">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['total_properties'] }}</div>
                        <div class="stat-label">Propiedades</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-card">
                        <div class="stat-icon reservations">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $stats['total_reservations'] }}</div>
                            <div class="stat-label">Total Reservas</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-icon pending">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['pending_reservations'] }}</div>
                        <div class="stat-label">Pendientes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas de Precios y Descuentos -->
    <div class="pricing-stats-section mb-4">
        <div class="row g-3">
            <div class="col-md-4 col-6">
                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['active_prices'] ?? 0 }}</div>
                        <div class="stat-label">Precios Activos</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-6">
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fas fa-tags"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['active_discounts'] ?? 0 }}</div>
                        <div class="stat-label">Descuentos Activos</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-6">
                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">${{ number_format($stats['average_base_price'] ?? 0, 0) }}</div>
                        <div class="stat-label">Precio Promedio</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>

    <!-- Estadísticas de Usuarios Desactivados -->
    <div class="deactivated-users-section mb-4">
        <div class="row g-3">
            <div class="col-md-4 col-6">
                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['deactivated_users'] }}</div>
                        <div class="stat-label">Usuarios Desactivados</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-6">
                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class="fas fa-undo"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['reactivation_requests'] }}</div>
                        <div class="stat-label">Solicitudes de Reactivación</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-6">
                <div class="stat-card">
                    <div class="stat-content">
                        <a href="{{ route('admin.deactivated-users.index') }}" class="btn btn-outline-warning w-100">
                            <i class="fas fa-user-clock me-2"></i>Gestionar Usuarios Desactivados
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="quick-actions-section mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <a href="{{ route('admin.properties.create') }}" class="quick-action-card">
                            <div class="action-icon">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="action-content">
                                <h6>Nueva Propiedad</h6>
                                <p>Agregar una nueva propiedad al sistema</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('admin.reservations.index') }}" class="quick-action-card">
                            <div class="action-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="action-content">
                                <h6>Gestionar Reservas</h6>
                                <p>Revisar y aprobar reservas pendientes</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('admin.users.index') }}" class="quick-action-card">
                            <div class="action-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="action-content">
                                <h6>Gestionar Usuarios</h6>
                                <p>Administrar cuentas de usuario</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('admin.deactivated-users.index') }}" class="quick-action-card">
                            <div class="action-icon">
                                <i class="fas fa-user-clock"></i>
                            </div>
                            <div class="action-content">
                                <h6>Usuarios Desactivados</h6>
                                <p>Revisar y reactivar cuentas</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enlaces de Navegación -->
    <div class="navigation-section">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-home me-2"></i>Gestión de Propiedades
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="nav-links">
                            <a href="{{ route('admin.properties.index') }}" class="nav-link">
                                <i class="fas fa-list me-2"></i>Ver Todas las Propiedades
                            </a>
                            <a href="{{ route('admin.properties.create') }}" class="nav-link">
                                <i class="fas fa-plus me-2"></i>Crear Nueva Propiedad
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar me-2"></i>Gestión de Reservas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="nav-links">
                            <a href="{{ route('admin.reservations.index') }}" class="nav-link">
                                <i class="fas fa-list me-2"></i>Ver Todas las Reservas
                            </a>
                            <a href="{{ route('admin.export') }}" class="nav-link">
                                <i class="fas fa-download me-2"></i>Exportar Reservas
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-users me-2"></i>Gestión de Usuarios
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="nav-links">
                            <a href="{{ route('admin.users.index') }}" class="nav-link">
                                <i class="fas fa-list me-2"></i>Ver Usuarios Activos
                            </a>
                            <a href="{{ route('admin.deactivated-users.index') }}" class="nav-link">
                                <i class="fas fa-user-clock me-2"></i>Usuarios Desactivados
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>Reportes y Análisis
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="nav-links">
                            <a href="{{ route('admin.reports') }}" class="nav-link">
                                <i class="fas fa-chart-line me-2"></i>Ver Reportes
                            </a>
                            <a href="{{ route('admin.settings') }}" class="nav-link">
                                <i class="fas fa-cog me-2"></i>Configuración
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
