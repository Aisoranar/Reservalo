@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Gestión de Reservas</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-success" onclick="bulkAction('approve')">
                <i class="fas fa-check me-2"></i>Aprobar Seleccionadas
            </button>
            <button class="btn btn-warning" onclick="bulkAction('reject')">
                <i class="fas fa-times me-2"></i>Rechazar Seleccionadas
            </button>
            <button class="btn btn-danger" onclick="bulkAction('cancel')">
                <i class="fas fa-ban me-2"></i>Cancelar Seleccionadas
            </button>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h4 class="mb-0">{{ $stats['total'] }}</h4>
                    <small>Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h4 class="mb-0">{{ $stats['pending'] }}</h4>
                    <small>Pendientes</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h4 class="mb-0">{{ $stats['approved'] }}</h4>
                    <small>Aprobadas</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h4 class="mb-0">{{ $stats['rejected'] }}</h4>
                    <small>Rechazadas</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h4 class="mb-0">{{ $stats['cancelled'] }}</h4>
                    <small>Canceladas</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Estado</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">Todos</option>
                        <option value="pending">Pendientes</option>
                        <option value="approved">Aprobadas</option>
                        <option value="rejected">Rechazadas</option>
                        <option value="cancelled">Canceladas</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Estado de Pago</label>
                    <select class="form-select" id="paymentFilter">
                        <option value="">Todos</option>
                        <option value="pending">Pendiente</option>
                        <option value="paid">Pagado</option>
                        <option value="partial">Parcial</option>
                        <option value="refunded">Reembolsado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha Desde</label>
                    <input type="date" class="form-control" id="dateFrom">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Fecha Hasta</label>
                    <input type="date" class="form-control" id="dateTo">
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Reservas -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" class="form-check-input" id="selectAll">
                            </th>
                            <th>ID</th>
                            <th>Usuario</th>
                            <th>Propiedad</th>
                            <th>Fechas</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Pago</th>
                            <th>Fecha Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservations as $reservation)
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input reservation-checkbox" 
                                       value="{{ $reservation->id }}">
                            </td>
                            <td>#{{ $reservation->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        <i class="fas fa-user-circle fa-2x text-muted"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $reservation->user->name }}</div>
                                        <small class="text-muted">{{ $reservation->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($reservation->property->images->count() > 0)
                                        <img src="{{ $reservation->property->images->first()->full_url }}" 
                                             class="rounded me-2" style="width: 40px; height: 30px; object-fit: cover;">
                                    @endif
                                    <div>
                                        <div class="fw-bold">{{ $reservation->property->name }}</div>
                                        <small class="text-muted">{{ $reservation->property->location }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="text-nowrap">
                                    <div class="fw-bold">{{ $reservation->start_date->format('d/m/Y') }}</div>
                                    <div class="text-muted">a {{ $reservation->end_date->format('d/m/Y') }}</div>
                                    <small class="badge bg-info">
                                        {{ $reservation->start_date->diffInDays($reservation->end_date) }} noches
                                    </small>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-primary">${{ number_format($reservation->total_price, 0) }}</div>
                                @if($reservation->amount_paid > 0)
                                    <small class="text-success">Pagado: ${{ number_format($reservation->amount_paid, 0) }}</small>
                                @endif
                            </td>
                            <td>
                                @switch($reservation->status)
                                    @case('pending')
                                        <span class="badge bg-warning">Pendiente</span>
                                        @break
                                    @case('approved')
                                        <span class="badge bg-success">Aprobada</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-danger">Rechazada</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-secondary">Cancelada</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @switch($reservation->payment_status)
                                    @case('pending')
                                        <span class="badge bg-warning">Pendiente</span>
                                        @break
                                    @case('paid')
                                        <span class="badge bg-success">Pagado</span>
                                        @break
                                    @case('partial')
                                        <span class="badge bg-info">Parcial</span>
                                        @break
                                    @case('refunded')
                                        <span class="badge bg-secondary">Reembolsado</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                <div class="text-nowrap">
                                    <div>{{ $reservation->created_at->format('d/m/Y') }}</div>
                                    <small class="text-muted">{{ $reservation->created_at->format('H:i') }}</small>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="viewReservation({{ $reservation->id }})"
                                            title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($reservation->status === 'pending')
                                        <button class="btn btn-sm btn-outline-success" 
                                                onclick="approveReservation({{ $reservation->id }})"
                                                title="Aprobar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="rejectReservation({{ $reservation->id }})"
                                                title="Rechazar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    @if($reservation->status === 'approved')
                                        <button class="btn btn-sm btn-outline-warning" 
                                                onclick="updatePayment({{ $reservation->id }})"
                                                title="Actualizar pago">
                                            <i class="fas fa-credit-card"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $reservations->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal para actualizar pago -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Actualizar Estado de Pago</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    <div class="mb-3">
                        <label class="form-label">Estado de Pago</label>
                        <select class="form-select" name="payment_status" required>
                            <option value="pending">Pendiente</option>
                            <option value="paid">Pagado</option>
                            <option value="partial">Parcial</option>
                            <option value="refunded">Reembolsado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Monto Pagado</label>
                        <input type="number" class="form-control" name="amount_paid" step="0.01" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas del Admin</label>
                        <textarea class="form-control" name="admin_notes" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="savePayment()">Guardar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para notas del admin -->
<div class="modal fade" id="notesModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notesModalTitle">Acción en Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="notesForm">
                    <div class="mb-3">
                        <label class="form-label">Notas del Admin</label>
                        <textarea class="form-control" name="admin_notes" rows="3" placeholder="Agregar notas..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmActionBtn">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentReservationId = null;
let currentAction = null;

// Seleccionar todas las reservas
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.reservation-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Ver reserva
function viewReservation(id) {
    window.open(`/admin/reservations/${id}`, '_blank');
}

// Aprobar reserva
function approveReservation(id) {
    currentReservationId = id;
    currentAction = 'approve';
    document.getElementById('notesModalTitle').textContent = 'Aprobar Reserva';
    document.getElementById('confirmActionBtn').className = 'btn btn-success';
    document.getElementById('confirmActionBtn').innerHTML = '<i class="fas fa-check me-2"></i>Aprobar';
    new bootstrap.Modal(document.getElementById('notesModal')).show();
}

// Rechazar reserva
function rejectReservation(id) {
    currentReservationId = id;
    currentAction = 'reject';
    document.getElementById('notesModalTitle').textContent = 'Rechazar Reserva';
    document.getElementById('confirmActionBtn').className = 'btn btn-danger';
    document.getElementById('confirmActionBtn').innerHTML = '<i class="fas fa-times me-2"></i>Rechazar';
    new bootstrap.Modal(document.getElementById('notesModal')).show();
}

// Actualizar pago
function updatePayment(id) {
    currentReservationId = id;
    new bootstrap.Modal(document.getElementById('paymentModal')).show();
}

// Guardar pago
function savePayment() {
    const form = document.getElementById('paymentForm');
    const formData = new FormData(form);
    
    fetch(`/admin/reservations/${currentReservationId}/payment`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

// Confirmar acción
document.getElementById('confirmActionBtn').addEventListener('click', function() {
    const form = document.getElementById('notesForm');
    const formData = new FormData(form);
    
    fetch(`/admin/reservations/${currentReservationId}/${currentAction}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
});

// Acción masiva
function bulkAction(action) {
    const checkboxes = document.querySelectorAll('.reservation-checkbox:checked');
    const ids = Array.from(checkboxes).map(cb => cb.value);
    
    if (ids.length === 0) {
        alert('Selecciona al menos una reserva');
        return;
    }
    
    if (!confirm(`¿Estás seguro de que quieres ${action === 'approve' ? 'aprobar' : action === 'reject' ? 'rechazar' : 'cancelar'} ${ids.length} reserva(s)?`)) {
        return;
    }
    
    fetch('/admin/reservations/bulk-action', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: action,
            reservation_ids: ids
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

// Filtros
document.getElementById('statusFilter').addEventListener('change', function() {
    // Implementar filtro por estado
});

document.getElementById('paymentFilter').addEventListener('change', function() {
    // Implementar filtro por estado de pago
});
</script>
@endsection
