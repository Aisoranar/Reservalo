@extends('layouts.app')

@section('title', 'Detalles de Reserva - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-calendar-check text-primary me-2"></i>
                        Detalles de Reserva #{{ $reservation->id }}
                    </h1>
                    <p class="text-muted mb-0">Información completa de la reserva</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('superadmin.reservations') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                    @if($reservation->status === 'pending')
                        <button type="button" class="btn btn-success" onclick="approveReservation({{ $reservation->id }})">
                            <i class="fas fa-check me-1"></i>Aprobar
                        </button>
                        <button type="button" class="btn btn-danger" onclick="rejectReservation({{ $reservation->id }})">
                            <i class="fas fa-times me-1"></i>Rechazar
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <!-- Estado de la Reserva -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Estado de la Reserva
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Estado Actual:</strong>
                                <p class="text-muted">
                                    @switch($reservation->status)
                                        @case('pending')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-clock me-1"></i>Pendiente
                                            </span>
                                            @break
                                        @case('approved')
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Confirmada
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
                                    @endswitch
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Fecha de Creación:</strong>
                                <p class="text-muted">{{ $reservation->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    @if($reservation->approved_at)
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Aprobada por:</strong>
                                <p class="text-muted">{{ $reservation->approvedBy->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Fecha de Aprobación:</strong>
                                <p class="text-muted">{{ $reservation->approved_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($reservation->rejected_at)
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Rechazada por:</strong>
                                <p class="text-muted">{{ $reservation->rejectedBy->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Fecha de Rechazo:</strong>
                                <p class="text-muted">{{ $reservation->rejected_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Información del Usuario -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user me-2"></i>Información del Usuario
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3">
                                    {{ substr($reservation->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <h5 class="mb-0">{{ $reservation->user->name }}</h5>
                                    <p class="text-muted mb-0">{{ $reservation->user->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Teléfono:</strong>
                                <p class="text-muted">{{ $reservation->user->phone ?? 'No disponible' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Miembro desde:</strong>
                            <p class="text-muted">{{ $reservation->user->created_at->format('d/m/Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Último acceso:</strong>
                            <p class="text-muted">{{ $reservation->user->last_login_at ? $reservation->user->last_login_at->format('d/m/Y H:i') : 'Nunca' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de la Propiedad -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-home me-2"></i>Información de la Propiedad
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h5 class="text-primary">{{ $reservation->property->title }}</h5>
                            <p class="text-muted">{{ $reservation->property->location }}</p>
                            <p class="text-muted">{{ $reservation->property->description }}</p>
                        </div>
                        <div class="col-md-4">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="fw-bold text-primary">{{ $reservation->property->bedrooms }}</div>
                                    <small class="text-muted">Habitaciones</small>
                                </div>
                                <div class="col-6">
                                    <div class="fw-bold text-primary">{{ $reservation->property->capacity }}</div>
                                    <small class="text-muted">Huéspedes</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles de la Reserva -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-alt me-2"></i>Detalles de la Reserva
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-center p-3 bg-light rounded mb-3">
                                <div class="fw-bold text-primary">Check-in</div>
                                <div class="h4">{{ $reservation->check_in->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $reservation->check_in->format('H:i') }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-center p-3 bg-light rounded mb-3">
                                <div class="fw-bold text-primary">Check-out</div>
                                <div class="h4">{{ $reservation->check_out->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $reservation->check_out->format('H:i') }}</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="fw-bold text-primary">{{ $reservation->nights }}</div>
                            <small class="text-muted">Noches</small>
                        </div>
                        <div class="col-md-3">
                            <div class="fw-bold text-primary">{{ $reservation->guests }}</div>
                            <small class="text-muted">Huéspedes</small>
                        </div>
                        <div class="col-md-3">
                            <div class="fw-bold text-primary">${{ number_format($reservation->price_per_night, 0, ',', '.') }}</div>
                            <small class="text-muted">Por noche</small>
                        </div>
                        <div class="col-md-3">
                            <div class="fw-bold text-primary">${{ number_format($reservation->total_amount, 0, ',', '.') }}</div>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>

                    @if($reservation->notes)
                        <div class="mt-3">
                            <strong>Notas del Usuario:</strong>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0">{{ $reservation->notes }}</p>
                            </div>
                        </div>
                    @endif

                    @if($reservation->admin_notes)
                        <div class="mt-3">
                            <strong>Notas del Administrador:</strong>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0">{{ $reservation->admin_notes }}</p>
                            </div>
                        </div>
                    @endif

                    @if($reservation->rejection_reason)
                        <div class="mt-3">
                            <strong>Motivo del Rechazo:</strong>
                            <div class="bg-danger bg-opacity-10 p-3 rounded">
                                <p class="mb-0 text-danger">{{ $reservation->rejection_reason }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="col-lg-4">
            <!-- Acciones Rápidas -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($reservation->status === 'pending')
                            <button type="button" class="btn btn-success" onclick="approveReservation({{ $reservation->id }})">
                                <i class="fas fa-check me-2"></i>Aprobar Reserva
                            </button>
                            <button type="button" class="btn btn-danger" onclick="rejectReservation({{ $reservation->id }})">
                                <i class="fas fa-times me-2"></i>Rechazar Reserva
                            </button>
                        @elseif($reservation->status === 'approved')
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i>
                                Esta reserva ya ha sido aprobada
                            </div>
                        @elseif($reservation->status === 'rejected')
                            <div class="alert alert-danger">
                                <i class="fas fa-times-circle me-2"></i>
                                Esta reserva ha sido rechazada
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Información Adicional -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info me-2"></i>Información Adicional
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>ID de Reserva:</strong>
                        <p class="text-muted">#{{ $reservation->id }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Código de Confirmación:</strong>
                        <p class="text-muted">{{ $reservation->confirmation_code ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Método de Pago:</strong>
                        <p class="text-muted">{{ $reservation->payment_method ?? 'No especificado' }}</p>
                    </div>
                    <div class="mb-3">
                        <strong>Última Actualización:</strong>
                        <p class="text-muted">{{ $reservation->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
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

@push('scripts')
<script>
let currentReservationId = {{ $reservation->id }};

function approveReservation(reservationId) {
    currentReservationId = reservationId;
    $('#approveModal').modal('show');
}

function rejectReservation(reservationId) {
    currentReservationId = reservationId;
    $('#rejectModal').modal('show');
}

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
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'No se pudo aprobar la reserva'));
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
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'No se pudo rechazar la reserva'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al rechazar la reserva');
    });
});
</script>
@endpush
@endsection
