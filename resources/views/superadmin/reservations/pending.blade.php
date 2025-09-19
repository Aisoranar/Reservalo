@extends('layouts.app')

@section('title', 'Reservas Pendientes - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-clock text-warning me-2"></i>
                        Reservas Pendientes
                    </h1>
                    <p class="text-muted mb-0">Reservas que requieren aprobación</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('superadmin.reservations') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i>Ver Todas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $reservations->count() }}</h4>
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
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ \App\Models\Reservation::where('status', 'approved')->count() }}</h4>
                            <p class="mb-0">Confirmadas</p>
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
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ \App\Models\Reservation::where('status', 'cancelled')->count() }}</h4>
                            <p class="mb-0">Canceladas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-ban fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Reservas Pendientes -->
    <div class="row">
        @forelse($reservations as $reservation)
            <div class="col-xl-4 col-lg-6 mb-4">
                <div class="card shadow h-100 border-warning">
                    <div class="card-header bg-warning text-dark">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold">
                                <i class="fas fa-clock me-2"></i>Reserva #{{ $reservation->id }}
                            </h6>
                            <span class="badge bg-dark">PENDIENTE</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Información del Usuario -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                    {{ substr($reservation->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $reservation->user->name }}</div>
                                    <small class="text-muted">{{ $reservation->user->email }}</small>
                                </div>
                            </div>
                        </div>

                        <!-- Información de la Propiedad -->
                        <div class="mb-3">
                            <h6 class="fw-bold text-primary">{{ $reservation->property->title }}</h6>
                            <p class="text-muted small mb-1">{{ $reservation->property->location }}</p>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-bed me-1 text-muted"></i>
                                <small class="text-muted">{{ $reservation->property->bedrooms }} habitaciones</small>
                                <i class="fas fa-users ms-3 me-1 text-muted"></i>
                                <small class="text-muted">{{ $reservation->property->capacity }} huéspedes</small>
                            </div>
                        </div>

                        <!-- Fechas de Reserva -->
                        <div class="mb-3">
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="fw-bold text-primary">Check-in</div>
                                        <div>{{ $reservation->check_in->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $reservation->check_in->format('H:i') }}</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded">
                                        <div class="fw-bold text-primary">Check-out</div>
                                        <div>{{ $reservation->check_out->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $reservation->check_out->format('H:i') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mt-2">
                                <span class="badge bg-info">{{ $reservation->nights }} noche(s)</span>
                            </div>
                        </div>

                        <!-- Detalles de la Reserva -->
                        <div class="mb-3">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="fw-bold text-primary">Huéspedes</div>
                                    <div>{{ $reservation->guests }}</div>
                                </div>
                                <div class="col-4">
                                    <div class="fw-bold text-primary">Total</div>
                                    <div>${{ number_format($reservation->total_amount, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-4">
                                    <div class="fw-bold text-primary">Creada</div>
                                    <div>{{ $reservation->created_at->format('d/m') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Notas del Usuario -->
                        @if($reservation->notes)
                            <div class="mb-3">
                                <div class="fw-bold text-muted">Notas del Usuario:</div>
                                <div class="bg-light p-2 rounded">
                                    <small>{{ $reservation->notes }}</small>
                                </div>
                            </div>
                        @endif

                        <!-- Acciones -->
                        <div class="d-grid gap-2">
                            <a href="{{ route('superadmin.reservations.show', $reservation) }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>Ver Detalles
                            </a>
                            <div class="row g-2">
                                <div class="col-6">
                                    <button type="button" 
                                            class="btn btn-success btn-sm w-100" 
                                            onclick="approveReservation({{ $reservation->id }})">
                                        <i class="fas fa-check me-1"></i>Aprobar
                                    </button>
                                </div>
                                <div class="col-6">
                                    <button type="button" 
                                            class="btn btn-danger btn-sm w-100" 
                                            onclick="rejectReservation({{ $reservation->id }})">
                                        <i class="fas fa-times me-1"></i>Rechazar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5 class="text-muted">¡Excelente!</h5>
                        <p class="text-muted">No hay reservas pendientes de aprobación en este momento.</p>
                        <a href="{{ route('superadmin.reservations') }}" class="btn btn-primary">
                            <i class="fas fa-list me-1"></i>Ver Todas las Reservas
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
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
let currentReservationId = null;

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
