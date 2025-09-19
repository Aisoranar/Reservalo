@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-users text-primary me-2"></i>
                        Gestión de Usuarios
                    </h1>
                    <p class="text-muted mb-0">Administra todos los usuarios del sistema</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('superadmin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Nuevo Usuario
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
                            <label class="form-label">Buscar</label>
                            <input type="text" class="form-control" name="search" 
                                   value="{{ request('search') }}" placeholder="Nombre o email...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Rol</label>
                            <select class="form-select" name="role">
                                <option value="">Todos</option>
                                <option value="superadmin" {{ request('role') === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Usuario</option>
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
                        <div class="col-md-2">
                            <label class="form-label">Tipo de Cuenta</label>
                            <select class="form-select" name="account_type">
                                <option value="">Todos</option>
                                <option value="regular" {{ request('account_type') === 'regular' ? 'selected' : '' }}>Regular</option>
                                <option value="premium" {{ request('account_type') === 'premium' ? 'selected' : '' }}>Premium</option>
                                <option value="business" {{ request('account_type') === 'business' ? 'selected' : '' }}>Business</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Por página</label>
                            <div class="position-relative">
                                <select class="form-select" name="per_page" id="perPageSelect" onchange="changePerPage()">
                                    <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                                <div id="perPageLoader" class="position-absolute top-50 end-0 translate-middle-y me-2" style="display: none;">
                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-1"></i>Filtrar
                            </button>
                            <a href="{{ route('superadmin.users') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Lista de Usuarios ({{ $users->total() }} total)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>Usuario</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Membresía</th>
                                    <th>Último Acceso</th>
                                    <th>Registro</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-{{ $user->role === 'superadmin' ? 'warning' : ($user->role === 'admin' ? 'info' : 'primary') }} text-white d-flex align-items-center justify-content-center me-3" 
                                                     style="width: 40px; height: 40px;">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $user->name }}</div>
                                                    <small class="text-muted">{{ $user->email }}</small>
                                                    @if($user->phone)
                                                        <br><small class="text-muted">{{ $user->phone }}</small>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $user->role === 'superadmin' ? 'warning' : ($user->role === 'admin' ? 'info' : 'primary') }}">
                                                @if($user->role === 'superadmin')
                                                    <i class="fas fa-crown me-1"></i>Super Admin
                                                @elseif($user->role === 'admin')
                                                    <i class="fas fa-user-shield me-1"></i>Admin
                                                @else
                                                    <i class="fas fa-user me-1"></i>Usuario
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Activo
                                                </span>
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->currentMembership)
                                                <div>
                                                    <span class="badge bg-info">{{ $user->currentMembership->plan->name }}</span>
                                                    <br><small class="text-muted">
                                                        Expira: {{ $user->currentMembership->expires_at->format('d/m/Y') }}
                                                    </small>
                                                </div>
                                            @else
                                                <span class="badge bg-secondary">Sin membresía</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->last_login_at)
                                                <span class="text-muted">{{ $user->last_login_at->format('d/m/Y H:i') }}</span>
                                            @else
                                                <span class="text-muted">Nunca</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $user->created_at->format('d/m/Y') }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('superadmin.users.show', $user) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Ver">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('superadmin.users.edit', $user) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($user->id !== auth()->id())
                                                    <button type="button" class="btn btn-sm btn-outline-{{ $user->is_active ? 'danger' : 'success' }}" 
                                                            onclick="toggleUserStatus({{ $user->id }}, '{{ $user->is_active ? 'false' : 'true' }}')" 
                                                            title="{{ $user->is_active ? 'Desactivar' : 'Activar' }}">
                                                        <i class="fas fa-{{ $user->is_active ? 'ban' : 'check' }}"></i>
                                                    </button>
                                                    @php
                                                        $canDelete = true;
                                                        $deleteReason = '';
                                                        
                                                        // Verificar si tiene reservas activas
                                                        $activeReservations = $user->reservations()
                                                            ->whereIn('status', ['pending', 'approved', 'confirmed'])
                                                            ->count();
                                                        
                                                        if ($activeReservations > 0) {
                                                            $canDelete = false;
                                                            $deleteReason = "Tiene {$activeReservations} reserva(s) activa(s)";
                                                        }
                                                        
                                                        // Verificar si es el último superadmin
                                                        if ($user->role === 'superadmin') {
                                                            $superAdminCount = \App\Models\User::where('role', 'superadmin')->count();
                                                            if ($superAdminCount <= 1) {
                                                                $canDelete = false;
                                                                $deleteReason = 'Es el último superadministrador';
                                                            }
                                                        }
                                                    @endphp
                                                    
                                                    @if($canDelete)
                                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                onclick="deleteUser({{ $user->id }}, '{{ $user->name }}')" 
                                                                title="Eliminar usuario">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @else
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                                disabled title="No se puede eliminar: {{ $deleteReason }}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">No se encontraron usuarios</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($users->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $users->appends(request()->query())->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteUserModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Eliminación de Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>¡Atención!</strong> Esta acción no se puede deshacer.
                </div>
                <p>¿Estás seguro de que quieres eliminar al usuario <strong id="userNameToDelete"></strong>?</p>
                <p>Esta acción eliminará permanentemente:</p>
                <ul class="list-unstyled ms-3">
                    <li><i class="fas fa-user text-danger me-2"></i>El usuario y todos sus datos personales</li>
                    <li><i class="fas fa-calendar text-danger me-2"></i>Sus reservas (se marcarán como canceladas)</li>
                    <li><i class="fas fa-star text-danger me-2"></i>Sus reseñas y comentarios</li>
                    <li><i class="fas fa-history text-danger me-2"></i>Su historial de actividad</li>
                </ul>
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" id="confirmDeleteCheckbox">
                    <label class="form-check-label" for="confirmDeleteCheckbox">
                        Entiendo las consecuencias y quiero proceder con la eliminación
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                    <i class="fas fa-trash me-1"></i>Eliminar Usuario
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleUserStatus(userId, newStatus) {
    const action = newStatus === 'true' ? 'activar' : 'desactivar';
    const isActive = newStatus === 'true';
    
    if (confirm(`¿Estás seguro de que quieres ${action} este usuario?`)) {
        fetch(`{{ url('/superadmin/users') }}/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin',
            body: JSON.stringify({ is_active: isActive })
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return response.text().then(text => {
                console.log('Response text:', text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.error('Response was not JSON:', text);
                    throw new Error('La respuesta del servidor no es JSON válido');
                }
            });
        })
        .then(data => {
            console.log('Parsed data:', data);
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'No se pudo cambiar el estado del usuario'));
            }
        })
        .catch(error => {
            console.error('Error completo:', error);
            alert('Error al cambiar el estado del usuario: ' + error.message);
        });
    }
}

let userIdToDelete = null;

function deleteUser(userId, userName) {
    userIdToDelete = userId;
    document.getElementById('userNameToDelete').textContent = userName;
    
    // Resetear el checkbox y botón
    document.getElementById('confirmDeleteCheckbox').checked = false;
    document.getElementById('confirmDeleteBtn').disabled = true;
    
    // Mostrar el modal
    const modal = new bootstrap.Modal(document.getElementById('deleteUserModal'));
    modal.show();
}

// Event listener para el checkbox de confirmación
document.getElementById('confirmDeleteCheckbox').addEventListener('change', function() {
    document.getElementById('confirmDeleteBtn').disabled = !this.checked;
});

// Event listener para el botón de confirmación
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (userIdToDelete && document.getElementById('confirmDeleteCheckbox').checked) {
        // Crear formulario para enviar DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `{{ url('/superadmin/users') }}/${userIdToDelete}`;
        
        // Agregar token CSRF
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);
        
        // Agregar método DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        // Agregar al DOM y enviar
        document.body.appendChild(form);
        form.submit();
    }
});

// Función para cambiar elementos por página
function changePerPage() {
    const loader = document.getElementById('perPageLoader');
    const select = document.getElementById('perPageSelect');
    
    // Mostrar loader
    loader.style.display = 'block';
    select.disabled = true;
    
    // Enviar formulario
    document.querySelector('form').submit();
}
</script>
@endpush
@endsection
