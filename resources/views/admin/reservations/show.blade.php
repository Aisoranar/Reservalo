@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Detalle de Reserva #{{ $reservation->id }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reservations.index') }}">Reservas</a></li>
                    <li class="breadcrumb-item active">Reserva #{{ $reservation->id }}</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @if($reservation->status === 'pending')
                <button class="btn btn-success" onclick="approveReservation({{ $reservation->id }})">
                    <i class="fas fa-check me-2"></i>Aprobar
                </button>
                <button class="btn btn-warning" onclick="rejectReservation({{ $reservation->id }})">
                    <i class="fas fa-times me-2"></i>Rechazar
                </button>
            @endif
            @if($reservation->status === 'approved')
                <button class="btn btn-danger" onclick="cancelReservation({{ $reservation->id }})">
                    <i class="fas fa-ban me-2"></i>Cancelar
                </button>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Información de la reserva -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                        Detalles de la Reserva
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-muted small">Estado:</label>
                                <div>
                                    @switch($reservation->status)
                                        @case('pending')
                                            <span class="badge bg-warning">Pendiente de Aprobación</span>
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
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="fw-bold text-muted small">Fechas:</label>
                                <div>
                                    <strong>Check-in:</strong> {{ $reservation->start_date->format('d/m/Y') }}<br>
                                    <strong>Check-out:</strong> {{ $reservation->end_date->format('d/m/Y') }}<br>
                                    <strong>Noches:</strong> {{ $reservation->start_date->diffInDays($reservation->end_date) }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-muted small">Estado del Pago:</label>
                                <div>
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
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="fw-bold text-muted small">Monto:</label>
                                <div>
                                    <strong>Total:</strong> ${{ number_format($reservation->total_amount, 0) }}<br>
                                    <strong>Pagado:</strong> ${{ number_format($reservation->amount_paid, 0) }}<br>
                                    @if($reservation->amount_paid < $reservation->total_amount)
                                        <strong>Pendiente:</strong> ${{ number_format($reservation->total_amount - $reservation->amount_paid, 0) }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if($reservation->special_requests)
                        <div class="mb-3">
                            <label class="fw-bold text-muted small">Solicitudes Especiales:</label>
                            <div class="p-3 bg-light rounded">
                                {{ $reservation->special_requests }}
                            </div>
                        </div>
                    @endif
                    
                    @if($reservation->admin_notes)
                        <div class="mb-3">
                            <label class="fw-bold text-muted small">Notas del Admin:</label>
                            <div class="p-3 bg-light rounded">
                                {{ $reservation->admin_notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Información del usuario -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-user text-primary me-2"></i>
                        Información del Usuario
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-muted small">Nombre:</label>
                                <div>{{ $reservation->user->name }}</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="fw-bold text-muted small">Email:</label>
                                <div>{{ $reservation->user->email }}</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-muted small">Teléfono:</label>
                                <div>{{ $reservation->user->phone ?? 'No especificado' }}</div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="fw-bold text-muted small">Miembro desde:</label>
                                <div>{{ $reservation->user->created_at->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Acciones rápidas -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-cogs text-primary me-2"></i>
                        Acciones
                    </h5>
                </div>
                <div class="card-body p-4">
                    @if($reservation->status === 'pending')
                        <div class="mb-3">
                            <label class="fw-bold text-muted small">Notas para la aprobación:</label>
                            <textarea class="form-control" id="approval-notes" rows="3" placeholder="Notas opcionales..."></textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button class="btn btn-success" onclick="approveReservation({{ $reservation->id }})">
                                <i class="fas fa-check me-2"></i>Aprobar Reserva
                            </button>
                            <button class="btn btn-warning" onclick="rejectReservation({{ $reservation->id }})">
                                <i class="fas fa-times me-2"></i>Rechazar Reserva
                            </button>
                        </div>
                    @endif
                    
                    @if($reservation->status === 'approved')
                        <div class="mb-3">
                            <label class="fw-bold text-muted small">Estado del Pago:</label>
                            <select class="form-control" id="payment-status">
                                <option value="pending" {{ $reservation->payment_status === 'pending' ? 'selected' : '' }}>Pendiente</option>
                                <option value="partial" {{ $reservation->payment_status === 'partial' ? 'selected' : '' }}>Parcial</option>
                                <option value="paid" {{ $reservation->payment_status === 'paid' ? 'selected' : '' }}>Pagado</option>
                                <option value="refunded" {{ $reservation->payment_status === 'refunded' ? 'selected' : '' }}>Reembolsado</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="fw-bold text-muted small">Monto Pagado:</label>
                            <input type="number" class="form-control" id="amount-paid" value="{{ $reservation->amount_paid }}" min="0" max="{{ $reservation->total_amount }}">
                        </div>
                        
                        <div class="mb-3">
                            <label class="fw-bold text-muted small">Notas del Admin:</label>
                            <textarea class="form-control" id="admin-notes" rows="3" placeholder="Notas opcionales...">{{ $reservation->admin_notes }}</textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" onclick="updatePayment({{ $reservation->id }})">
                                <i class="fas fa-credit-card me-2"></i>Actualizar Pago
                            </button>
                            <button class="btn btn-danger" onclick="cancelReservation({{ $reservation->id }})">
                                <i class="fas fa-ban me-2"></i>Cancelar Reserva
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Información de la propiedad -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-home text-primary me-2"></i>
                        Propiedad
                    </h5>
                </div>
                <div class="card-body p-4">
                    <h6 class="mb-2">{{ $reservation->property->name }}</h6>
                    <p class="text-muted small mb-2">{{ $reservation->property->location }}</p>
                    
                    @if($reservation->property->images->count() > 0)
                        <img src="{{ $reservation->property->images->first()->full_url }}" 
                             alt="{{ $reservation->property->name }}" 
                             class="img-fluid rounded mb-3" 
                             style="max-height: 150px; width: 100%; object-fit: cover;">
                    @endif
                    
                    <div class="d-grid">
                        <a href="{{ route('properties.show', $reservation->property) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye me-2"></i>Ver Propiedad
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para confirmar acciones -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalTitle">Confirmar Acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="confirmModalBody">
                ¿Estás seguro de que quieres realizar esta acción?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmModalBtn">Confirmar</button>
            </div>
        </div>
    </div>
</div>

<script>
function approveReservation(reservationId) {
    const notes = document.getElementById('approval-notes')?.value || '';
    
    fetch(`/admin/reservations/${reservationId}/approve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ admin_notes: notes })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Reserva aprobada exitosamente', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('Error al aprobar la reserva', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error al aprobar la reserva', 'danger');
    });
}

function rejectReservation(reservationId) {
    const notes = document.getElementById('approval-notes')?.value || '';
    
    fetch(`/admin/reservations/${reservationId}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ admin_notes: notes })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Reserva rechazada exitosamente', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('Error al rechazar la reserva', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error al rechazar la reserva', 'danger');
    });
}

function cancelReservation(reservationId) {
    showConfirmModal(
        'Cancelar Reserva',
        '¿Estás seguro de que quieres cancelar esta reserva? Esta acción no se puede deshacer.',
        () => {
            fetch(`/admin/reservations/${reservationId}/cancel`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ admin_notes: document.getElementById('admin-notes')?.value || '' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Reserva cancelada exitosamente', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert('Error al cancelar la reserva', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error al cancelar la reserva', 'danger');
            });
        }
    );
}

function updatePayment(reservationId) {
    const paymentStatus = document.getElementById('payment-status').value;
    const amountPaid = document.getElementById('amount-paid').value;
    const adminNotes = document.getElementById('admin-notes').value;
    
    fetch(`/admin/reservations/${reservationId}/payment`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            payment_status: paymentStatus,
            amount_paid: amountPaid,
            admin_notes: adminNotes
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Pago actualizado exitosamente', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('Error al actualizar el pago', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error al actualizar el pago', 'danger');
    });
}

function showConfirmModal(title, message, onConfirm) {
    document.getElementById('confirmModalTitle').textContent = title;
    document.getElementById('confirmModalBody').textContent = message;
    document.getElementById('confirmModalBtn').onclick = onConfirm;
    
    const modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}
</script>
@endsection
