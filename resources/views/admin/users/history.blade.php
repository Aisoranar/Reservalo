@extends('layouts.app')

@section('title', 'Historial Completo del Usuario - Panel de Administración')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="admin-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="header-icon me-3">
                        <i class="fas fa-history"></i>
                    </div>
                    <div>
                        <h1 class="h2 mb-1">Historial Completo del Usuario</h1>
                        <p class="text-muted mb-0">{{ $email }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="header-actions">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Usuarios
                    </a>
                    <a href="{{ route('admin.deactivated-users.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-user-clock me-2"></i>Usuarios Desactivados
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen del Historial -->
    <div class="history-summary mb-4">
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="summary-card">
                    <div class="summary-icon total">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-number">{{ $history->count() }}</div>
                        <div class="summary-label">Total de Cuentas</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="summary-card">
                    <div class="summary-icon active">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-number">{{ $history->where('type', 'active')->count() }}</div>
                        <div class="summary-label">Cuentas Activas</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="summary-card">
                    <div class="summary-icon deactivated">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-number">{{ $history->where('type', 'deactivated')->count() }}</div>
                        <div class="summary-label">Cuentas Desactivadas</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="summary-card">
                    <div class="summary-icon multiple">
                        <i class="fas fa-copy"></i>
                    </div>
                    <div class="summary-content">
                        <div class="summary-number">{{ $history->where('type', 'deactivated')->count() > 1 ? 'Sí' : 'No' }}</div>
                        <div class="summary-label">Cuentas Múltiples</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline del Historial -->
    <div class="history-timeline">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-stream me-2"></i>Timeline de Actividad
                </h5>
            </div>
            <div class="card-body">
                @if($history->count() > 0)
                    <div class="timeline">
                        @foreach($history as $index => $entry)
                        <div class="timeline-item {{ $entry['type'] }}">
                            <div class="timeline-marker">
                                @if($entry['type'] === 'active')
                                    <i class="fas fa-user-check"></i>
                                @else
                                    <i class="fas fa-user-clock"></i>
                                @endif
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <div class="timeline-title">
                                        @if($entry['type'] === 'active')
                                            <span class="badge badge-success">Cuenta Activa</span>
                                        @else
                                            <span class="badge badge-{{ $entry['user']->deactivation_reason }}">
                                                {{ $entry['user']->deactivation_reason_text }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="timeline-date">
                                        {{ $entry['date']->format('d/m/Y H:i:s') }}
                                        <small class="text-muted">({{ $entry['date']->diffForHumans() }})</small>
                                    </div>
                                </div>
                                
                                <div class="timeline-body">
                                    @if($entry['type'] === 'active')
                                        <div class="user-info">
                                            <h6>{{ $entry['user']->name }}</h6>
                                            <p class="text-muted mb-2">{{ $entry['user']->email }}</p>
                                            
                                            <div class="user-stats">
                                                <div class="stat-item">
                                                    <i class="fas fa-calendar-check text-success"></i>
                                                    <span>{{ $entry['user']->reservations()->count() }} reservas</span>
                                                </div>
                                                <div class="stat-item">
                                                    <i class="fas fa-star text-warning"></i>
                                                    <span>{{ $entry['user']->propertyReviews()->count() }} reseñas</span>
                                                </div>
                                                <div class="stat-item">
                                                    <i class="fas fa-clock text-info"></i>
                                                    <span>{{ $entry['user']->created_at->diffInDays(now()) }} días como miembro</span>
                                                </div>
                                            </div>
                                            
                                            @if($entry['user']->reactivated_at)
                                                <div class="reactivation-info">
                                                    <small class="text-success">
                                                        <i class="fas fa-undo me-1"></i>
                                                        Reactivado el {{ $entry['user']->reactivated_at->format('d/m/Y') }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="deactivated-user-info">
                                            <h6>{{ $entry['user']->name }}</h6>
                                            <p class="text-muted mb-2">{{ $entry['user']->email }}</p>
                                            
                                            @if($entry['data'])
                                                <div class="preserved-data">
                                                    <h6 class="text-primary">Datos Preservados:</h6>
                                                    <div class="data-grid">
                                                        <div class="data-item">
                                                            <i class="fas fa-calendar-check text-success"></i>
                                                            <span>{{ $entry['data']['reservations_count'] ?? 0 }} reservas</span>
                                                        </div>
                                                        <div class="data-item">
                                                            <i class="fas fa-star text-warning"></i>
                                                            <span>{{ $entry['data']['reviews_count'] ?? 0 }} reseñas</span>
                                                        </div>
                                                        <div class="data-item">
                                                            <i class="fas fa-heart text-danger"></i>
                                                            <span>{{ $entry['data']['favorites_count'] ?? 0 }} favoritos</span>
                                                        </div>
                                                        <div class="data-item">
                                                            <i class="fas fa-clock text-info"></i>
                                                            <span>{{ $entry['data']['account_age_days'] ?? 0 }} días</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            <div class="deactivation-details">
                                                <div class="detail-item">
                                                    <strong>Motivo:</strong> {{ $entry['user']->deactivation_reason_text }}
                                                </div>
                                                @if($entry['user']->deactivation_notes)
                                                    <div class="detail-item">
                                                        <strong>Notas:</strong> {{ $entry['user']->deactivation_notes }}
                                                    </div>
                                                @endif
                                                <div class="detail-item">
                                                    <strong>Desactivado por:</strong> 
                                                    {{ $entry['user']->deactivated_by === 'self' ? 'El usuario mismo' : 'Administrador' }}
                                                </div>
                                                @if($entry['user']->deactivation_ip)
                                                    <div class="detail-item">
                                                        <strong>IP:</strong> {{ $entry['user']->deactivation_ip }}
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            @if($entry['user']->hasRequestedReactivation())
                                                <div class="reactivation-request">
                                                    <small class="text-info">
                                                        <i class="fas fa-clock me-1"></i>
                                                        Solicitud de reactivación pendiente
                                                        ({{ $entry['user']->time_since_reactivation_request }})
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="timeline-actions">
                                    @if($entry['type'] === 'active')
                                        <button type="button" class="btn btn-sm btn-warning" 
                                                onclick="showSuspensionModal('{{ $entry['user']->id }}', '{{ $entry['user']->email }}')">
                                            <i class="fas fa-ban me-1"></i>Suspender
                                        </button>
                                        <a href="{{ route('admin.users.history', $entry['user']->email) }}" 
                                           class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-history me-1"></i>Historial
                                        </a>
                                    @else
                                        @if($entry['user']->canBeReactivated())
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="showReactivationModal('{{ $entry['user']->id }}')">
                                                <i class="fas fa-undo me-1"></i>Reactivar
                                            </button>
                                        @else
                                            <span class="badge badge-danger">No reactivable</span>
                                        @endif
                                        <a href="{{ route('admin.deactivated-users.show', $entry['user']) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>Ver Detalles
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <h3>No hay historial disponible</h3>
                        <p>No se encontró información de historial para este usuario.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Suspensión -->
<div class="modal fade" id="suspensionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Suspender Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="suspensionForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="suspension_reason" class="form-label">Motivo de Suspensión</label>
                        <select class="form-select" id="suspension_reason" name="suspension_reason" required>
                            <option value="">Selecciona un motivo</option>
                            <option value="policy_violation">Violación de políticas</option>
                            <option value="suspicious_activity">Actividad sospechosa</option>
                            <option value="temporary_hold">Suspensión temporal</option>
                            <option value="other">Otro motivo</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="suspension_notes" class="form-label">Notas Adicionales</label>
                        <textarea class="form-control" id="suspension_notes" name="suspension_notes" 
                                  rows="3" placeholder="Explica detalladamente el motivo de la suspensión..."></textarea>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>¡Atención!</strong> Al suspender un usuario, su cuenta será desactivada y 
                        no podrá acceder al sistema hasta que sea reactivada por un administrador.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-ban me-2"></i>Suspender Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Reactivación -->
<div class="modal fade" id="reactivationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reactivar Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="reactivationForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reactivation_reason" class="form-label">Motivo de Reactivación</label>
                        <textarea class="form-control" id="reactivation_reason" name="reactivation_reason" 
                                  rows="3" required placeholder="Explica por qué se reactiva esta cuenta..."></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Información:</strong> Al reactivar la cuenta, se restaurarán todos los datos preservados.
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
function showSuspensionModal(userId, userEmail) {
    const form = document.getElementById('suspensionForm');
    form.action = `/admin/users/${userId}/suspend`;
    
    const modal = new bootstrap.Modal(document.getElementById('suspensionModal'));
    modal.show();
}

function showReactivationModal(userId) {
    const form = document.getElementById('reactivationForm');
    form.action = `/admin/deactivated-users/${userId}/reactivate`;
    
    const modal = new bootstrap.Modal(document.getElementById('reactivationModal'));
    modal.show();
}

// Auto-submit de formularios
document.getElementById('suspensionForm').addEventListener('submit', function() {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
});

document.getElementById('reactivationForm').addEventListener('submit', function() {
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
});
</script>
@endpush
