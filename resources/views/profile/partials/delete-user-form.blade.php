<div class="deactivate-account-form">
    <div class="deactivation-info">
        <div class="info-header">
            <i class="fas fa-user-clock text-info"></i>
            <span class="info-title">Desactivar Cuenta</span>
        </div>
        <p class="info-description">
            Al desactivar tu cuenta, tu perfil y datos se ocultarán pero se mantendrán seguros en nuestro sistema. 
            Podrás reactivarla en cualquier momento.
        </p>
    </div>

    <div class="deactivation-benefits">
        <div class="benefits-header">
            <i class="fas fa-shield-alt text-success"></i>
            <span class="benefits-title">¿Por qué desactivar en lugar de eliminar?</span>
        </div>
        <div class="benefits-content">
            <ul class="benefits-list">
                <li><i class="fas fa-check-circle text-success me-2"></i><strong>Seguridad:</strong> Tus datos se mantienen seguros</li>
                <li><i class="fas fa-check-circle text-success me-2"></i><strong>Recuperación:</strong> Puedes reactivar tu cuenta cuando quieras</li>
                <li><i class="fas fa-check-circle text-success me-2"></i><strong>Historial:</strong> Mantienes tu historial de reservas y reseñas</li>
                <li><i class="fas fa-check-circle text-success me-2"></i><strong>Flexibilidad:</strong> Ideal para pausas temporales</li>
            </ul>
        </div>
    </div>

    <div class="deactivation-options">
        <div class="options-header">
            <i class="fas fa-cog text-primary"></i>
            <span class="options-title">Opciones de Desactivación</span>
        </div>
        
        <div class="options-grid">
            <div class="option-card">
                <div class="option-icon">
                    <i class="fas fa-pause"></i>
                </div>
                <div class="option-content">
                    <h6>Pausa Temporal</h6>
                    <p>Ideal para vacaciones o pausas cortas</p>
                    <small class="text-muted">Reactivación inmediata disponible</small>
                </div>
            </div>
            
            <div class="option-card">
                <div class="option-icon">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="option-content">
                    <h6>Desactivación Completa</h6>
                    <p>Oculta tu perfil completamente</p>
                    <small class="text-muted">Requiere verificación para reactivar</small>
                </div>
            </div>
        </div>
    </div>

    <div class="deactivation-form">
        <form method="post" action="{{ route('profile.deactivate') }}" class="deactivate-form-content">
            @csrf
            @method('patch')
            
            <div class="form-group">
                <label for="deactivation_reason" class="form-label">
                    <i class="fas fa-question-circle me-2"></i>Motivo de Desactivación
                </label>
                <select id="deactivation_reason" name="deactivation_reason" class="form-select" required>
                    <option value="">Selecciona un motivo</option>
                    <option value="temporary_break">Pausa temporal</option>
                    <option value="privacy_concerns">Preocupaciones de privacidad</option>
                    <option value="not_using_service">No uso el servicio</option>
                    <option value="technical_issues">Problemas técnicos</option>
                    <option value="other">Otro motivo</option>
                </select>
            </div>

            <div class="form-group">
                <label for="deactivation_notes" class="form-label">
                    <i class="fas fa-comment me-2"></i>Comentarios Adicionales (Opcional)
                </label>
                <textarea id="deactivation_notes" name="deactivation_notes" 
                          class="form-control" rows="3" 
                          placeholder="Cuéntanos más sobre tu decisión..."></textarea>
            </div>

            <div class="form-group">
                <label for="deactivation_password" class="form-label">
                    <i class="fas fa-key me-2"></i>Confirma tu Contraseña
                </label>
                <div class="input-wrapper">
                    <input id="deactivation_password" name="password" type="password" 
                           class="form-control" 
                           placeholder="Ingresa tu contraseña para confirmar"
                           required>
                    <div class="input-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <button type="button" class="password-toggle" onclick="toggleDeactivationPassword()">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password')
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <div class="deactivation-actions">
                <button type="submit" class="btn btn-warning btn-deactivate-account">
                    <i class="fas fa-pause me-2"></i>Desactivar Mi Cuenta
                </button>
                
                <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i>Cancelar
                </a>
            </div>
        </form>
    </div>

    <!-- Información de Reactivación -->
    <div class="reactivation-info">
        <div class="info-header">
            <i class="fas fa-undo text-success"></i>
            <span class="info-title">¿Cómo Reactivar tu Cuenta?</span>
        </div>
        <div class="info-content">
            <p>Para reactivar tu cuenta, simplemente:</p>
            <ol class="reactivation-steps">
                <li>Inicia sesión con tu email y contraseña</li>
                <li>Confirma que quieres reactivar tu cuenta</li>
                <li>Tu perfil y datos estarán disponibles inmediatamente</li>
            </ol>
            <div class="reactivation-note">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Nota:</strong> Si tu cuenta fue suspendida por violaciones de políticas, 
                contacta a nuestro equipo de soporte para revisar tu caso.
            </div>
        </div>
    </div>
</div>

<script>
function toggleDeactivationPassword() {
    const input = document.getElementById('deactivation_password');
    const toggle = input.nextElementSibling.nextElementSibling;
    const icon = toggle.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Validación del formulario
document.querySelector('.deactivate-form-content').addEventListener('submit', function(e) {
    const reason = document.getElementById('deactivation_reason').value;
    const password = document.getElementById('deactivation_password').value;
    
    if (!reason) {
        e.preventDefault();
        alert('Por favor selecciona un motivo de desactivación');
        return false;
    }
    
    if (!password) {
        e.preventDefault();
        alert('Por favor ingresa tu contraseña para confirmar');
        return false;
    }
    
    // Confirmación final
    if (!confirm('¿Estás seguro de que quieres desactivar tu cuenta? Podrás reactivarla en cualquier momento.')) {
        e.preventDefault();
        return false;
    }
});
</script>
