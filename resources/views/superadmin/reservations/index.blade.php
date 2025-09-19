@extends('layouts.app')

@section('title', 'Gestión de Reservas - Super Admin')

@section('content')
<div class="container-fluid py-4">
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
                    <a href="{{ route('superadmin.reservations.pending') }}" class="btn btn-warning">
                        <i class="fas fa-clock me-1"></i>Pendientes
                        @if(\App\Models\Reservation::where('status', 'pending')->count() > 0)
                            <span class="badge bg-danger ms-1">{{ \App\Models\Reservation::where('status', 'pending')->count() }}</span>
                        @endif
                    </a>
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
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
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
                                        <tr>
                                            <td>
                                                <strong>#{{ $reservation->id }}</strong>
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
                                                @endswitch
                                            </td>
                                            <td>
                                                <div>{{ $reservation->created_at->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $reservation->created_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('superadmin.reservations.show', $reservation) }}" 
                                                       class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($reservation->status === 'pending')
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-success" 
                                                                onclick="approveReservation({{ $reservation->id }})" 
                                                                title="Aprobar">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                onclick="rejectReservation({{ $reservation->id }})" 
                                                                title="Rechazar">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-info" 
                                                            onclick="sendEmail({{ $reservation->id }})" 
                                                            title="Enviar correo">
                                                        <i class="fas fa-envelope"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="d-flex justify-content-center">
                            {{ $reservations->links() }}
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
            alert('Correo enviado correctamente');
            $('#emailModal').modal('hide');
            // Limpiar el formulario
            document.getElementById('emailForm').reset();
        } else {
            alert('Error: ' + (data.message || 'No se pudo enviar el correo'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al enviar el correo');
    });
});
</script>
@endpush
@endsection
