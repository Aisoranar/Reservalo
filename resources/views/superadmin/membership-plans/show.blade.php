@extends('layouts.app')

@section('title', 'Detalles del Plan de Membresía - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-gem text-primary me-2"></i>
                        Detalles del Plan de Membresía
                    </h1>
                    <p class="text-muted mb-0">Información completa del plan</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('superadmin.membership-plans') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                    <a href="{{ route('superadmin.membership-plans.edit', $plan) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Editar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Información General
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Nombre del Plan:</strong>
                                <p class="text-muted">{{ $plan->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Precio:</strong>
                                <p class="text-muted">
                                    <span class="h4 text-primary">${{ number_format($plan->price, 0, ',', '.') }}</span>
                                    <small class="text-muted">COP</small>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Duración:</strong>
                                <p class="text-muted">{{ $plan->duration_days }} días</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Estado:</strong>
                                <p class="text-muted">
                                    @if($plan->is_active)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Activo
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times me-1"></i>Inactivo
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Máximo de Propiedades:</strong>
                                <p class="text-muted">
                                    @if($plan->max_properties)
                                        {{ $plan->max_properties }}
                                    @else
                                        <span class="text-muted">Sin límite</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Máximo de Reservas:</strong>
                                <p class="text-muted">
                                    @if($plan->max_reservations)
                                        {{ $plan->max_reservations }}
                                    @else
                                        <span class="text-muted">Sin límite</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    @if($plan->description)
                    <div class="mb-3">
                        <strong>Descripción:</strong>
                        <p class="text-muted">{{ $plan->description }}</p>
                    </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Creado:</strong>
                            <p class="text-muted">{{ $plan->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Última actualización:</strong>
                            <p class="text-muted">{{ $plan->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Características -->
            @if($plan->features && count($plan->features) > 0)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-star me-2"></i>Características del Plan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($plan->features as $feature)
                            <div class="col-md-6 col-lg-4 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span>{{ $feature }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Membresías Activas -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users me-2"></i>Membresías Activas ({{ $plan->memberships_count }})
                    </h6>
                </div>
                <div class="card-body">
                    @if($plan->memberships_count > 0)
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Este plan tiene {{ $plan->memberships_count }} membresía(s) activa(s). 
                            <a href="{{ route('superadmin.memberships', ['plan' => $plan->id]) }}" class="alert-link">
                                Ver detalles de membresías
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay membresías activas</h5>
                            <p class="text-muted">Este plan no tiene usuarios suscritos actualmente.</p>
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
                        <a href="{{ route('superadmin.membership-plans.edit', $plan) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Editar Plan
                        </a>
                        
                        <button type="button" 
                                class="btn btn-{{ $plan->is_active ? 'warning' : 'success' }}" 
                                onclick="togglePlanStatus({{ $plan->id }}, '{{ $plan->is_active ? 'false' : 'true' }}')">
                            <i class="fas fa-{{ $plan->is_active ? 'ban' : 'check' }} me-2"></i>
                            {{ $plan->is_active ? 'Desactivar' : 'Activar' }}
                        </button>
                        
                        @if(!$plan->is_default)
                        <button type="button" 
                                class="btn btn-outline-primary" 
                                onclick="setAsDefault({{ $plan->id }})">
                            <i class="fas fa-star me-2"></i>Establecer como Predeterminado
                        </button>
                        @endif
                        
                        <button type="button" 
                                class="btn btn-outline-danger" 
                                onclick="deletePlan({{ $plan->id }})">
                            <i class="fas fa-trash me-2"></i>Eliminar Plan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i>Estadísticas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <h4 class="text-primary">{{ $plan->memberships_count }}</h4>
                                <small class="text-muted">Membresías</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-{{ $plan->is_active ? 'success' : 'danger' }}">
                                {{ $plan->is_active ? 'Sí' : 'No' }}
                            </h4>
                            <small class="text-muted">Activo</small>
                        </div>
                    </div>
                    
                    @if($plan->is_default)
                    <div class="mt-3 text-center">
                        <span class="badge bg-warning text-dark">
                            <i class="fas fa-star me-1"></i>Plan Predeterminado
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
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

function setAsDefault(planId) {
    if (confirm('¿Estás seguro de que quieres establecer este plan como predeterminado?')) {
        fetch(`/superadmin/membership-plans/${planId}/set-default`, {
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
                alert('Error: ' + (data.message || 'No se pudo establecer como predeterminado'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al establecer como predeterminado');
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
                window.location.href = '{{ route("superadmin.membership-plans") }}';
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
