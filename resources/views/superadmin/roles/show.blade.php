@extends('layouts.app')

@section('title', 'Ver Rol - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-user-tag text-primary me-2"></i>
                        {{ $role->display_name }}
                    </h1>
                    <p class="text-muted mb-0">{{ $role->description }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('superadmin.roles.edit', $role) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Editar
                    </a>
                    <a href="{{ route('superadmin.roles') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información del Rol -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i>Información del Rol
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Nombre del Rol:</strong>
                            <p class="text-muted">{{ $role->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Estado:</strong>
                            <p>
                                @if($role->is_active)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Activo
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle me-1"></i>Inactivo
                                    </span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <strong>Descripción:</strong>
                            <p class="text-muted">{{ $role->description ?: 'Sin descripción' }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Creado:</strong>
                            <p class="text-muted">{{ $role->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Última actualización:</strong>
                            <p class="text-muted">{{ $role->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permisos -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-key me-2"></i>Permisos Asignados ({{ $role->rolePermissions->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    @if($role->rolePermissions->count() > 0)
                        <div class="row">
                            @foreach($role->rolePermissions as $permission)
                                <div class="col-md-6 col-lg-4 mb-2">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>{{ $permission->display_name ?? $permission->name }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-key fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Este rol no tiene permisos asignados</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Usuarios con este Rol -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users me-2"></i>Usuarios con este Rol ({{ $role->users->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    @if($role->users->count() > 0)
                        @foreach($role->users as $user)
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" 
                                     style="width: 40px; height: 40px;">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold">{{ $user->name }}</div>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay usuarios con este rol</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt me-2"></i>Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('superadmin.roles.edit', $role) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>Editar Rol
                        </a>
                        
                        @if($role->users->count() == 0)
                            <form method="POST" action="{{ route('superadmin.roles.destroy', $role) }}" 
                                  class="d-inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este rol?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-trash me-1"></i>Eliminar Rol
                                </button>
                            </form>
                        @else
                            <button class="btn btn-outline-secondary" disabled title="No se puede eliminar porque tiene usuarios asignados">
                                <i class="fas fa-trash me-1"></i>Eliminar Rol
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
