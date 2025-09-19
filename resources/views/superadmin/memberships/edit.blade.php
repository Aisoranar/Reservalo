@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-edit text-primary me-2"></i>
                        Editar Membresía
                    </h1>
                    <p class="text-muted mb-0">Modificar membresía de {{ $membership->user->name }}</p>
                </div>
                <div>
                    <a href="{{ route('superadmin.memberships') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Información de la Membresía -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-info">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-user-circle fa-3x text-primary mb-2"></i>
                                <h6 class="text-muted">Usuario</h6>
                                <h5 class="mb-0">{{ $membership->user->name }}</h5>
                                <small class="text-muted">{{ $membership->user->email }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-crown fa-3x text-warning mb-2"></i>
                                <h6 class="text-muted">Plan Actual</h6>
                                <h5 class="mb-0">{{ $membership->plan->name }}</h5>
                                <small class="text-muted">${{ number_format($membership->plan->price, 0) }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-calendar-alt fa-3x text-success mb-2"></i>
                                <h6 class="text-muted">Período</h6>
                                <h5 class="mb-0">{{ \Carbon\Carbon::parse($membership->starts_at)->format('d/m/Y') }}</h5>
                                <small class="text-muted">hasta {{ \Carbon\Carbon::parse($membership->expires_at)->format('d/m/Y') }}</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <i class="fas fa-toggle-on fa-3x text-{{ $membership->status === 'active' ? 'success' : ($membership->status === 'inactive' ? 'warning' : 'danger') }} mb-2"></i>
                                <h6 class="text-muted">Estado</h6>
                                <h5 class="mb-0">
                                    <span class="badge bg-{{ $membership->status === 'active' ? 'success' : ($membership->status === 'inactive' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($membership->status) }}
                                    </span>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.memberships.update', $membership) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Usuario -->
                            <div class="col-md-6 mb-3">
                                <label for="user_id" class="form-label">
                                    <i class="fas fa-user text-primary me-1"></i>Usuario <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('user_id') is-invalid @enderror" 
                                        id="user_id" name="user_id" required>
                                    <option value="">Seleccionar usuario...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" 
                                                {{ old('user_id', $membership->user_id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Plan de Membresía -->
                            <div class="col-md-6 mb-3">
                                <label for="membership_plan_id" class="form-label">
                                    <i class="fas fa-crown text-warning me-1"></i>Plan de Membresía <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('membership_plan_id') is-invalid @enderror" 
                                        id="membership_plan_id" name="membership_plan_id" required>
                                    <option value="">Seleccionar plan...</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}" 
                                                data-price="{{ $plan->price }}"
                                                data-duration="{{ $plan->duration_days }}"
                                                {{ old('membership_plan_id', $membership->membership_plan_id) == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }} - ${{ number_format($plan->price, 0) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('membership_plan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fecha de Inicio -->
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">
                                    <i class="fas fa-calendar-alt text-success me-1"></i>Fecha de Inicio <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" 
                                       name="start_date" 
                                       value="{{ old('start_date', $membership->starts_at) }}" 
                                       required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fecha de Fin -->
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">
                                    <i class="fas fa-calendar-times text-danger me-1"></i>Fecha de Fin <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" 
                                       name="end_date" 
                                       value="{{ old('end_date', $membership->expires_at) }}" 
                                       required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Estado -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">
                                    <i class="fas fa-toggle-on text-info me-1"></i>Estado <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="active" {{ old('status', $membership->status) == 'active' ? 'selected' : '' }}>
                                        <i class="fas fa-check-circle text-success"></i> Activa
                                    </option>
                                    <option value="inactive" {{ old('status', $membership->status) == 'inactive' ? 'selected' : '' }}>
                                        <i class="fas fa-pause-circle text-warning"></i> Inactiva
                                    </option>
                                    <option value="expired" {{ old('status', $membership->status) == 'expired' ? 'selected' : '' }}>
                                        <i class="fas fa-times-circle text-danger"></i> Expirada
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Duración Calculada -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-clock text-secondary me-1"></i>Duración Calculada
                                </label>
                                <div class="form-control-plaintext" id="duration-display">
                                    Selecciona un plan para ver la duración
                                </div>
                            </div>

                            <!-- Notas -->
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">
                                    <i class="fas fa-sticky-note text-secondary me-1"></i>Notas
                                </label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" 
                                          id="notes" 
                                          name="notes" 
                                          rows="3" 
                                          placeholder="Notas adicionales sobre la membresía...">{{ old('notes', $membership->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="button" class="btn btn-danger" onclick="deleteMembership()">
                                            <i class="fas fa-trash me-1"></i>Eliminar Membresía
                                        </button>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('superadmin.memberships') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-1"></i>Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-success text-white fw-bold">
                                            <i class="fas fa-save me-1"></i>Actualizar Membresía
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación de Eliminación -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que quieres eliminar esta membresía?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>¡Atención!</strong> Esta acción no se puede deshacer.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <form method="POST" action="{{ route('superadmin.memberships.destroy', $membership) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.btn-success {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
    background: linear-gradient(135deg, #218838, #1ea085);
}

.btn-danger {
    background: linear-gradient(135deg, #dc3545, #e74c3c);
    border: none;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    transition: all 0.3s ease;
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
    background: linear-gradient(135deg, #c82333, #d63031);
}

.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.card {
    border: none;
    box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
}

.border-left-info {
    border-left: 4px solid #36b9cc !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const planSelect = document.getElementById('membership_plan_id');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const durationDisplay = document.getElementById('duration-display');

    // Calcular fecha de fin automáticamente
    function calculateEndDate() {
        const selectedPlan = planSelect.options[planSelect.selectedIndex];
        const startDate = startDateInput.value;
        
        if (selectedPlan && selectedPlan.value && startDate) {
            const durationDays = parseInt(selectedPlan.dataset.duration);
            const start = new Date(startDate);
            const end = new Date(start);
            end.setDate(start.getDate() + durationDays);
            
            endDateInput.value = end.toISOString().split('T')[0];
            
            // Mostrar duración
            durationDisplay.innerHTML = `
                <span class="badge bg-primary">${durationDays} días</span>
                <span class="text-muted ms-2">(${Math.floor(durationDays/30)} meses)</span>
            `;
        } else {
            durationDisplay.textContent = 'Selecciona un plan para ver la duración';
        }
    }

    // Event listeners
    planSelect.addEventListener('change', calculateEndDate);
    startDateInput.addEventListener('change', calculateEndDate);

    // Calcular al cargar si ya hay valores
    calculateEndDate();
});

function deleteMembership() {
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}
</script>
@endsection
