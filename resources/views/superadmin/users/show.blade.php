@extends('layouts.app')

@section('title', 'Detalles del Usuario - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-user text-primary me-2"></i>
                        Detalles del Usuario
                    </h1>
                    <p class="text-muted mb-0">Información completa del usuario {{ $user->name }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('superadmin.users.edit', $user) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i>Editar
                    </a>
                    <a href="{{ route('superadmin.users') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información Principal -->
        <div class="col-lg-8">
            <!-- Información Personal -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-circle me-2"></i>Información Personal
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Nombre Completo</label>
                                <p class="form-control-plaintext">{{ $user->name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Email</label>
                                <p class="form-control-plaintext">{{ $user->email }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Teléfono</label>
                                <p class="form-control-plaintext">{{ $user->phone ?? 'No especificado' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">WhatsApp</label>
                                <p class="form-control-plaintext">{{ $user->whatsapp ?? 'No especificado' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tipo de Cuenta</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-{{ $user->account_type === 'business' ? 'info' : 'success' }}">
                                        {{ $user->account_type === 'business' ? 'Empresarial' : 'Individual' }}
                                    </span>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Estado</label>
                                <p class="form-control-plaintext">
                                    <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                        {{ $user->is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Fecha de Registro</label>
                                <p class="form-control-plaintext">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Último Acceso</label>
                                <p class="form-control-plaintext">
                                    {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de Ubicación -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-map-marker-alt me-2"></i>Información de Ubicación
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Dirección</label>
                                <p class="form-control-plaintext">{{ $user->address ?? 'No especificada' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Ciudad</label>
                                <p class="form-control-plaintext">{{ $user->city ?? 'No especificada' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Estado/Departamento</label>
                                <p class="form-control-plaintext">{{ $user->state ?? 'No especificado' }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">País</label>
                                <p class="form-control-plaintext">{{ $user->country ?? 'No especificado' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Propiedades del Usuario -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-home me-2"></i>Propiedades ({{ $user->ownedProperties->count() }})
                    </h6>
                </div>
                <div class="card-body">
                    @if($user->ownedProperties->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Precio</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->ownedProperties as $property)
                                        <tr>
                                            <td>{{ $property->name }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst($property->type) }}</span>
                                            </td>
                                            <td>${{ number_format($property->price, 0, ',', '.') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $property->is_active ? 'success' : 'danger' }}">
                                                    {{ $property->is_active ? 'Activa' : 'Inactiva' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('properties.show', $property) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-home fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Este usuario no tiene propiedades registradas</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Roles del Usuario -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-tag me-2"></i>Roles y Permisos
                    </h6>
                </div>
                <div class="card-body">
                    @if($user->roles->count() > 0)
                        @foreach($user->roles as $role)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary">{{ $role->name }}</span>
                                <small class="text-muted">
                                    @if($role->pivot->assigned_at)
                                        {{ \Carbon\Carbon::parse($role->pivot->assigned_at)->format('d/m/Y') }}
                                    @else
                                        Sin fecha
                                    @endif
                                </small>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">Sin roles asignados</p>
                    @endif
                </div>
            </div>

            <!-- Membresías -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-gem me-2"></i>Membresías
                    </h6>
                </div>
                <div class="card-body">
                    @if($user->memberships->count() > 0)
                        @foreach($user->memberships as $membership)
                            <div class="mb-3 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>{{ $membership->plan->name ?? 'Sin plan' }}</strong>
                                    <span class="badge bg-{{ $membership->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($membership->status) }}
                                    </span>
                                </div>
                                @if($membership->expires_at)
                                    <small class="text-muted">
                                        Expira: {{ \Carbon\Carbon::parse($membership->expires_at)->format('d/m/Y') }}
                                    </small>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">Sin membresías activas</p>
                    @endif
                </div>
            </div>

            <!-- Actividad Reciente -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Actividad Reciente
                    </h6>
                </div>
                <div class="card-body">
                    @if($user->auditLogs->count() > 0)
                        @foreach($user->auditLogs as $log)
                            <div class="mb-2 p-2 border-start border-primary border-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $log->action }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $log->description }}</small>
                                    </div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m H:i') }}</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">Sin actividad registrada</p>
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
                        <form method="POST" action="{{ route('superadmin.users.toggle-status', $user) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-{{ $user->is_active ? 'warning' : 'success' }} w-100">
                                <i class="fas fa-power-off me-1"></i>
                                {{ $user->is_active ? 'Desactivar' : 'Activar' }} Usuario
                            </button>
                        </form>
                        
                        <a href="mailto:{{ $user->email }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-envelope me-1"></i>Enviar Email
                        </a>
                        
                        @if($user->whatsapp)
                            <a href="https://wa.me/{{ $user->whatsapp }}" target="_blank" class="btn btn-outline-success w-100">
                                <i class="fab fa-whatsapp me-1"></i>WhatsApp
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
