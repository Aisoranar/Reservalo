@extends('layouts.app')

@section('title', 'Gestión de Membresías - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-users-cog text-primary me-2"></i>
                        Gestión de Membresías
                    </h1>
                    <p class="text-muted mb-0">Administra las membresías de los usuarios</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('superadmin.memberships.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nueva Membresía
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
                            <label class="form-label">Buscar Usuario</label>
                            <input type="text" class="form-control" name="search" 
                                   value="{{ request('search') }}" placeholder="Nombre o email...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="status">
                                <option value="">Todos</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expirado</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelado</option>
                                <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspendido</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Plan</label>
                            <select class="form-select" name="plan">
                                <option value="">Todos</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}" {{ request('plan') == $plan->id ? 'selected' : '' }}>
                                        {{ $plan->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Expiración</label>
                            <select class="form-select" name="expiration">
                                <option value="">Todos</option>
                                <option value="expiring_soon" {{ request('expiration') === 'expiring_soon' ? 'selected' : '' }}>Próximas a expirar</option>
                                <option value="expired" {{ request('expiration') === 'expired' ? 'selected' : '' }}>Expiradas</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                            <a href="{{ route('superadmin.memberships') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Membresías -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Lista de Membresías ({{ $memberships->total() }} total)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>Usuario</th>
                                    <th>Plan</th>
                                    <th>Estado</th>
                                    <th>Período</th>
                                    <th>Precio</th>
                                    <th>Progreso</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($memberships as $membership)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                                                     style="width: 40px; height: 40px;">
                                                    {{ substr($membership->user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $membership->user->name }}</div>
                                                    <small class="text-muted">{{ $membership->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                <span class="badge bg-info">{{ $membership->plan->name }}</span>
                                                <br><small class="text-muted">{{ $membership->plan->duration_text }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($membership->status === 'active')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Activo
                                                </span>
                                            @elseif($membership->status === 'expired')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Expirado
                                                </span>
                                            @elseif($membership->status === 'cancelled')
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-ban me-1"></i>Cancelado
                                                </span>
                                            @elseif($membership->status === 'suspended')
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-pause me-1"></i>Suspendido
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <small class="text-muted">Inicio:</small>
                                                <div>{{ $membership->starts_at->format('d/m/Y') }}</div>
                                                <small class="text-muted">Fin:</small>
                                                <div>{{ $membership->expires_at->format('d/m/Y') }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $membership->formatted_price }}</div>
                                            @if($membership->auto_renew)
                                                <small class="text-success">
                                                    <i class="fas fa-sync me-1"></i>Auto-renovación
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($membership->status === 'active')
                                                <div class="progress" style="height: 20px;">
                                                    <div class="progress-bar bg-{{ $membership->progress_percentage > 80 ? 'success' : ($membership->progress_percentage > 60 ? 'warning' : 'info') }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $membership->progress_percentage }}%"
                                                         aria-valuenow="{{ $membership->progress_percentage }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                        {{ number_format($membership->progress_percentage, 1) }}%
                                                    </div>
                                                </div>
                                                <small class="text-muted">
                                                    {{ $membership->days_remaining }} días restantes
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('superadmin.memberships.show', $membership) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('superadmin.memberships.edit', $membership) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($membership->status === 'active')
                                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                                            onclick="suspendMembership({{ $membership->id }})" 
                                                            title="Suspender">
                                                        <i class="fas fa-pause"></i>
                                                    </button>
                                                @elseif($membership->status === 'suspended')
                                                    <button type="button" class="btn btn-sm btn-outline-success" 
                                                            onclick="reactivateMembership({{ $membership->id }})" 
                                                            title="Reactivar">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                @endif
                                                @if($membership->status !== 'cancelled')
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="cancelMembership({{ $membership->id }})" 
                                                            title="Cancelar">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-users-cog fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No se encontraron membresías</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($memberships->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $memberships->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function suspendMembership(membershipId) {
    if (confirm('¿Estás seguro de que quieres suspender esta membresía?')) {
        fetch(`/superadmin/memberships/${membershipId}/suspend`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'No se pudo suspender la membresía'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al suspender la membresía');
        });
    }
}

function reactivateMembership(membershipId) {
    if (confirm('¿Estás seguro de que quieres reactivar esta membresía?')) {
        fetch(`/superadmin/memberships/${membershipId}/reactivate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'No se pudo reactivar la membresía'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al reactivar la membresía');
        });
    }
}

function cancelMembership(membershipId) {
    const reason = prompt('¿Cuál es la razón de la cancelación?');
    if (reason) {
        fetch(`/superadmin/memberships/${membershipId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'No se pudo cancelar la membresía'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cancelar la membresía');
        });
    }
}
</script>
@endpush
@endsection
