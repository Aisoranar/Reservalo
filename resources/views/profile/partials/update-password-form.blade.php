<div class="password-form">
    <form method="post" action="{{ route('password.update') }}" class="password-form-content">
        @csrf
        @method('put')

        <!-- Contraseña Actual -->
        <div class="form-group">
            <label for="update_password_current_password" class="form-label">
                <i class="fas fa-key me-2"></i>Contraseña Actual
            </label>
            <div class="input-wrapper">
                <input id="update_password_current_password" name="current_password" type="password" 
                       class="form-control" 
                       autocomplete="current-password" 
                       placeholder="Ingresa tu contraseña actual">
                <div class="input-icon">
                    <i class="fas fa-key"></i>
                </div>
                <button type="button" class="password-toggle" onclick="togglePassword('update_password_current_password')">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @error('updatePassword.current_password')
                <div class="error-message">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <!-- Nueva Contraseña -->
        <div class="form-group">
            <label for="update_password_password" class="form-label">
                <i class="fas fa-lock me-2"></i>Nueva Contraseña
            </label>
            <div class="input-wrapper">
                <input id="update_password_password" name="password" type="password" 
                       class="form-control" 
                       autocomplete="new-password" 
                       placeholder="Ingresa tu nueva contraseña">
                <div class="input-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <button type="button" class="password-toggle" onclick="togglePassword('update_password_password')">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @error('updatePassword.password')
                <div class="error-message">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
            
            <!-- Indicador de Fortaleza de Contraseña -->
            <div class="password-strength mt-2">
                <div class="strength-bar">
                    <div class="strength-fill" id="passwordStrength"></div>
                </div>
                <div class="strength-text" id="passwordStrengthText">Ingresa una contraseña</div>
            </div>
        </div>

        <!-- Confirmar Nueva Contraseña -->
        <div class="form-group">
            <label for="update_password_password_confirmation" class="form-label">
                <i class="fas fa-check-circle me-2"></i>Confirmar Nueva Contraseña
            </label>
            <div class="input-wrapper">
                <input id="update_password_password_confirmation" name="password_confirmation" type="password" 
                       class="form-control" 
                       autocomplete="new-password" 
                       placeholder="Confirma tu nueva contraseña">
                <div class="input-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <button type="button" class="password-toggle" onclick="togglePassword('update_password_password_confirmation')">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            @error('updatePassword.password_confirmation')
                <div class="error-message">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <!-- Requisitos de Contraseña -->
        <div class="password-requirements">
            <h6 class="requirements-title">
                <i class="fas fa-shield-alt me-2"></i>Requisitos de Seguridad
            </h6>
            <div class="requirements-list">
                <div class="requirement-item" id="req-length">
                    <i class="fas fa-circle"></i>
                    <span>Mínimo 8 caracteres</span>
                </div>
                <div class="requirement-item" id="req-uppercase">
                    <i class="fas fa-circle"></i>
                    <span>Al menos una mayúscula</span>
                </div>
                <div class="requirement-item" id="req-lowercase">
                    <i class="fas fa-circle"></i>
                    <span>Al menos una minúscula</span>
                </div>
                <div class="requirement-item" id="req-number">
                    <i class="fas fa-circle"></i>
                    <span>Al menos un número</span>
                </div>
                <div class="requirement-item" id="req-special">
                    <i class="fas fa-circle"></i>
                    <span>Al menos un carácter especial</span>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-save">
                <i class="fas fa-key me-2"></i>Actualizar Contraseña
            </button>

            @if (session('status') === 'password-updated')
                <div class="success-message" 
                     x-data="{ show: true }" 
                     x-show="show" 
                     x-transition 
                     x-init="setTimeout(() => show = false, 3000)">
                    <i class="fas fa-check-circle me-2"></i>
                    <span>¡Contraseña actualizada exitosamente!</span>
                </div>
            @endif
        </div>
    </form>
</div>

<script>
// Función para mostrar/ocultar contraseña
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
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

// Validación de fortaleza de contraseña
document.getElementById('update_password_password').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('passwordStrength');
    const strengthText = document.getElementById('passwordStrengthText');
    
    // Requisitos
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /\d/.test(password),
        special: /[!@#$%^&*(),.?":{}|<>]/.test(password)
    };
    
    // Actualizar indicadores visuales
    Object.keys(requirements).forEach(req => {
        const reqElement = document.getElementById(`req-${req}`);
        if (requirements[req]) {
            reqElement.classList.add('met');
            reqElement.querySelector('i').className = 'fas fa-check-circle text-success';
        } else {
            reqElement.classList.remove('met');
            reqElement.querySelector('i').className = 'fas fa-circle text-muted';
        }
    });
    
    // Calcular fortaleza
    const metCount = Object.values(requirements).filter(Boolean).length;
    const strength = (metCount / 5) * 100;
    
    strengthBar.style.width = strength + '%';
    
    // Actualizar color y texto
    if (strength < 40) {
        strengthBar.className = 'strength-fill weak';
        strengthText.textContent = 'Débil';
        strengthText.className = 'strength-text text-danger';
    } else if (strength < 80) {
        strengthBar.className = 'strength-fill medium';
        strengthText.textContent = 'Media';
        strengthText.className = 'strength-text text-warning';
    } else {
        strengthBar.className = 'strength-fill strong';
        strengthText.textContent = 'Fuerte';
        strengthText.className = 'strength-text text-success';
    }
});
</script>
