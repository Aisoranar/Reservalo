@extends('layouts.app')

@section('title', 'Usuarios Desactivados - Panel de Administración')

@section('content')
<div class="container-fluid py-4">
    <!-- Header del Panel -->
    <div class="modern-header mb-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <div class="d-flex align-items-center">
                    <div class="header-icon-wrapper me-4">
                        <div class="header-icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <div class="icon-pulse"></div>
                    </div>
                    <div>
                        <h1 class="display-6 fw-bold text-primary mb-2">Usuarios Desactivados</h1>
                        <p class="lead text-muted mb-0">Gestiona todas las cuentas desactivadas del sistema</p>
                        <div class="breadcrumb-nav mt-2">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Usuarios Desactivados</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                <div class="header-actions d-flex flex-wrap gap-2 justify-content-lg-end">
                    <a href="{{ route('admin.deactivated-users.export') }}" class="btn btn-outline-primary btn-modern">
                        <i class="fas fa-download me-2"></i>Exportar
                    </a>
                    <button type="button" class="btn btn-warning btn-modern" data-bs-toggle="modal" data-bs-target="#cleanupModal">
                        <i class="fas fa-broom me-2"></i>Limpieza
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas del Sistema -->
    <div class="stats-section mb-5">
        <div class="row g-3">
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                <div class="stat-card modern-card">
                    <div class="stat-card-body">
                        <div class="stat-icon-wrapper total">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="icon-bg"></div>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $stats['total'] }}</div>
                            <div class="stat-label">Total Desactivados</div>
                            <div class="stat-trend">
                                <i class="fas fa-chart-line text-success"></i>
                                <span class="text-muted small">Sistema</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                <div class="stat-card modern-card">
                    <div class="stat-card-body">
                        <div class="stat-icon-wrapper can-reactivate">
                            <div class="stat-icon">
                                <i class="fas fa-undo"></i>
                            </div>
                            <div class="icon-bg"></div>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $stats['can_be_reactivated'] }}</div>
                            <div class="stat-label">Pueden Reactivarse</div>
                            <div class="stat-trend">
                                <i class="fas fa-check-circle text-success"></i>
                                <span class="text-muted small">Disponibles</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                <div class="stat-card modern-card">
                    <div class="stat-card-body">
                        <div class="stat-icon-wrapper suspended">
                            <div class="stat-icon">
                                <i class="fas fa-ban"></i>
                            </div>
                            <div class="icon-bg"></div>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $stats['policy_violations'] + $stats['suspicious_activity'] }}</div>
                            <div class="stat-label">Suspendidos</div>
                            <div class="stat-trend">
                                <i class="fas fa-exclamation-triangle text-warning"></i>
                                <span class="text-muted small">Restringidos</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                <div class="stat-card modern-card">
                    <div class="stat-card-body">
                        <div class="stat-icon-wrapper requests">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="icon-bg"></div>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $stats['reactivation_requests'] }}</div>
                            <div class="stat-label">Solicitudes</div>
                            <div class="stat-trend">
                                <i class="fas fa-hourglass-half text-info"></i>
                                <span class="text-muted small">Pendientes</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                <div class="stat-card modern-card">
                    <div class="stat-card-body">
                        <div class="stat-icon-wrapper multiple">
                            <div class="stat-icon">
                                <i class="fas fa-copy"></i>
                            </div>
                            <div class="icon-bg"></div>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $groupedUsers->count() }}</div>
                            <div class="stat-label">Cuentas Múltiples</div>
                            <div class="stat-trend">
                                <i class="fas fa-layer-group text-secondary"></i>
                                <span class="text-muted small">Duplicadas</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-6">
                <div class="stat-card modern-card">
                    <div class="stat-card-body">
                        <div class="stat-icon-wrapper self">
                            <div class="stat-icon">
                                <i class="fas fa-user-times"></i>
                            </div>
                            <div class="icon-bg"></div>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $stats['self_deactivated'] }}</div>
                            <div class="stat-label">Solicitud Propia</div>
                            <div class="stat-trend">
                                <i class="fas fa-user-minus text-primary"></i>
                                <span class="text-muted small">Voluntario</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="filters-section mb-5">
        <div class="modern-card">
            <div class="card-header-modern">
                <div class="d-flex align-items-center">
                    <div class="filter-icon me-3">
                        <i class="fas fa-filter"></i>
                    </div>
                    <div>
                        <h5 class="mb-1">Filtros y Búsqueda</h5>
                        <p class="text-muted mb-0 small">Encuentra usuarios desactivados específicos</p>
                    </div>
                </div>
                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse" aria-expanded="true">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </div>
            <div class="collapse show" id="filtersCollapse">
                <div class="card-body-modern">
                    <form method="GET" action="{{ route('admin.deactivated-users.index') }}" class="filters-form">
                        <div class="row g-4">
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group-modern">
                                    <label for="search" class="form-label-modern">
                                        <i class="fas fa-search me-2"></i>Buscar
                                    </label>
                                    <div class="input-group-modern">
                                        <input type="text" class="form-control-modern" id="search" name="search" 
                                               value="{{ request('search') }}" placeholder="Nombre o email...">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <i class="fas fa-user"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group-modern">
                                    <label for="reason" class="form-label-modern">
                                        <i class="fas fa-exclamation-circle me-2"></i>Motivo
                                    </label>
                                    <select class="form-select-modern" id="reason" name="reason">
                                        <option value="">Todos los motivos</option>
                                        <option value="user_request" {{ request('reason') == 'user_request' ? 'selected' : '' }}>Solicitud del usuario</option>
                                        <option value="inactivity" {{ request('reason') == 'inactivity' ? 'selected' : '' }}>Inactividad</option>
                                        <option value="policy_violation" {{ request('reason') == 'policy_violation' ? 'selected' : '' }}>Violación de políticas</option>
                                        <option value="suspicious_activity" {{ request('reason') == 'suspicious_activity' ? 'selected' : '' }}>Actividad sospechosa</option>
                                        <option value="temporary_hold" {{ request('reason') == 'temporary_hold' ? 'selected' : '' }}>Suspensión temporal</option>
                                        <option value="other" {{ request('reason') == 'other' ? 'selected' : '' }}>Otro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group-modern">
                                    <label for="status" class="form-label-modern">
                                        <i class="fas fa-toggle-on me-2"></i>Estado
                                    </label>
                                    <select class="form-select-modern" id="status" name="status">
                                        <option value="">Todos los estados</option>
                                        <option value="can_reactivate" {{ request('status') == 'can_reactivate' ? 'selected' : '' }}>Pueden reactivarse</option>
                                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspendidos</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group-modern">
                                    <label for="order_by" class="form-label-modern">
                                        <i class="fas fa-sort me-2"></i>Ordenar por
                                    </label>
                                    <select class="form-select-modern" id="order_by" name="order_by">
                                        <option value="deactivated_at" {{ request('order_by') == 'deactivated_at' ? 'selected' : '' }}>Fecha desactivación</option>
                                        <option value="name" {{ request('order_by') == 'name' ? 'selected' : '' }}>Nombre</option>
                                        <option value="email" {{ request('order_by') == 'email' ? 'selected' : '' }}>Email</option>
                                        <option value="deactivation_reason" {{ request('order_by') == 'deactivation_reason' ? 'selected' : '' }}>Motivo</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-6">
                                <div class="form-group-modern">
                                    <label for="order_direction" class="form-label-modern">
                                        <i class="fas fa-sort-amount-down me-2"></i>Dirección
                                    </label>
                                    <select class="form-select-modern" id="order_direction" name="order_direction">
                                        <option value="desc" {{ request('order_direction') == 'desc' ? 'selected' : '' }}>Descendente</option>
                                        <option value="asc" {{ request('order_direction') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-1 col-md-6">
                                <div class="form-group-modern">
                                    <label class="form-label-modern">&nbsp;</label>
                                    <button type="submit" class="btn btn-primary-modern w-100">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @if(request()->hasAny(['search', 'reason', 'status', 'order_by', 'order_direction']))
                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="active-filters">
                                    <span class="badge bg-primary me-2 mb-2">
                                        <i class="fas fa-filter me-1"></i>Filtros activos
                                    </span>
                                    @if(request('search'))
                                        <span class="badge bg-info me-2 mb-2">
                                            Búsqueda: "{{ request('search') }}"
                                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="text-white ms-1">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </span>
                                    @endif
                                    @if(request('reason'))
                                        <span class="badge bg-warning me-2 mb-2">
                                            Motivo: {{ ucfirst(str_replace('_', ' ', request('reason'))) }}
                                            <a href="{{ request()->fullUrlWithQuery(['reason' => null]) }}" class="text-white ms-1">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </span>
                                    @endif
                                    @if(request('status'))
                                        <span class="badge bg-success me-2 mb-2">
                                            Estado: {{ ucfirst(str_replace('_', ' ', request('status'))) }}
                                            <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="text-white ms-1">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </span>
                                    @endif
                                    <a href="{{ route('admin.deactivated-users.index') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-times me-1"></i>Limpiar todos
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Usuarios Desactivados -->
    <div class="deactivated-users-list">
        @if($deactivatedUsers->count() > 0)
            <div class="modern-table-container">
                <div class="table-header-modern">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Usuarios Desactivados</h5>
                            <p class="text-muted mb-0 small">Mostrando {{ $deactivatedUsers->count() }} de {{ $deactivatedUsers->total() }} usuarios</p>
                        </div>
                        <div class="table-actions">
                            <button class="btn btn-outline-secondary btn-sm" onclick="toggleTableView()">
                                <i class="fas fa-th-large"></i> Vista de tarjetas
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Vista de Tabla -->
                <div class="table-view" id="tableView">
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th class="user-column">
                                        <i class="fas fa-user me-2"></i>Usuario
                                    </th>
                                    <th class="reason-column">
                                        <i class="fas fa-exclamation-circle me-2"></i>Motivo
                                    </th>
                                    <th class="date-column">
                                        <i class="fas fa-calendar me-2"></i>Fecha Desactivación
                                    </th>
                                    <th class="data-column">
                                        <i class="fas fa-database me-2"></i>Datos Preservados
                                    </th>
                                    <th class="status-column">
                                        <i class="fas fa-toggle-on me-2"></i>Estado
                                    </th>
                                    <th class="actions-column">
                                        <i class="fas fa-cogs me-2"></i>Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deactivatedUsers as $user)
                                <tr class="user-row">
                                    <td>
                                        <div class="user-info-modern">
                                            <div class="user-avatar-modern">
                                                @if($user->profile_picture)
                                                    <img src="{{ $user->profile_picture }}" alt="{{ $user->name }}" class="avatar-img-modern">
                                                @else
                                                    <div class="avatar-placeholder-modern">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                @endif
                                                <div class="avatar-status {{ $user->canBeReactivated() ? 'can-reactivate' : 'suspended' }}"></div>
                                            </div>
                                            <div class="user-details-modern">
                                                <div class="user-name-modern">{{ $user->name }}</div>
                                                <div class="user-email-modern">{{ $user->email }}</div>
                                                @if($user->deactivation_data)
                                                    <div class="user-stats-modern">
                                                        <span class="stat-item">
                                                            <i class="fas fa-calendar-check text-success"></i>
                                                            {{ $user->deactivation_data['reservations_count'] ?? 0 }}
                                                        </span>
                                                        <span class="stat-item">
                                                            <i class="fas fa-star text-warning"></i>
                                                            {{ $user->deactivation_data['reviews_count'] ?? 0 }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="reason-info-modern">
                                            <span class="badge-modern bg-{{ $user->deactivation_reason === 'policy_violation' ? 'danger' : ($user->deactivation_reason === 'suspicious_activity' ? 'warning' : 'secondary') }}">
                                                {{ $user->deactivation_reason_text }}
                                            </span>
                                            @if($user->deactivation_notes)
                                                <div class="reason-notes">
                                                    <small class="text-muted">
                                                        {{ Str::limit($user->deactivation_notes, 50) }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="date-info-modern">
                                            <div class="date-modern">{{ $user->deactivated_at->format('d/m/Y') }}</div>
                                            <small class="text-muted">{{ $user->time_since_deactivation }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="data-preserved-modern">
                                            @if($user->deactivation_data)
                                                <div class="data-grid">
                                                    <div class="data-item-modern">
                                                        <i class="fas fa-calendar-check text-success"></i>
                                                        <span>{{ $user->deactivation_data['reservations_count'] ?? 0 }} reservas</span>
                                                    </div>
                                                    <div class="data-item-modern">
                                                        <i class="fas fa-star text-warning"></i>
                                                        <span>{{ $user->deactivation_data['reviews_count'] ?? 0 }} reseñas</span>
                                                    </div>
                                                    <div class="data-item-modern">
                                                        <i class="fas fa-clock text-info"></i>
                                                        <span>{{ $user->deactivation_data['account_age_days'] ?? 0 }} días</span>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">Sin datos</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="status-info-modern">
                                            @if($user->canBeReactivated())
                                                <span class="badge-modern bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Puede reactivarse
                                                </span>
                                            @else
                                                <span class="badge-modern bg-danger">
                                                    <i class="fas fa-ban me-1"></i>Suspendido
                                                </span>
                                            @endif
                                            @if($user->hasRequestedReactivation())
                                                <div class="reactivation-request-modern">
                                                    <small class="text-info">
                                                        <i class="fas fa-clock me-1"></i>Solicitud pendiente
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="actions-modern">
                                            <div class="btn-group-modern">
                                                <a href="{{ route('admin.deactivated-users.show', $user) }}" 
                                                   class="btn btn-sm btn-outline-primary btn-modern" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($user->canBeReactivated())
                                                    <button type="button" class="btn btn-sm btn-success btn-modern" 
                                                            onclick="showReactivationModal({{ $user->id }})" title="Reactivar">
                                                        <i class="fas fa-undo"></i>
                                                    </button>
                                                @endif
                                                <a href="{{ route('admin.users.history', $user->email) }}" 
                                                   class="btn btn-sm btn-outline-info btn-modern" title="Ver historial">
                                                    <i class="fas fa-history"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Vista de Tarjetas (Oculta por defecto) -->
                <div class="card-view d-none" id="cardView">
                    <div class="row g-4">
                        @foreach($deactivatedUsers as $user)
                        <div class="col-xl-4 col-lg-6 col-md-6">
                            <div class="user-card-modern">
                                <div class="card-header-modern">
                                    <div class="user-avatar-modern">
                                        @if($user->profile_picture)
                                            <img src="{{ $user->profile_picture }}" alt="{{ $user->name }}" class="avatar-img-modern">
                                        @else
                                            <div class="avatar-placeholder-modern">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                        <div class="avatar-status {{ $user->canBeReactivated() ? 'can-reactivate' : 'suspended' }}"></div>
                                    </div>
                                    <div class="user-info-modern">
                                        <h6 class="user-name-modern">{{ $user->name }}</h6>
                                        <p class="user-email-modern">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <div class="card-body-modern">
                                    <div class="reason-info-modern mb-3">
                                        <span class="badge-modern bg-{{ $user->deactivation_reason === 'policy_violation' ? 'danger' : ($user->deactivation_reason === 'suspicious_activity' ? 'warning' : 'secondary') }}">
                                            {{ $user->deactivation_reason_text }}
                                        </span>
                                    </div>
                                    <div class="date-info-modern mb-3">
                                        <small class="text-muted">Desactivado el {{ $user->deactivated_at->format('d/m/Y') }}</small>
                                        <br>
                                        <small class="text-muted">{{ $user->time_since_deactivation }}</small>
                                    </div>
                                    <div class="status-info-modern mb-3">
                                        @if($user->canBeReactivated())
                                            <span class="badge-modern bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Puede reactivarse
                                            </span>
                                        @else
                                            <span class="badge-modern bg-danger">
                                                <i class="fas fa-ban me-1"></i>Suspendido
                                            </span>
                                        @endif
                                    </div>
                                    <div class="actions-modern">
                                        <div class="btn-group-modern w-100">
                                            <a href="{{ route('admin.deactivated-users.show', $user) }}" 
                                               class="btn btn-sm btn-outline-primary btn-modern">
                                                <i class="fas fa-eye me-1"></i>Ver
                                            </a>
                                            @if($user->canBeReactivated())
                                                <button type="button" class="btn btn-sm btn-success btn-modern" 
                                                        onclick="showReactivationModal({{ $user->id }})">
                                                    <i class="fas fa-undo me-1"></i>Reactivar
                                                </button>
                                            @endif
                                            <a href="{{ route('admin.users.history', $user->email) }}" 
                                               class="btn btn-sm btn-outline-info btn-modern">
                                                <i class="fas fa-history me-1"></i>Historial
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Paginación -->
            <div class="pagination-section mt-4">
                {{ $deactivatedUsers->appends(request()->query())->links() }}
            </div>
        @else
            <div class="empty-state-modern">
                <div class="empty-state-content">
                    <div class="empty-state-icon-modern">
                        <i class="fas fa-user-clock"></i>
                        <div class="icon-pulse-modern"></div>
                    </div>
                    <h3 class="empty-state-title">No hay usuarios desactivados</h3>
                    <p class="empty-state-description">No se encontraron usuarios desactivados con los filtros aplicados.</p>
                    <div class="empty-state-actions">
                        <a href="{{ route('admin.deactivated-users.index') }}" class="btn btn-primary-modern">
                            <i class="fas fa-refresh me-2"></i>Limpiar filtros
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modal de Reactivación -->
<div class="modal fade" id="reactivationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reactivar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reactivationForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reactivation_reason" class="form-label">Motivo de Reactivación</label>
                        <textarea class="form-control" id="reactivation_reason" name="reactivation_reason" 
                                  rows="3" required placeholder="Explica por qué se reactiva esta cuenta..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-undo me-2"></i>Reactivar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Limpieza -->
<div class="modal fade" id="cleanupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Limpieza de Usuarios Desactivados</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.deactivated-users.cleanup') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>¡Atención!</strong> Esta acción eliminará permanentemente usuarios desactivados antiguos.
                    </div>
                    <div class="form-group">
                        <label for="days" class="form-label">Eliminar usuarios de más de (días)</label>
                        <input type="number" class="form-control" id="days" name="days" 
                               min="30" max="3650" value="365" required>
                        <small class="form-text text-muted">Mínimo 30 días, máximo 10 años</small>
                    </div>
                    <div class="form-group">
                        <label for="reason" class="form-label">Motivo de la limpieza</label>
                        <textarea class="form-control" id="reason" name="reason" 
                                  rows="2" placeholder="Explica por qué se realiza esta limpieza..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-broom me-2"></i>Ejecutar Limpieza
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Estilos modernos para la página de usuarios desactivados */

/* Header moderno */
.modern-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2rem;
    color: white;
    position: relative;
    overflow: hidden;
}

.modern-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.header-icon-wrapper {
    position: relative;
    z-index: 2;
}

.header-icon {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.icon-pulse {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100px;
    height: 100px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: translate(-50%, -50%) scale(1); opacity: 1; }
    100% { transform: translate(-50%, -50%) scale(1.2); opacity: 0; }
}

.breadcrumb-nav .breadcrumb {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 0.5rem 1rem;
    margin: 0;
}

.breadcrumb-nav .breadcrumb-item a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
}

.breadcrumb-nav .breadcrumb-item.active {
    color: white;
}

/* Botones modernos */
.btn-modern {
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    position: relative;
    overflow: hidden;
}

.btn-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-modern:hover::before {
    left: 100%;
}

.btn-primary-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.btn-primary-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    color: white;
}

/* Tarjetas de estadísticas modernas */
.modern-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: none;
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}

.modern-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.stat-card.modern-card {
    height: 100%;
}

.stat-card-body {
    padding: 2rem;
    position: relative;
    z-index: 2;
}

.stat-icon-wrapper {
    position: relative;
    margin-bottom: 1.5rem;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    position: relative;
    z-index: 2;
}

.icon-bg {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    opacity: 0.1;
    z-index: 1;
}

.stat-icon-wrapper.total .stat-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-icon-wrapper.total .icon-bg {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-icon-wrapper.can-reactivate .stat-icon {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stat-icon-wrapper.can-reactivate .icon-bg {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stat-icon-wrapper.suspended .stat-icon {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.stat-icon-wrapper.suspended .icon-bg {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.stat-icon-wrapper.requests .stat-icon {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    color: #333;
}

.stat-icon-wrapper.requests .icon-bg {
    background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
}

.stat-icon-wrapper.multiple .stat-icon {
    background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
    color: #333;
}

.stat-icon-wrapper.multiple .icon-bg {
    background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
}

.stat-icon-wrapper.self .stat-icon {
    background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%);
    color: #333;
}

.stat-icon-wrapper.self .icon-bg {
    background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%);
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    color: #718096;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.stat-trend {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
}

/* Filtros modernos */
.card-header-modern {
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    border-bottom: 1px solid #e2e8f0;
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: between;
    align-items: center;
}

.filter-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.card-body-modern {
    padding: 2rem;
}

.form-group-modern {
    margin-bottom: 1.5rem;
}

.form-label-modern {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.form-control-modern,
.form-select-modern {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    background: white;
}

.form-control-modern:focus,
.form-select-modern:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    outline: none;
}

.input-group-modern {
    position: relative;
}

.input-group-modern .input-group-append {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    z-index: 3;
}

.input-group-modern .input-group-text {
    background: transparent;
    border: none;
    color: #a0aec0;
}

.active-filters {
    padding: 1rem;
    background: #f7fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

/* Tabla moderna */
.modern-table-container {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.table-header-modern {
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
}

.table-modern {
    margin: 0;
}

.table-modern thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 1.5rem 1rem;
    font-weight: 600;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table-modern tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: all 0.3s ease;
}

.table-modern tbody tr:hover {
    background: #f8fafc;
    transform: scale(1.01);
}

.table-modern tbody td {
    padding: 1.5rem 1rem;
    vertical-align: middle;
    border: none;
}

/* Información de usuario moderna */
.user-info-modern {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-avatar-modern {
    position: relative;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.avatar-img-modern {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder-modern {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.avatar-status {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 3px solid white;
}

.avatar-status.can-reactivate {
    background: #48bb78;
}

.avatar-status.suspended {
    background: #f56565;
}

.user-name-modern {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

.user-email-modern {
    color: #718096;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.user-stats-modern {
    display: flex;
    gap: 1rem;
}

.stat-item {
    font-size: 0.8rem;
    color: #a0aec0;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* Badges modernos */
.badge-modern {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.8rem;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

/* Información de razón moderna */
.reason-info-modern {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.reason-notes {
    font-size: 0.8rem;
    color: #718096;
    line-height: 1.4;
}

/* Información de fecha moderna */
.date-info-modern {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.date-modern {
    font-weight: 600;
    color: #2d3748;
}

/* Datos preservados modernos */
.data-preserved-modern {
    min-width: 150px;
}

.data-grid {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.data-item-modern {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    color: #4a5568;
}

/* Estado moderno */
.status-info-modern {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.reactivation-request-modern {
    font-size: 0.8rem;
    color: #3182ce;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* Acciones modernas */
.actions-modern {
    display: flex;
    justify-content: center;
}

.btn-group-modern {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.btn-modern {
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

.btn-modern:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Vista de tarjetas */
.user-card-modern {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.user-card-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.card-header-modern {
    padding: 1.5rem;
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.card-body-modern {
    padding: 1.5rem;
}

/* Estado vacío moderno */
.empty-state-modern {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 400px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.empty-state-content {
    text-align: center;
    max-width: 400px;
}

.empty-state-icon-modern {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    color: white;
    font-size: 3rem;
}

.icon-pulse-modern {
    position: absolute;
    top: -10px;
    left: -10px;
    right: -10px;
    bottom: -10px;
    border: 2px solid rgba(102, 126, 234, 0.3);
    border-radius: 50%;
    animation: pulse 2s infinite;
}

.empty-state-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 1rem;
}

.empty-state-description {
    color: #718096;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.empty-state-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
}

/* Responsive */
@media (max-width: 768px) {
    .modern-header {
        padding: 1.5rem;
    }
    
    .header-icon {
        width: 60px;
        height: 60px;
        font-size: 1.5rem;
    }
    
    /* Tarjetas de estadísticas más pequeñas en móvil */
    .stat-card-body {
        padding: 1rem;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    .stat-label {
        font-size: 0.8rem;
    }
    
    .stat-trend {
        font-size: 0.7rem;
    }
    
    .stat-trend span {
        display: none; /* Ocultar texto descriptivo en móvil */
    }
    
    .table-modern thead th {
        padding: 1rem 0.5rem;
        font-size: 0.8rem;
    }
    
    .table-modern tbody td {
        padding: 1rem 0.5rem;
    }
    
    .user-info-modern {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
    
    .btn-group-modern {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-modern {
        width: 100%;
        justify-content: center;
    }
}

/* Estilos específicos para móviles muy pequeños */
@media (max-width: 576px) {
    .stat-card-body {
        padding: 0.75rem;
    }
    
    .stat-icon {
        width: 35px;
        height: 35px;
        font-size: 0.9rem;
    }
    
    .stat-number {
        font-size: 1.25rem;
    }
    
    .stat-label {
        font-size: 0.75rem;
        line-height: 1.2;
    }
    
    .stat-trend {
        font-size: 0.65rem;
    }
    
    .icon-bg {
        width: 60px;
        height: 60px;
        top: -8px;
        right: -8px;
    }
}

/* Asegurar que las 6 tarjetas se vean en 2 filas de 3 en móvil */
@media (max-width: 768px) {
    .stats-section .row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
    }
    
    .stats-section .col-6 {
        flex: 0 0 auto;
        width: 100%;
    }
}

/* Para pantallas muy pequeñas, 2 columnas */
@media (max-width: 480px) {
    .stats-section .row {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Animaciones */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.user-row {
    animation: fadeInUp 0.5s ease-out;
}

.user-card-modern {
    animation: fadeInUp 0.5s ease-out;
}

/* Scrollbar personalizado */
.table-responsive::-webkit-scrollbar {
    height: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
}
</style>
@endpush

@push('scripts')
<script>
function showReactivationModal(userId) {
    const form = document.getElementById('reactivationForm');
    form.action = `/admin/deactivated-users/${userId}/reactivate`;
    
    const modal = new bootstrap.Modal(document.getElementById('reactivationModal'));
    modal.show();
}

// Auto-submit del formulario de reactivación
document.getElementById('reactivationForm').addEventListener('submit', function() {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
});

// Toggle entre vista de tabla y tarjetas
function toggleTableView() {
    const tableView = document.getElementById('tableView');
    const cardView = document.getElementById('cardView');
    const toggleBtn = document.querySelector('[onclick="toggleTableView()"]');
    
    if (tableView.classList.contains('d-none')) {
        // Mostrar tabla
        tableView.classList.remove('d-none');
        cardView.classList.add('d-none');
        toggleBtn.innerHTML = '<i class="fas fa-th-large"></i> Vista de tarjetas';
    } else {
        // Mostrar tarjetas
        tableView.classList.add('d-none');
        cardView.classList.remove('d-none');
        toggleBtn.innerHTML = '<i class="fas fa-table"></i> Vista de tabla';
    }
}

// Animación de entrada para las tarjetas
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.user-card-modern');
    cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });
});
</script>
@endpush
