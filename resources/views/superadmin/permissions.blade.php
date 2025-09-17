@extends('layouts.app')

@section('title', 'Gestión de Permisos - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-key text-primary me-2"></i>
                        Gestión de Permisos
                    </h1>
                    <p class="text-muted mb-0">Administra los permisos del sistema</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('superadmin.permissions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nuevo Permiso
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
                        <div class="col-md-4">
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" name="search" 
                                   value="{{ request('search') }}" placeholder="Nombre o descripción...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Categoría</label>
                            <select class="form-select" name="category">
                                <option value="">Todas</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                        {{ ucfirst($category) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Estado</label>
                            <select class="form-select" name="status">
                                <option value="">Todos</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Activo</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                            <a href="{{ route('superadmin.permissions') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Permisos por Categoría -->
    @foreach($permissionsByCategory as $category => $permissions)
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-{{ $category === 'system' ? 'cogs' : ($category === 'users' ? 'users' : ($category === 'properties' ? 'home' : ($category === 'reservations' ? 'calendar' : ($category === 'memberships' ? 'gem' : ($category === 'reports' ? 'chart-bar' : 'key')))) }} me-2"></i>
                            {{ ucfirst($category) }} ({{ count($permissions) }} permisos)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($permissions as $permission)
                                <div class="col-xl-4 col-lg-6 mb-3">
                                    <div class="card border-left-{{ $permission->is_active ? 'success' : 'danger' }} h-100">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="card-title mb-0">
                                                    <i class="fas fa-{{ $permission->is_active ? 'check-circle text-success' : 'times-circle text-danger' }} me-1"></i>
                                                    {{ $permission->display_name }}
                                                </h6>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('superadmin.permissions.show', $permission) }}">
                                                                <i class="fas fa-eye me-2"></i>Ver Detalles
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('superadmin.permissions.edit', $permission) }}">
                                                                <i class="fas fa-edit me-2"></i>Editar
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li>
                                                            <button class="dropdown-item text-{{ $permission->is_active ? 'warning' : 'success' }}" 
                                                                    onclick="togglePermissionStatus({{ $permission->id }}, {{ $permission->is_active ? 'false' : 'true' }})">
                                                                <i class="fas fa-{{ $permission->is_active ? 'ban' : 'check' }} me-2"></i>
                                                                {{ $permission->is_active ? 'Desactivar' : 'Activar' }}
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <button class="dropdown-item text-danger" 
                                                                    onclick="deletePermission({{ $permission->id }})">
                                                                <i class="fas fa-trash me-2"></i>Eliminar
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            
                                            <p class="card-text text-muted small mb-2">
                                                {{ $permission->description }}
                                            </p>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted">
                                                    <code>{{ $permission->name }}</code>
                                                </small>
                                                <span class="badge bg-{{ $permission->is_active ? 'success' : 'danger' }}">
                                                    {{ $permission->is_active ? 'Activo' : 'Inactivo' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    @if(empty($permissionsByCategory))
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-key fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay permisos registrados</h5>
                        <p class="text-muted">Crea el primer permiso para comenzar a organizar el sistema.</p>
                        <a href="{{ route('superadmin.permissions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Crear Primer Permiso
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }
    .border-left-danger {
        border-left: 0.25rem solid #e74a3b !important;
    }
</style>
@endpush

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
                location.reload();
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
