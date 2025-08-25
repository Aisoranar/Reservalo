@extends('layouts.app')

@section('title', 'Usuarios Desactivados - Panel de Administración')

@section('content')
<div class="container-fluid py-4">
    <!-- Header del Panel -->
    <div class="admin-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="header-icon me-3">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div>
                        <h1 class="h2 mb-1">Usuarios Desactivados</h1>
                        <p class="text-muted mb-0">Gestiona todas las cuentas desactivadas del sistema</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="header-actions">
                    <a href="{{ route('admin.deactivated-users.export') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-download me-2"></i>Exportar
                    </a>
                    <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#cleanupModal">
                        <i class="fas fa-broom me-2"></i>Limpieza
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas del Sistema -->
    <div class="stats-section mb-4">
        <div class="row g-3">
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['total'] }}</div>
                        <div class="stat-label">Total Desactivados</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <div class="stat-icon can-reactivate">
                        <i class="fas fa-undo"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['can_be_reactivated'] }}</div>
                        <div class="stat-label">Pueden Reactivarse</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <div class="stat-icon suspended">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['policy_violations'] + $stats['suspicious_activity'] }}</div>
                        <div class="stat-label">Suspendidos</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <div class="stat-icon requests">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['reactivation_requests'] }}</div>
                        <div class="stat-label">Solicitudes</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <div class="stat-icon multiple">
                        <i class="fas fa-copy"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $groupedUsers->count() }}</div>
                        <div class="stat-label">Cuentas Múltiples</div>
                    </div>
                </div>
            </div>
            <div class="col-md-2 col-6">
                <div class="stat-card">
                    <div class="stat-icon self">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-number">{{ $stats['self_deactivated'] }}</div>
                        <div class="stat-label">Solicitud Propia</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="filters-section mb-4">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.deactivated-users.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="search" name="search" 
                               value="{{ request('search') }}" placeholder="Nombre o email...">
                    </div>
                    <div class="col-md-2">
                        <label for="reason" class="form-label">Motivo</label>
                        <select class="form-select" id="reason" name="reason">
                            <option value="">Todos</option>
                            <option value="user_request" {{ request('reason') == 'user_request' ? 'selected' : '' }}>Solicitud del usuario</option>
                            <option value="inactivity" {{ request('reason') == 'inactivity' ? 'selected' : '' }}>Inactividad</option>
                            <option value="policy_violation" {{ request('reason') == 'policy_violation' ? 'selected' : '' }}>Violación de políticas</option>
                            <option value="suspicious_activity" {{ request('reason') == 'suspicious_activity' ? 'selected' : '' }}>Actividad sospechosa</option>
                            <option value="temporary_hold" {{ request('reason') == 'temporary_hold' ? 'selected' : '' }}>Suspensión temporal</option>
                            <option value="other" {{ request('reason') == 'other' ? 'selected' : '' }}>Otro</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Estado</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Todos</option>
                            <option value="can_reactivate" {{ request('status') == 'can_reactivate' ? 'selected' : '' }}>Pueden reactivarse</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspendidos</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="order_by" class="form-label">Ordenar por</label>
                        <select class="form-select" id="order_by" name="order_by">
                            <option value="deactivated_at" {{ request('order_by') == 'deactivated_at' ? 'selected' : '' }}>Fecha desactivación</option>
                            <option value="name" {{ request('order_by') == 'name' ? 'selected' : '' }}>Nombre</option>
                            <option value="email" {{ request('order_by') == 'email' ? 'selected' : '' }}>Email</option>
                            <option value="deactivation_reason" {{ request('order_by') == 'deactivation_reason' ? 'selected' : '' }}>Motivo</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="order_direction" class="form-label">Dirección</label>
                        <select class="form-select" id="order_direction" name="order_direction">
                            <option value="desc" {{ request('order_direction') == 'desc' ? 'selected' : '' }}>Descendente</option>
                            <option value="asc" {{ request('order_direction') == 'asc' ? 'selected' : '' }}>Ascendente</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Lista de Usuarios Desactivados -->
    <div class="deactivated-users-list">
        @if($deactivatedUsers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Usuario</th>
                            <th>Motivo</th>
                            <th>Fecha Desactivación</th>
                            <th>Datos Preservados</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deactivatedUsers as $user)
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        @if($user->profile_picture)
                                            <img src="{{ $user->profile_picture }}" alt="{{ $user->name }}" class="avatar-img">
                                        @else
                                            <div class="avatar-placeholder">
                                                <i class="fas fa-user"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="user-details">
                                        <div class="user-name">{{ $user->name }}</div>
                                        <div class="user-email">{{ $user->email }}</div>
                                        @if($user->deactivation_data)
                                            <div class="user-stats">
                                                <small class="text-muted">
                                                    {{ $user->deactivation_data['reservations_count'] ?? 0 }} reservas, 
                                                    {{ $user->deactivation_data['reviews_count'] ?? 0 }} reseñas
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="reason-info">
                                    <span class="badge badge-{{ $user->deactivation_reason }}">
                                        {{ $user->deactivation_reason_text }}
                                    </span>
                                    @if($user->deactivation_notes)
                                        <small class="d-block text-muted mt-1">
                                            {{ Str::limit($user->deactivation_notes, 50) }}
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="date-info">
                                    <div class="date">{{ $user->deactivated_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $user->time_since_deactivation }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="data-preserved">
                                    @if($user->deactivation_data)
                                        <div class="data-item">
                                            <i class="fas fa-calendar-check text-success"></i>
                                            <span>{{ $user->deactivation_data['reservations_count'] ?? 0 }} reservas</span>
                                        </div>
                                        <div class="data-item">
                                            <i class="fas fa-star text-warning"></i>
                                            <span>{{ $user->deactivation_data['reviews_count'] ?? 0 }} reseñas</span>
                                        </div>
                                        <div class="data-item">
                                            <i class="fas fa-clock text-info"></i>
                                            <span>{{ $user->deactivation_data['account_age_days'] ?? 0 }} días</span>
                                        </div>
                                    @else
                                        <span class="text-muted">Sin datos</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="status-info">
                                    @if($user->canBeReactivated())
                                        <span class="badge badge-success">Puede reactivarse</span>
                                    @else
                                        <span class="badge badge-danger">Suspendido</span>
                                    @endif
                                    @if($user->hasRequestedReactivation())
                                        <div class="reactivation-request mt-1">
                                            <small class="text-info">
                                                <i class="fas fa-clock"></i> Solicitud de reactivación
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('admin.deactivated-users.show', $user) }}" 
                                       class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($user->canBeReactivated())
                                        <button type="button" class="btn btn-sm btn-success" 
                                                onclick="showReactivationModal({{ $user->id }})" title="Reactivar">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.users.history', $user->email) }}" 
                                       class="btn btn-sm btn-outline-info" title="Ver historial">
                                        <i class="fas fa-history"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="pagination-section mt-4">
                {{ $deactivatedUsers->appends(request()->query())->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-user-clock"></i>
                </div>
                <h3>No hay usuarios desactivados</h3>
                <p>No se encontraron usuarios desactivados con los filtros aplicados.</p>
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
</script>
@endpush
