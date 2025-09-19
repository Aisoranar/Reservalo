@extends('layouts.app')

@section('title', 'Detalles del Permiso - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-key text-primary me-2"></i>
                        Detalles del Permiso
                    </h1>
                    <p class="text-muted mb-0">Información completa del permiso</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('superadmin.permissions') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                    <a href="{{ route('superadmin.permissions.edit', $permission) }}" class="btn btn-warning">
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
                                <strong>Nombre Técnico:</strong>
                                <p class="text-muted">
                                    <code>{{ $permission->name }}</code>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Nombre para Mostrar:</strong>
                                <p class="text-muted">{{ $permission->display_name }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Categoría:</strong>
                                <p class="text-muted">
                                    <span class="badge bg-primary">{{ ucfirst($permission->category) }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Estado:</strong>
                                <p class="text-muted">
                                    @if($permission->is_active)
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
                    
                    @if($permission->description)
                    <div class="mb-3">
                        <strong>Descripción:</strong>
                        <p class="text-muted">{{ $permission->description }}</p>
                    </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Creado:</strong>
                            <p class="text-muted">{{ $permission->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Última actualización:</strong>
                            <p class="text-muted">{{ $permission->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Roles que usan este permiso -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-tag me-2"></i>Roles que Usan este Permiso
                    </h6>
                </div>
                <div class="card-body">
                    @if($permission->roles->count() > 0)
                        <div class="row">
                            @foreach($permission->roles as $role)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="card border-left-primary h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="card-title mb-1">{{ $role->display_name }}</h6>
                                                    <p class="card-text text-muted small mb-2">
                                                        <code>{{ $role->name }}</code>
                                                    </p>
                                                    <span class="badge bg-{{ $role->is_active ? 'success' : 'danger' }}">
                                                        {{ $role->is_active ? 'Activo' : 'Inactivo' }}
                                                    </span>
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                            type="button" 
                                                            data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('superadmin.roles.show', $role) }}">
                                                                <i class="fas fa-eye me-2"></i>Ver Rol
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('superadmin.roles.edit', $role) }}">
                                                                <i class="fas fa-edit me-2"></i>Editar Rol
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-tag fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No hay roles asignados</h5>
                            <p class="text-muted">Este permiso no está siendo usado por ningún rol actualmente.</p>
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
                        <a href="{{ route('superadmin.permissions.edit', $permission) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Editar Permiso
                        </a>
                        
                        <button type="button" 
                                class="btn btn-{{ $permission->is_active ? 'warning' : 'success' }}" 
                                onclick="togglePermissionStatus({{ $permission->id }}, '{{ $permission->is_active ? 'false' : 'true' }}')">
                            <i class="fas fa-{{ $permission->is_active ? 'ban' : 'check' }} me-2"></i>
                            {{ $permission->is_active ? 'Desactivar' : 'Activar' }}
                        </button>
                        
                        <button type="button" 
                                class="btn btn-outline-danger" 
                                onclick="deletePermission({{ $permission->id }})">
                            <i class="fas fa-trash me-2"></i>Eliminar Permiso
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
                                <h4 class="text-primary">{{ $permission->roles->count() }}</h4>
                                <small class="text-muted">Roles Asignados</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <h4 class="text-{{ $permission->is_active ? 'success' : 'danger' }}">
                                {{ $permission->is_active ? 'Sí' : 'No' }}
                            </h4>
                            <small class="text-muted">Activo</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function togglePermissionStatus(permissionId, newStatus) {
    const action = newStatus === 'true' ? 'activar' : 'desactivar';
    
    if (confirm(`¿Estás seguro de que quieres ${action} este permiso?`)) {
        fetch(`/superadmin/permissions/${permissionId}/toggle-status`, {
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
                alert('Error: ' + (data.message || 'No se pudo cambiar el estado del permiso'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cambiar el estado del permiso');
        });
    }
}

function deletePermission(permissionId) {
    if (confirm('¿Estás seguro de que quieres eliminar este permiso? Esta acción no se puede deshacer.')) {
        fetch(`/superadmin/permissions/${permissionId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("superadmin.permissions") }}';
            } else {
                alert('Error: ' + (data.message || 'No se pudo eliminar el permiso'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el permiso');
        });
    }
}
</script>
@endpush
@endsection
