@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2 mb-0">
        <i class="fas fa-calendar-check me-2"></i>Gestión de Reservas
    </h1>
    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reservations') }}" class="row g-3">
            <div class="col-md-3">
                <label for="status" class="form-label">Estado</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos los estados</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Aprobada</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rechazada</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="property_id" class="form-label">Propiedad</label>
                <select class="form-select" id="property_id" name="property_id">
                    <option value="">Todas las propiedades</option>
                    @foreach(\App\Models\Property::all() as $property)
                        <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                            {{ $property->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="start_date" class="form-label">Fecha desde</label>
                <input type="date" class="form-control" id="start_date" name="start_date" 
                       value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">Fecha hasta</label>
                <input type="date" class="form-control" id="end_date" name="end_date" 
                       value="{{ request('end_date') }}">
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-2"></i>Filtrar
                </button>
                <a href="{{ route('admin.reservations') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Limpiar
                </a>
                <a href="{{ route('admin.export') }}" class="btn btn-success float-end">
                    <i class="fas fa-download me-2"></i>Exportar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Lista de reservas -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            Reservas ({{ $reservations->total() }})
        </h5>
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
                            <th>Noches</th>
                            <th>Precio Total</th>
                            <th>Estado</th>
                            <th>Fecha Solicitud</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reservations as $reservation)
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#{{ $reservation->id }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                             style="width: 32px; height: 32px;">
                                            <span class="text-white fw-bold">
                                                {{ strtoupper(substr($reservation->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $reservation->user->name }}</div>
                                            <small class="text-muted">{{ $reservation->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($reservation->property->primaryImage)
                                            <img src="{{ $reservation->property->primaryImage->full_url }}" 
                                                 class="rounded me-2" alt="{{ $reservation->property->name }}"
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center"
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-home text-muted"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $reservation->property->name }}</div>
                                            <small class="text-muted">{{ $reservation->property->location }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-center">
                                        <div class="fw-bold">{{ $reservation->start_date->format('d/m/Y') }}</div>
                                        <small class="text-muted">hasta</small><br>
                                        <div class="fw-bold">{{ $reservation->end_date->format('d/m/Y') }}</div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $reservation->nights }} noche(s)</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">${{ number_format($reservation->total_price, 2) }}</span>
                                </td>
                                <td>{!! $reservation->status_badge !!}</td>
                                <td>
                                    <small class="text-muted">
                                        {{ $reservation->created_at->format('d/m/Y H:i') }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($reservation->status === 'pending')
                                            <button type="button" class="btn btn-success btn-sm" 
                                                    onclick="approveReservation({{ $reservation->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="rejectReservation({{ $reservation->id }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        <button type="button" class="btn btn-info btn-sm" 
                                                onclick="viewReservation({{ $reservation->id }})">
                                            <i class="fas fa-eye"></i>
                                        </button>
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
        @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h4>No se encontraron reservas</h4>
                <p class="text-muted">Intenta ajustar los filtros de búsqueda.</p>
            </div>
        @endif
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
            <form id="rejectForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Motivo del rechazo</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" 
                                  rows="3" required placeholder="Explica el motivo del rechazo..."></textarea>
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
@endsection

@push('scripts')
<script>
function approveReservation(reservationId) {
    if (confirm('¿Estás seguro de que quieres aprobar esta reserva?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/reservations/${reservationId}/approve`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'PATCH';
        
        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

function rejectReservation(reservationId) {
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    const form = document.getElementById('rejectForm');
    form.action = `/admin/reservations/${reservationId}/reject`;
    modal.show();
}

function viewReservation(reservationId) {
    window.open(`/reservas/${reservationId}`, '_blank');
}
</script>
@endpush
