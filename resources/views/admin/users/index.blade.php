@extends('layouts.app')

@section('title', 'Gestión de Usuarios - Admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">
            <i class="fas fa-users me-2"></i>Gestión de Usuarios
        </h1>
        <div>
            <a href="{{ route('admin.deactivated-users.index') }}" class="btn btn-warning me-2">
                <i class="fas fa-user-clock me-2"></i>Usuarios Desactivados
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Usuario</th>
                            <th>Email</th>
                            <th>Estado</th>
                            <th>Reservas</th>
                            <th>Reseñas</th>
                            <th>Último Login</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($user->profile_picture)
                                            <img src="{{ $user->profile_picture }}" 
                                                 alt="{{ $user->name }}"
                                                 class="rounded-circle me-3"
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                                 style="width: 40px; height: 40px;">
                                                <span class="text-white fw-bold">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $user->name }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                Registrado: {{ $user->created_at->format('d/m/Y') }}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $user->email }}
                                    @if($user->email_verified_at)
                                        <br>
                                        <span class="badge bg-success">Verificado</span>
                                    @else
                                        <br>
                                        <span class="badge bg-warning">No Verificado</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->isActive())
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-danger">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $user->reservations->count() }} reservas
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $user->propertyReviews->count() }} reseñas
                                    </span>
                                </td>
                                <td>
                                    @if($user->last_login_at)
                                        <small>{{ $user->last_login_at->diffForHumans() }}</small>
                                    @else
                                        <small class="text-muted">Nunca</small>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.users.history', $user->email) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="Ver Historial">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-warning" 
                                                title="Suspender"
                                                onclick="confirmSuspend({{ $user->id }})">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <p>No hay usuarios registrados</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmación de suspensión -->
<div class="modal fade" id="suspendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Suspensión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres suspender a este usuario?</p>
                <p class="text-warning"><small>El usuario no podrá acceder al sistema hasta que sea reactivado.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="suspendForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-warning">Suspender</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmSuspend(userId) {
    const modal = new bootstrap.Modal(document.getElementById('suspendModal'));
    const form = document.getElementById('suspendForm');
    form.action = `/admin/users/${userId}/suspend`;
    modal.show();
}
</script>
@endpush
