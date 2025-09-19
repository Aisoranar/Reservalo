@extends('layouts.app')

@section('title', 'Planes de Membresía - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-gem text-primary me-2"></i>
                        Planes de Membresía
                    </h1>
                    <p class="text-muted mb-0">Administra los planes de membresía del sistema</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('superadmin.membership-plans.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nuevo Plan
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Planes -->
    <div class="row">
        @forelse($plans as $plan)
            <div class="col-xl-4 col-lg-6 mb-4">
                <div class="card shadow h-100 {{ $plan->is_default ? 'border-warning' : '' }}">
                    @if($plan->is_default)
                        <div class="card-header bg-warning text-dark">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-star me-2"></i>Plan Predeterminado
                                </h6>
                                <span class="badge bg-dark">DEFAULT</span>
                            </div>
                        </div>
                    @else
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-gem me-2"></i>{{ $plan->name }}
                            </h6>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('superadmin.membership-plans.show', $plan) }}">
                                            <i class="fas fa-eye me-2"></i>Ver Detalles
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('superadmin.membership-plans.edit', $plan) }}">
                                            <i class="fas fa-edit me-2"></i>Editar
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button class="dropdown-item text-{{ $plan->is_active ? 'warning' : 'success' }}" 
                                                onclick="togglePlanStatus({{ $plan->id }}, {{ $plan->is_active ? 'false' : 'true' }})">
                                            <i class="fas fa-{{ $plan->is_active ? 'ban' : 'check' }} me-2"></i>
                                            {{ $plan->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </li>
                                    @if(!$plan->is_default)
                                        <li>
                                            <button class="dropdown-item text-danger" 
                                                    onclick="deletePlan({{ $plan->id }})">
                                                <i class="fas fa-trash me-2"></i>Eliminar
                                            </button>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @endif
                    
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <h3 class="text-primary">{{ $plan->formatted_price }}</h3>
                            <p class="text-muted">{{ $plan->duration_text }}</p>
                        </div>

                        <p class="text-muted mb-3">{{ $plan->description }}</p>

                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Propiedades</small>
                                <div class="fw-bold">
                                    @if($plan->max_properties === -1)
                                        <i class="fas fa-infinity text-success"></i> Ilimitadas
                                    @else
                                        {{ $plan->max_properties }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Reservas</small>
                                <div class="fw-bold">
                                    @if($plan->max_reservations === -1)
                                        <i class="fas fa-infinity text-success"></i> Ilimitadas
                                    @else
                                        {{ $plan->max_reservations }}/mes
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Estado</small>
                            <div>
                                @if($plan->is_active)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Activo
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle me-1"></i>Inactivo
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($plan->features && count($plan->features) > 0)
                            <div class="mb-3">
                                <small class="text-muted">Características</small>
                                <ul class="list-unstyled mt-1">
                                    @foreach(array_slice($plan->features, 0, 3) as $feature)
                                        <li class="small">
                                            <i class="fas fa-check text-success me-1"></i>{{ $feature }}
                                        </li>
                                    @endforeach
                                    @if(count($plan->features) > 3)
                                        <li class="small text-muted">
                                            +{{ count($plan->features) - 3 }} más...
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <small class="text-muted">Membresías Activas</small>
                            <div class="fw-bold text-primary">
                                @if(isset($plan->memberships_count))
                                    {{ $plan->memberships_count }} usuarios
                                @else
                                    {{ $plan->memberships()->count() }} usuarios
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Creado: {{ $plan->created_at->format('d/m/Y') }}
                            </small>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('superadmin.membership-plans.show', $plan) }}" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('superadmin.membership-plans.edit', $plan) }}" 
                                   class="btn btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-gem fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay planes de membresía</h5>
                        <p class="text-muted">Crea el primer plan para comenzar a ofrecer membresías.</p>
                        <a href="{{ route('superadmin.membership-plans.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Crear Primer Plan
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function togglePlanStatus(planId, newStatus) {
    const action = newStatus === 'true' ? 'activar' : 'desactivar';
    
    if (confirm(`¿Estás seguro de que quieres ${action} este plan?`)) {
        fetch(`/superadmin/membership-plans/${planId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ is_active: newStatus === 'true' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'No se pudo cambiar el estado del plan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cambiar el estado del plan');
        });
    }
}

function deletePlan(planId) {
    if (confirm('¿Estás seguro de que quieres eliminar este plan? Esta acción no se puede deshacer.')) {
        fetch(`/superadmin/membership-plans/${planId}`, {
            method: 'DELETE',
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
                alert('Error: ' + (data.message || 'No se pudo eliminar el plan'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el plan');
        });
    }
}
</script>
@endpush
@endsection
