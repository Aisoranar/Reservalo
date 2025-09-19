@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-eye text-primary me-2"></i>
                        Detalles de Membresía
                    </h1>
                    <p class="text-muted mb-0">Información completa de la membresía</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('superadmin.memberships') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                    <a href="{{ route('superadmin.memberships.edit', $membership) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Editar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Información Principal -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-primary">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-user-circle fa-4x text-primary mb-3"></i>
                                <h6 class="text-muted">Usuario</h6>
                                <h4 class="mb-1">{{ $membership->user->name }}</h4>
                                <p class="text-muted mb-0">{{ $membership->user->email }}</p>
                                <small class="text-muted">{{ $membership->user->phone }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-crown fa-4x text-warning mb-3"></i>
                                <h6 class="text-muted">Plan de Membresía</h6>
                                <h4 class="mb-1">{{ $membership->plan->name }}</h4>
                                <p class="text-muted mb-0">${{ number_format($membership->plan->price, 0) }}</p>
                                <small class="text-muted">{{ $membership->plan->duration_days }} días</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-calendar-alt fa-4x text-success mb-3"></i>
                                <h6 class="text-muted">Período</h6>
                                <h5 class="mb-1">{{ \Carbon\Carbon::parse($membership->starts_at)->format('d/m/Y') }}</h5>
                                <p class="text-muted mb-0">hasta</p>
                                <h5 class="mb-0">{{ \Carbon\Carbon::parse($membership->expires_at)->format('d/m/Y') }}</h5>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-toggle-on fa-4x text-{{ $membership->status === 'active' ? 'success' : ($membership->status === 'inactive' ? 'warning' : 'danger') }} mb-3"></i>
                                <h6 class="text-muted">Estado</h6>
                                <h4 class="mb-1">
                                    <span class="badge bg-{{ $membership->status === 'active' ? 'success' : ($membership->status === 'inactive' ? 'warning' : 'danger') }} fs-6">
                                        {{ ucfirst($membership->status) }}
                                    </span>
                                </h4>
                                @if($membership->isActive())
                                    <small class="text-success">
                                        <i class="fas fa-check-circle me-1"></i>Activa
                                    </small>
                                @elseif($membership->isExpired())
                                    <small class="text-danger">
                                        <i class="fas fa-times-circle me-1"></i>Expirada
                                    </small>
                                @else
                                    <small class="text-warning">
                                        <i class="fas fa-pause-circle me-1"></i>Inactiva
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalles Adicionales -->
    <div class="row">
        <!-- Información de la Membresía -->
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Información de la Membresía
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Datos Básicos</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td>{{ $membership->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha de Inicio:</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($membership->starts_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha de Expiración:</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($membership->expires_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Duración Total:</strong></td>
                                    <td>{{ $membership->starts_at->diffInDays($membership->expires_at) }} días</td>
                                </tr>
                                <tr>
                                    <td><strong>Precio Pagado:</strong></td>
                                    <td>${{ number_format($membership->price_paid ?? 0, 0) }} {{ $membership->currency ?? 'COP' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Progreso</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Días Transcurridos:</strong></td>
                                    <td>{{ $membership->days_elapsed }} días</td>
                                </tr>
                                <tr>
                                    <td><strong>Días Restantes:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $membership->days_remaining > 7 ? 'success' : ($membership->days_remaining > 0 ? 'warning' : 'danger') }}">
                                            {{ $membership->days_remaining }} días
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Progreso:</strong></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-{{ $membership->progress_percentage > 80 ? 'success' : ($membership->progress_percentage > 50 ? 'warning' : 'info') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $membership->progress_percentage }}%"
                                                 aria-valuenow="{{ $membership->progress_percentage }}" 
                                                 aria-valuemin="0" 
                                                 aria-valuemax="100">
                                                {{ number_format($membership->progress_percentage, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Auto Renovación:</strong></td>
                                    <td>
                                        @if($membership->auto_renew)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check me-1"></i>Activada
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-times me-1"></i>Desactivada
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($membership->notes)
                        <div class="mt-3">
                            <h6 class="text-muted">Notas</h6>
                            <div class="alert alert-light">
                                <i class="fas fa-sticky-note me-2"></i>
                                {{ $membership->notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información del Usuario -->
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        Información del Usuario
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-user-circle fa-3x text-info"></i>
                    </div>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>Nombre:</strong></td>
                            <td>{{ $membership->user->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $membership->user->email }}</td>
                        </tr>
                        <tr>
                            <td><strong>Teléfono:</strong></td>
                            <td>{{ $membership->user->phone ?? 'No especificado' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tipo de Cuenta:</strong></td>
                            <td>
                                <span class="badge bg-{{ $membership->user->account_type === 'business' ? 'primary' : 'secondary' }}">
                                    {{ ucfirst($membership->user->account_type) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Estado:</strong></td>
                            <td>
                                @if($membership->user->is_active)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Activo
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times me-1"></i>Inactivo
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Miembro desde:</strong></td>
                            <td>{{ \Carbon\Carbon::parse($membership->user->created_at)->format('d/m/Y') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card shadow mt-3">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('superadmin.memberships.edit', $membership) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>Editar Membresía
                        </a>
                        
                        @if($membership->status === 'active')
                            <button type="button" class="btn btn-secondary" onclick="toggleStatus({{ $membership->id }}, 'inactive')">
                                <i class="fas fa-pause me-1"></i>Suspender
                            </button>
                        @elseif($membership->status === 'inactive')
                            <button type="button" class="btn btn-success" onclick="toggleStatus({{ $membership->id }}, 'active')">
                                <i class="fas fa-play me-1"></i>Activar
                            </button>
                        @endif
                        
                        <button type="button" class="btn btn-danger" onclick="deleteMembership({{ $membership->id }})">
                            <i class="fas fa-trash me-1"></i>Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres eliminar esta membresía?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>¡Atención!</strong> Esta acción no se puede deshacer.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <form method="POST" action="{{ route('superadmin.memberships.destroy', $membership) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 4px solid #4e73df !important;
}

.border-left-info {
    border-left: 4px solid #36b9cc !important;
}

.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

.btn {
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}
</style>

<script>
function deleteMembership(membershipId) {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

function toggleStatus(membershipId, newStatus) {
    const statusText = newStatus === 'active' ? 'activar' : 'suspender';
    
    if (confirm(`¿Estás seguro de que quieres ${statusText} esta membresía?`)) {
        fetch(`/superadmin/memberships/${membershipId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                status: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cambiar el estado de la membresía');
        });
    }
}
</script>
@endsection
