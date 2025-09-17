@extends('layouts.app')

@section('title', 'Gestión de Roles - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-user-tag text-primary me-2"></i>
                        Gestión de Roles
                    </h1>
                    <p class="text-muted mb-0">Administra los roles del sistema</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('superadmin.roles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nuevo Rol
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Roles -->
    <div class="row">
        @forelse($roles as $role)
            <div class="col-xl-4 col-lg-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-{{ $role->name === 'superadmin' ? 'crown' : ($role->name === 'admin' ? 'user-shield' : 'user') }} me-2"></i>
                            {{ $role->display_name }}
                        </h6>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('superadmin.roles.show', $role) }}">
                                        <i class="fas fa-eye me-2"></i>Ver Detalles
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('superadmin.roles.edit', $role) }}">
                                        <i class="fas fa-edit me-2"></i>Editar
                                    </a>
                                </li>
                                @if($role->name !== 'superadmin')
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button class="dropdown-item text-danger" 
                                                onclick="deleteRole({{ $role->id }})">
                                            <i class="fas fa-trash me-2"></i>Eliminar
                                        </button>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-3">{{ $role->description }}</p>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Nivel</small>
                                <div class="fw-bold">{{ $role->level }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Estado</small>
                                <div>
                                    @if($role->is_active)
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
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Usuarios Asignados</small>
                            <div class="fw-bold text-primary">{{ $role->users_count ?? 0 }} usuarios</div>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted">Permisos</small>
                            <div class="fw-bold text-info">{{ count($role->permissions ?? []) }} permisos</div>
                        </div>

                        @if($role->permissions && count($role->permissions) > 0)
                            <div class="mb-3">
                                <small class="text-muted">Categorías de Permisos</small>
                                <div class="mt-1">
                                    @php
                                        $categories = collect($role->permissions)->groupBy('category');
                                    @endphp
                                    @foreach($categories->take(3) as $category => $perms)
                                        <span class="badge bg-light text-dark me-1 mb-1">
                                            {{ ucfirst($category) }} ({{ count($perms) }})
                                        </span>
                                    @endforeach
                                    @if($categories->count() > 3)
                                        <span class="badge bg-secondary">
                                            +{{ $categories->count() - 3 }} más
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Creado: {{ $role->created_at->format('d/m/Y') }}
                            </small>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('superadmin.roles.show', $role) }}" 
                                   class="btn btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('superadmin.roles.edit', $role) }}" 
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
                        <i class="fas fa-user-tag fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No hay roles registrados</h5>
                        <p class="text-muted">Crea el primer rol para comenzar a organizar los permisos del sistema.</p>
                        <a href="{{ route('superadmin.roles.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Crear Primer Rol
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function deleteRole(roleId) {
    if (confirm('¿Estás seguro de que quieres eliminar este rol? Esta acción no se puede deshacer.')) {
        fetch(`/superadmin/roles/${roleId}`, {
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
                alert('Error: ' + (data.message || 'No se pudo eliminar el rol'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al eliminar el rol');
        });
    }
}
</script>
@endpush
@endsection
