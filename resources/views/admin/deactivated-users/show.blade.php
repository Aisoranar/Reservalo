@extends('layouts.app')

@section('title', 'Detalles de Usuario Desactivado - Panel de Administración')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="admin-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="header-icon me-3">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div>
                        <h1 class="h2 mb-1">Detalles del Usuario Desactivado</h1>
                        <p class="text-muted mb-0">{{ $deactivatedUser->name }} ({{ $deactivatedUser->email }})</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="header-actions">
                    <a href="{{ route('admin.deactivated-users.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Volver
                    </a>
                    @if($deactivatedUser->canBeReactivated())
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#reactivationModal">
                            <i class="fas fa-undo me-2"></i>Reactivar
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Información Principal del Usuario -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Información del Usuario
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center mb-3">
                            @if($deactivatedUser->profile_picture)
                                <img src="{{ $deactivatedUser->profile_picture }}" alt="{{ $deactivatedUser->name }}" 
                                     class="profile-picture-large">
                            @else
                                <div class="profile-picture-placeholder-large">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <div class="user-info-grid">
                                <div class="info-item">
                                    <label>Nombre:</label>
                                    <span>{{ $deactivatedUser->name }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Email:</label>
                                    <span>{{ $deactivatedUser->email }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Teléfono:</label>
                                    <span>{{ $deactivatedUser->phone ?? 'No especificado' }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Dirección:</label>
                                    <span>{{ $deactivatedUser->address ?? 'No especificada' }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Ciudad:</label>
                                    <span>{{ $deactivatedUser->city ?? 'No especificada' }}</span>
                                </div>
                                <div class="info-item">
                                    <label>País:</label>
                                    <span>{{ $deactivatedUser->country ?? 'No especificado' }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Fecha de Nacimiento:</label>
                                    <span>{{ $deactivatedUser->birth_date ? $deactivatedUser->birth_date->format('d/m/Y') : 'No especificada' }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Género:</label>
                                    <span>{{ $deactivatedUser->gender ? ucfirst($deactivatedUser->gender) : 'No especificado' }}</span>
                                </div>
                                <div class="info-item">
                                    <label>Tipo de Cuenta:</label>
                                    <span class="badge badge-{{ $deactivatedUser->account_type }}">
                                        {{ $deactivatedUser->account_type_text }}
                                    </span>
                                </div>
                                <div class="info-item">
                                    <label>Email Verificado:</label>
                                    <span class="badge badge-{{ $deactivatedUser->email_verified_at ? 'success' : 'warning' }}">
                                        {{ $deactivatedUser->email_verified_at ? 'Sí' : 'No' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de Desactivación -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-ban me-2"></i>Información de Desactivación
                    </h5>
                </div>
                <div class="card-body">
                    <div class="deactivation-info-grid">
                        <div class="info-item">
                            <label>Motivo:</label>
                            <span class="badge badge-{{ $deactivatedUser->deactivation_reason }}">
                                {{ $deactivatedUser->deactivation_reason_text }}
                            </span>
                        </div>
                        <div class="info-item">
                            <label>Fecha de Desactivación:</label>
                            <span>{{ $deactivatedUser->deactivated_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <div class="info-item">
                            <label>Tiempo Transcurrido:</label>
                            <span>{{ $deactivatedUser->time_since_deactivation }}</span>
                        </div>
                        <div class="info-item">
                            <label>Desactivado por:</label>
                            <span>{{ $deactivatedUser->deactivated_by === 'self' ? 'El usuario mismo' : 'Administrador' }}</span>
                        </div>
                        <div class="info-item">
                            <label>IP de Desactivación:</label>
                            <span>{{ $deactivatedUser->deactivation_ip ?? 'No registrada' }}</span>
                        </div>
                        <div class="info-item">
                            <label>User Agent:</label>
                            <span class="text-muted">{{ Str::limit($deactivatedUser->deactivation_user_agent ?? 'No registrado', 100) }}</span>
                        </div>
                    </div>

                    @if($deactivatedUser->deactivation_notes)
                        <div class="deactivation-notes mt-3">
                            <label class="form-label">Notas de Desactivación:</label>
                            <div class="notes-content">
                                {{ $deactivatedUser->deactivation_notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Datos Preservados -->
            @if($deactivatedUser->deactivation_data)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-database me-2"></i>Datos Preservados
                    </h5>
                </div>
                <div class="card-body">
                    <div class="data-stats-grid">
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $deactivatedUser->deactivation_data['reservations_count'] ?? 0 }}</div>
                                <div class="stat-label">Reservas</div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $deactivatedUser->deactivation_data['reviews_count'] ?? 0 }}</div>
                                <div class="stat-label">Reseñas</div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-heart"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $deactivatedUser->deactivation_data['favorites_count'] ?? 0 }}</div>
                                <div class="stat-label">Favoritos</div>
                            </div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ $deactivatedUser->deactivation_data['account_age_days'] ?? 0 }}</div>
                                <div class="stat-label">Días como Miembro</div>
                            </div>
                        </div>
                    </div>

                    @if(isset($deactivatedUser->deactivation_data['recent_reservations']) && count($deactivatedUser->deactivation_data['recent_reservations']) > 0)
                    <div class="recent-reservations mt-4">
                        <h6>Reservas Recientes:</h6>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Propiedad</th>
                                        <th>Fechas</th>
                                        <th>Estado</th>
                                        <th>Precio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($deactivatedUser->deactivation_data['recent_reservations'] as $reservation)
                                    <tr>
                                        <td>{{ $reservation['property_name'] }}</td>
                                        <td>{{ $reservation['start_date'] }} - {{ $reservation['end_date'] }}</td>
                                        <td>
                                            <span class="badge badge-{{ $reservation['status'] }}">
                                                {{ ucfirst($reservation['status']) }}
                                            </span>
                                        </td>
                                        <td>${{ number_format($reservation['total_price'], 0) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    @if(isset($deactivatedUser->deactivation_data['recent_reviews']) && count($deactivatedUser->deactivation_data['recent_reviews']) > 0)
                    <div class="recent-reviews mt-4">
                        <h6>Reseñas Recientes:</h6>
                        <div class="reviews-list">
                            @foreach($deactivatedUser->deactivation_data['recent_reviews'] as $review)
                            <div class="review-item">
                                <div class="review-header">
                                    <span class="property-name">{{ $review['property_name'] }}</span>
                                    <span class="review-rating">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review['rating'])
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-muted"></i>
                                            @endif
                                        @endfor
                                        <span class="rating-number">({{ $review['rating'] }}/5)</span>
                                    </span>
                                </div>
                                <div class="review-comment">{{ $review['comment'] }}</div>
                                <div class="review-date">{{ $review['created_at'] }}</div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Estado de la Cuenta -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Estado de la Cuenta
                    </h5>
                </div>
                <div class="card-body">
                    <div class="account-status">
                        @if($deactivatedUser->canBeReactivated())
                            <div class="status-item status-success">
                                <i class="fas fa-check-circle"></i>
                                <span>Puede ser reactivada</span>
                            </div>
                        @else
                            <div class="status-item status-danger">
                                <i class="fas fa-ban"></i>
                                <span>No puede ser reactivada</span>
                            </div>
                        @endif

                        @if($deactivatedUser->hasRequestedReactivation())
                            <div class="status-item status-info">
                                <i class="fas fa-clock"></i>
                                <span>Solicitud de reactivación pendiente</span>
                                <small>{{ $deactivatedUser->time_since_reactivation_request }}</small>
                            </div>
                        @endif

                        <div class="status-item">
                            <i class="fas fa-calendar"></i>
                            <span>Último login: {{ $deactivatedUser->last_login_at ? $deactivatedUser->last_login_at->format('d/m/Y') : 'Nunca' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial de Cuentas Múltiples -->
            @if($multipleAccounts->count() > 1)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-copy me-2"></i>Cuentas Múltiples ({{ $multipleAccounts->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="multiple-accounts">
                        <p class="text-muted small mb-3">
                            Este usuario ha tenido múltiples cuentas. Selecciona cuál quieres reactivar:
                        </p>
                        
                        @foreach($multipleAccounts as $index => $account)
                        <div class="account-item {{ $account->id === $deactivatedUser->id ? 'active' : '' }}">
                            <div class="account-header">
                                <span class="account-number">#{{ $index + 1 }}</span>
                                <span class="account-date">{{ $account->deactivated_at->format('d/m/Y') }}</span>
                            </div>
                            <div class="account-details">
                                <div class="account-reason">
                                    <strong>{{ $account->deactivation_reason_text }}</strong>
                                </div>
                                @if($account->deactivation_notes)
                                    <div class="account-notes">
                                        {{ Str::limit($account->deactivation_notes, 50) }}
                                    </div>
                                @endif
                            </div>
                            @if($account->canBeReactivated())
                                <button type="button" class="btn btn-sm btn-success" 
                                        onclick="selectAccountForReactivation({{ $account->id }})">
                                    <i class="fas fa-undo me-1"></i>Seleccionar
                                </button>
                            @else
                                <span class="badge badge-danger">No reactivable</span>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Estadísticas del Usuario -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Estadísticas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="user-stats">
                        <div class="stat-item">
                            <label>Total de Desactivaciones:</label>
                            <span>{{ $userStats['total_deactivations'] }}</span>
                        </div>
                        <div class="stat-item">
                            <label>Primera Desactivación:</label>
                            <span>{{ $userStats['first_deactivation']->format('d/m/Y') }}</span>
                        </div>
                        <div class="stat-item">
                            <label>Última Desactivación:</label>
                            <span>{{ $userStats['last_deactivation']->format('d/m/Y') }}</span>
                        </div>
                        <div class="stat-item">
                            <label>Total de Reservas:</label>
                            <span>{{ $userStats['total_reservations'] }}</span>
                        </div>
                        <div class="stat-item">
                            <label>Total de Reseñas:</label>
                            <span>{{ $userStats['total_reviews'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Acciones Rápidas -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tools me-2"></i>Acciones
                    </h5>
                </div>
                <div class="card-body">
                    <div class="quick-actions">
                        <a href="{{ route('admin.users.history', $deactivatedUser->email) }}" 
                           class="btn btn-outline-info btn-sm w-100 mb-2">
                            <i class="fas fa-history me-2"></i>Ver Historial Completo
                        </a>
                        
                        @if($deactivatedUser->canBeReactivated())
                            <button type="button" class="btn btn-success btn-sm w-100 mb-2" 
                                    data-bs-toggle="modal" data-bs-target="#reactivationModal">
                                <i class="fas fa-undo me-2"></i>Reactivar Cuenta
                            </button>
                        @endif
                        
                        <a href="mailto:{{ $deactivatedUser->email }}" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-envelope me-2"></i>Enviar Email
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Reactivación -->
<div class="modal fade" id="reactivationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reactivar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.deactivated-users.reactivate', $deactivatedUser) }}">
                @csrf
                <div class="modal-body">
                    @if($multipleAccounts->count() > 1)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Cuentas Múltiples Detectadas:</strong> 
                        Este usuario tiene {{ $multipleAccounts->count() }} cuentas desactivadas. 
                        Selecciona cuál quieres reactivar o usa la cuenta actual.
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="selected_account" class="form-label">Seleccionar Cuenta para Reactivar:</label>
                        <select class="form-select" id="selected_account" name="selected_account">
                            <option value="">Usar cuenta actual ({{ $deactivatedUser->deactivated_at->format('d/m/Y') }})</option>
                            @foreach($multipleAccounts as $account)
                                @if($account->canBeReactivated())
                                    <option value="{{ $account->id }}">
                                        Cuenta #{{ $loop->iteration }} - {{ $account->deactivated_at->format('d/m/Y') }} 
                                        ({{ $account->deactivation_reason_text }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    @endif
                    
                    <div class="form-group">
                        <label for="reactivation_reason" class="form-label">Motivo de Reactivación:</label>
                        <textarea class="form-control" id="reactivation_reason" name="reactivation_reason" 
                                  rows="4" required placeholder="Explica detalladamente por qué se reactiva esta cuenta..."></textarea>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Importante:</strong> Al reactivar la cuenta, se restaurarán todos los datos preservados 
                        incluyendo reservas, reseñas y configuraciones del usuario.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-undo me-2"></i>Reactivar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function selectAccountForReactivation(accountId) {
    document.getElementById('selected_account').value = accountId;
    
    // Mostrar modal de reactivación
    const modal = new bootstrap.Modal(document.getElementById('reactivationModal'));
    modal.show();
}

// Auto-submit del formulario de reactivación
document.getElementById('reactivationModal').addEventListener('submit', function() {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
});
</script>
@endpush
