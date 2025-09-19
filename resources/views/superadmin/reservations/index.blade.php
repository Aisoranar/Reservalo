@extends('layouts.app')

@section('title', 'Gestión de Reservas - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Indicador de carga global -->
    <div id="global-loading" class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center" style="background: rgba(0,0,0,0.5); z-index: 9999; display: none !important;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-calendar-check text-primary me-2"></i>
                        Gestión de Reservas
                    </h1>
                    <p class="text-muted mb-0">Administra todas las reservas del sistema</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('superadmin.reservations.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>Crear Reserva
                    </a>
                    <a href="{{ route('superadmin.reservations.pending') }}" class="btn btn-warning position-relative">
                        <i class="fas fa-clock me-1"></i>Pendientes
                        @if(\App\Models\Reservation::where('status', 'pending')->count() > 0)
                            <span class="badge bg-danger ms-1">{{ \App\Models\Reservation::where('status', 'pending')->count() }}</span>
                        @endif
                    </a>
                    <a href="{{ route('superadmin.pricing') }}" class="btn btn-info">
                        <i class="fas fa-dollar-sign me-1"></i>Actualizar Precios
                    </a>
                    <button type="button" class="btn btn-outline-primary" onclick="refreshReservations()">
                        <i class="fas fa-sync-alt me-1"></i>Actualizar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ \App\Models\Reservation::where('status', 'pending')->count() }}</h4>
                            <p class="mb-0">Pendientes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ \App\Models\Reservation::where('status', 'approved')->count() }}</h4>
                            <p class="mb-0">Aprobadas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ \App\Models\Reservation::where('status', 'rejected')->count() }}</h4>
                            <p class="mb-0">Rechazadas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ \App\Models\Reservation::count() }}</h4>
                            <p class="mb-0">Total</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-list fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" name="search" 
                                   value="{{ request('search') }}" placeholder="Usuario o propiedad...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="status">
                                <option value="">Todos</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Aprobada</option>
                                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rechazada</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelada</option>
                                <option value="deleted" {{ request('status') === 'deleted' ? 'selected' : '' }}>Eliminada</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Desde</label>
                            <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Hasta</label>
                            <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-dark text-white">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                            <a href="{{ route('superadmin.reservations') }}?status=pending" class="btn btn-warning text-dark">
                                <i class="fas fa-clock me-1"></i>Solo Pendientes
                            </a>
                            <a href="{{ route('superadmin.reservations') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Reservas -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list me-2"></i>Reservas ({{ $reservations->total() }})
                    </h6>
                </div>
                <div class="card-body">
                    @if($reservations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Usuario</th>
                                        <th>Propiedad</th>
                                        <th>Fechas</th>
                                        <th>Estado</th>
                                        <th>Creada</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reservations as $reservation)
                                        <tr class="{{ $reservation->status === 'pending' ? 'table-warning' : '' }} {{ $reservation->status === 'pending' && $reservation->created_at->diffInHours(now()) > 24 ? 'border-start border-danger border-4' : '' }}">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <strong>#{{ $reservation->id }}</strong>
                                                    @if($reservation->status === 'pending')
                                                        <span class="badge bg-warning text-dark ms-2 pulse-animation">
                                                            <i class="fas fa-exclamation-triangle me-1"></i>Urgente
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm {{ $reservation->is_guest_reservation ? 'bg-warning' : 'bg-primary' }} text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        {{ substr($reservation->customer_name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">
                                                            {{ $reservation->customer_name }}
                                                            @if($reservation->is_guest_reservation)
                                                                <span class="badge bg-warning text-dark ms-1">Huésped</span>
                                                            @endif
                                                        </div>
                                                        <small class="text-muted">{{ $reservation->customer_email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-bold">{{ $reservation->property->name }}</div>
                                                <small class="text-muted">{{ $reservation->property->location }}</small>
                                            </td>
                                            <td>
                                                <div class="fw-bold">
                                                    {{ $reservation->check_in ? $reservation->check_in->format('d/m/Y') : 'N/A' }}
                                                </div>
                                                <div class="text-muted">
                                                    {{ $reservation->check_out ? $reservation->check_out->format('d/m/Y') : 'N/A' }}
                                                </div>
                                                <small class="text-muted">
                                                    {{ $reservation->check_in && $reservation->check_out ? $reservation->nights . ' noche(s)' : 'N/A' }}
                                                </small>
                                            </td>
                                            <td>
                                                @switch($reservation->status)
                                                    @case('pending')
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-clock me-1"></i>Pendiente
                                                        </span>
                                                        @break
                                                    @case('approved')
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check me-1"></i>Aprobada
                                                        </span>
                                                        @break
                                                    @case('rejected')
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times me-1"></i>Rechazada
                                                        </span>
                                                        @break
                                                    @case('cancelled')
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-ban me-1"></i>Cancelada
                                                        </span>
                                                        @break
                                                    @case('deleted')
                                                        <span class="badge bg-dark">
                                                            <i class="fas fa-trash me-1"></i>Eliminada
                                                        </span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td>
                                                <div>{{ $reservation->created_at->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $reservation->created_at->format('H:i') }}</small>
                                                @if($reservation->status === 'pending')
                                                    <div class="mt-1">
                                                        @php
                                                            $hoursAgo = $reservation->created_at->diffInHours(now());
                                                            $daysAgo = $reservation->created_at->diffInDays(now());
                                                        @endphp
                                                        @if($daysAgo > 0)
                                                            <span class="badge bg-danger">
                                                                <i class="fas fa-clock me-1"></i>{{ $daysAgo }} día{{ $daysAgo > 1 ? 's' : '' }} pendiente
                                                            </span>
                                                        @elseif($hoursAgo > 2)
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="fas fa-clock me-1"></i>{{ $hoursAgo }}h pendiente
                                                            </span>
                                                        @else
                                                            <span class="badge bg-success">
                                                                <i class="fas fa-clock me-1"></i>{{ $hoursAgo }}h pendiente
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-1 align-items-center">
                                                    <a href="{{ route('superadmin.reservations.show', $reservation) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($reservation->status === 'pending')
                                                        <button type="button" 
                                                                class="btn btn-sm btn-success" 
                                                                onclick="approveReservation({{ $reservation->id }})" 
                                                                title="Aprobar">
                                                            <i class="fas fa-check me-1"></i>Aprobar
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-danger" 
                                                                onclick="rejectReservation({{ $reservation->id }})" 
                                                                title="Rechazar">
                                                            <i class="fas fa-times me-1"></i>Rechazar
                                                        </button>
                                                    @endif
                                                    <div class="dropdown">
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-info dropdown-toggle" 
                                                                data-bs-toggle="dropdown" 
                                                                data-bs-auto-close="true"
                                                                title="Más acciones">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item" href="#" onclick="sendEmail({{ $reservation->id }})">
                                                                    <i class="fas fa-envelope me-2"></i>Enviar correo
                                                                </a>
                                                            </li>
                                                            @if(auth()->user()->hasPermission('delete_reservations'))
                                                                <li><hr class="dropdown-divider"></li>
                                                                <li>
                                                                    <a class="dropdown-item text-danger" href="#" onclick="deleteReservation({{ $reservation->id }})">
                                                                        <i class="fas fa-trash me-2"></i>Eliminar reserva
                                                                    </a>
                                                                </li>
                                                            @endif
                                                        </ul>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación y controles -->
                        <div class="mt-4">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    {{ $reservations->links('vendor.pagination.bootstrap-5') }}
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div class="d-flex align-items-center justify-content-md-end gap-3">
                                        <small class="text-muted">Elementos por página:</small>
                                        <select class="form-select form-select-sm" style="width: auto;" onchange="changePerPage(this.value)">
                                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                            <option value="15" {{ request('per_page') == 15 || !request('per_page') ? 'selected' : '' }}>15</option>
                                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay reservas</h5>
                            <p class="text-muted">No se encontraron reservas con los filtros aplicados.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para aprobar reserva -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aprobar Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approveForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="approve_notes" class="form-label">Notas (opcional)</label>
                        <textarea class="form-control" id="approve_notes" name="notes" rows="3" 
                                  placeholder="Agregar notas adicionales..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Aprobar Reserva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para rechazar reserva -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rechazar Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="reject_reason" class="form-label">Motivo del rechazo <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reject_reason" name="reason" rows="3" 
                                  placeholder="Explica el motivo del rechazo..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rechazar Reserva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para enviar correo -->
<div class="modal fade" id="emailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Enviar Correo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="emailForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email_type" class="form-label">Tipo de correo <span class="text-danger">*</span></label>
                        <select class="form-select" id="email_type" name="email_type" required>
                            <option value="">Seleccionar tipo...</option>
                            <option value="reservation_approved">Reserva Aprobada</option>
                            <option value="reservation_rejected">Reserva Rechazada</option>
                            <option value="reservation_reminder">Recordatorio de Reserva</option>
                            <option value="reservation_cancelled">Reserva Cancelada</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="email_subject" class="form-label">Asunto del correo</label>
                        <input type="text" class="form-control" id="email_subject" name="subject" 
                               placeholder="Asunto del correo...">
                    </div>
                    <div class="mb-3">
                        <label for="email_message" class="form-label">Mensaje adicional</label>
                        <textarea class="form-control" id="email_message" name="message" rows="4" 
                                  placeholder="Mensaje adicional para el usuario..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-envelope me-1"></i>Enviar Correo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para eliminar reserva -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Eliminar Reserva
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deleteForm">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Advertencia:</strong> Esta acción eliminará permanentemente la reserva. Esta acción no se puede deshacer.
                    </div>
                    <div class="mb-3">
                        <label for="deletion_reason" class="form-label">Motivo de la eliminación <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="deletion_reason" name="deletion_reason" rows="4" 
                                  placeholder="Explica el motivo por el cual se elimina esta reserva..." required></textarea>
                        <div class="form-text">Este motivo será registrado en el sistema y notificado al usuario si corresponde.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Eliminar Reserva
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
.pulse-animation {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.table-hover tbody tr:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.btn-group .btn {
    transition: all 0.3s ease;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.loading {
    opacity: 0.6;
    pointer-events: none;
}

.status-badge {
    font-size: 0.8rem;
    padding: 0.4rem 0.8rem;
}

.urgent-row {
    background: linear-gradient(90deg, #fff3cd 0%, #ffffff 100%);
    border-left: 4px solid #ffc107;
}

.very-urgent-row {
    background: linear-gradient(90deg, #f8d7da 0%, #ffffff 100%);
    border-left: 4px solid #dc3545;
}

/* Estilos para paginación personalizada */
.pagination {
    border-radius: 0.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    background: #fff;
    padding: 0.5rem;
}

.pagination .page-link {
    border: 1px solid #e9ecef;
    color: #495057;
    padding: 0.5rem 0.75rem;
    margin: 0 1px;
    border-radius: 0.375rem;
    transition: all 0.3s ease;
    font-weight: 500;
    min-width: 2.5rem;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
}

.pagination .page-link:hover {
    background-color: #f8f9fa;
    border-color: #0d6efd;
    color: #0d6efd;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.2);
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
    border-color: #0d6efd;
    color: white;
    box-shadow: 0 4px 12px rgba(13, 110, 253, 0.4);
    transform: translateY(-1px);
}

.pagination .page-item.disabled .page-link {
    color: #adb5bd;
    background-color: #f8f9fa;
    border-color: #e9ecef;
    opacity: 0.6;
    cursor: not-allowed;
}

.pagination .page-link i {
    font-size: 0.75rem;
}

/* Estilos para el selector de elementos por página */
.form-select-sm {
    border-radius: 0.375rem;
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-select-sm:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Responsive para paginación */
@media (max-width: 768px) {
    .pagination {
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .pagination .page-link {
        padding: 0.375rem 0.5rem;
        margin: 0 1px;
        min-width: 2rem;
    }
}

/* Fix para dropdowns que se superponen */
.dropdown-menu {
    z-index: 1050 !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    border: 1px solid rgba(0, 0, 0, 0.15) !important;
    border-radius: 0.5rem !important;
}

/* Asegurar que los dropdowns no se superpongan */
.table-responsive {
    overflow: visible !important;
}

/* Mejorar el espaciado de las filas para evitar superposiciones */
.table tbody tr {
    position: relative;
    z-index: 1;
}

.table tbody tr:hover {
    z-index: 2;
}

/* Estilos específicos para los botones de acción */
.btn-group .dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1050;
    min-width: 160px;
}

/* Asegurar que el dropdown se posicione correctamente */
.dropdown {
    position: relative;
}

.dropdown-menu.show {
    z-index: 1050 !important;
}

/* Mejorar el posicionamiento de los dropdowns en la tabla */
.table td {
    position: relative;
    vertical-align: middle;
}

/* Asegurar que los dropdowns se abran hacia arriba cuando están cerca del borde inferior */
.dropdown-menu.dropdown-menu-end {
    right: 0;
    left: auto;
}

/* Mejorar el espaciado entre filas para evitar superposiciones */
.table tbody tr {
    min-height: 60px;
}

/* Estilos para los botones de acción */
.d-flex.gap-1 .btn {
    margin-right: 0.25rem;
    margin-bottom: 0.25rem;
}

/* Asegurar que el dropdown tenga suficiente espacio */
.dropdown-menu {
    min-width: 180px;
    max-width: 250px;
}

/* Mejorar la visibilidad del dropdown */
.dropdown-menu {
    background: white;
    border: 1px solid #dee2e6;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.dropdown-item {
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: #f8f9fa;
    color: #495057;
}

.dropdown-item.text-danger:hover {
    background-color: #f8d7da;
    color: #721c24;
}

/* Fix específico para evitar superposiciones en la tabla */
.table-responsive {
    overflow-x: auto;
    overflow-y: visible;
}

/* Asegurar que los dropdowns tengan el z-index correcto */
.dropdown-menu {
    position: absolute !important;
    z-index: 1060 !important;
    top: 100% !important;
    left: auto !important;
    right: 0 !important;
    transform: none !important;
}

/* Mejorar el espaciado de la tabla */
.table tbody tr td:last-child {
    width: 200px;
    min-width: 200px;
}

/* Asegurar que los botones no se superpongan */
.d-flex.gap-1 {
    flex-wrap: wrap;
    align-items: center;
    gap: 0.25rem;
}

/* Estilos personalizados para botones de filtro */
.btn-dark.text-white {
    background-color: #212529 !important;
    border-color: #212529 !important;
    color: #ffffff !important;
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.btn-dark.text-white:hover {
    background-color: #000000 !important;
    border-color: #000000 !important;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
}

.btn-warning.text-dark {
    background-color: #ffc107 !important;
    border-color: #ffc107 !important;
    color: #000000 !important;
    font-weight: 600;
    box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
    transition: all 0.3s ease;
}

.btn-warning.text-dark:hover {
    background-color: #e0a800 !important;
    border-color: #d39e00 !important;
    color: #000000 !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(255, 193, 7, 0.4);
}
</style>
@endpush

@push('scripts')
<script>
let currentReservationId = null;

function approveReservation(reservationId) {
    currentReservationId = reservationId;
    $('#approveModal').modal('show');
}

function rejectReservation(reservationId) {
    currentReservationId = reservationId;
    $('#rejectModal').modal('show');
}

function sendEmail(reservationId) {
    currentReservationId = reservationId;
    $('#emailModal').modal('show');
}

function deleteReservation(reservationId) {
    currentReservationId = reservationId;
    $('#deleteModal').modal('show');
}

function refreshReservations() {
    // Mostrar indicador de carga
    const refreshBtn = document.querySelector('button[onclick="refreshReservations()"]');
    const originalContent = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Actualizando...';
    refreshBtn.disabled = true;
    
    // Recargar la página
    setTimeout(() => {
        location.reload();
    }, 1000);
}

// Función para mostrar notificaciones toast
function showToast(message, type = 'success') {
    try {
        const toastContainer = document.getElementById('toast-container') || createToastContainer();
        
        if (!toastContainer) {
            console.error('No se pudo crear el contenedor de toast');
            return;
        }
        
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Verificar si Bootstrap está disponible
        if (typeof bootstrap !== 'undefined' && bootstrap.Toast) {
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            // Remover el toast después de que se oculte
            toast.addEventListener('hidden.bs.toast', () => {
                if (toast.parentNode) {
                    toast.remove();
                }
            });
        } else {
            // Fallback si Bootstrap no está disponible
            toast.style.display = 'block';
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 3000);
        }
    } catch (error) {
        console.error('Error mostrando toast:', error);
        // Fallback a alert si hay error
        alert(message);
    }
}

function createToastContainer() {
    try {
        const container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '9999';
        document.body.appendChild(container);
        return container;
    } catch (error) {
        console.error('Error creando contenedor de toast:', error);
        return null;
    }
}

// Función para confirmar acciones importantes
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Funciones para el indicador de carga global
function showGlobalLoading() {
    const loadingElement = document.getElementById('global-loading');
    if (loadingElement) {
        loadingElement.style.display = 'flex';
    }
}

function hideGlobalLoading() {
    const loadingElement = document.getElementById('global-loading');
    if (loadingElement) {
        loadingElement.style.display = 'none';
    }
}

// Auto-refresh cada 30 segundos para reservas pendientes
function autoRefresh() {
    const pendingCount = {{ \App\Models\Reservation::where('status', 'pending')->count() }};
    if (pendingCount > 0) {
        setTimeout(() => {
            location.reload();
        }, 30000); // 30 segundos
    }
}

// Iniciar auto-refresh si hay reservas pendientes
document.addEventListener('DOMContentLoaded', function() {
    // Esperar un poco más para asegurar que todos los elementos estén cargados
    setTimeout(() => {
        autoRefresh();
    }, 100);
});

// Función para cambiar elementos por página
function changePerPage(perPage) {
    showGlobalLoading();
    const url = new URL(window.location);
    url.searchParams.set('per_page', perPage);
    url.searchParams.delete('page'); // Reset a la primera página
    window.location.href = url.toString();
}

// Agregar indicador de carga a los enlaces de paginación
document.addEventListener('DOMContentLoaded', function() {
    // Esperar un poco para asegurar que todos los elementos estén disponibles
    setTimeout(() => {
        try {
            const paginationLinks = document.querySelectorAll('.pagination a');
            if (paginationLinks.length > 0) {
                paginationLinks.forEach(link => {
                    if (link && typeof link.addEventListener === 'function') {
                        link.addEventListener('click', function() {
                            showGlobalLoading();
                        });
                    }
                });
            }

            // Mejorar el posicionamiento de los dropdowns
            const dropdowns = document.querySelectorAll('.dropdown');
            if (dropdowns.length > 0) {
                dropdowns.forEach(dropdown => {
                    if (dropdown) {
                        const button = dropdown.querySelector('.dropdown-toggle');
                        const menu = dropdown.querySelector('.dropdown-menu');
                        
                        // Verificar que los elementos existen antes de agregar event listeners
                        if (button && menu && typeof button.addEventListener === 'function') {
                            button.addEventListener('click', function(e) {
                                e.stopPropagation();
                                
                                // Cerrar otros dropdowns abiertos
                                document.querySelectorAll('.dropdown-menu.show').forEach(openMenu => {
                                    if (openMenu !== menu) {
                                        openMenu.classList.remove('show');
                                    }
                                });
                            });
                        }
                    }
                });
            }

            // Cerrar dropdowns al hacer clic fuera
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                        if (menu) {
                            menu.classList.remove('show');
                        }
                    });
                }
            });
        } catch (error) {
            console.error('Error inicializando JavaScript:', error);
        }
    }, 200);
});

$('#approveForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/superadmin/reservations/${currentReservationId}/approve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Reserva aprobada correctamente', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('Error: ' + (data.message || 'No se pudo aprobar la reserva'), 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al aprobar la reserva');
    });
});

$('#rejectForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/superadmin/reservations/${currentReservationId}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Reserva rechazada correctamente', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('Error: ' + (data.message || 'No se pudo rechazar la reserva'), 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al rechazar la reserva');
    });
});

$('#emailForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/superadmin/reservations/${currentReservationId}/send-email`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Correo enviado correctamente', 'success');
            $('#emailModal').modal('hide');
            // Limpiar el formulario
            document.getElementById('emailForm').reset();
        } else {
            showToast('Error: ' + (data.message || 'No se pudo enviar el correo'), 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al enviar el correo');
    });
});

$('#deleteForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(`/superadmin/reservations/${currentReservationId}/delete`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Reserva eliminada correctamente', 'success');
            $('#deleteModal').modal('hide');
            // Recargar la página para actualizar la lista
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast('Error: ' + (data.message || 'No se pudo eliminar la reserva'), 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al eliminar la reserva');
    });
});
</script>
@endpush
@endsection
