<div class="profile-form">
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="profile-form-content">
        @csrf
        @method('patch')

        <!-- Campo Nombre -->
        <div class="form-group">
            <label for="name" class="form-label">
                <i class="fas fa-user me-2"></i>Nombre Completo
            </label>
            <div class="input-wrapper">
                <input id="name" name="name" type="text" 
                       class="form-control" 
                       value="{{ old('name', $user->name) }}" 
                       required autofocus autocomplete="name" 
                       placeholder="Ingresa tu nombre completo">
                <div class="input-icon">
                    <i class="fas fa-user"></i>
                </div>
            </div>
            @error('name')
                <div class="error-message">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror
        </div>

        <!-- Campo Email -->
        <div class="form-group">
            <label for="email" class="form-label">
                <i class="fas fa-envelope me-2"></i>Correo Electrónico
            </label>
            <div class="input-wrapper">
                <input id="email" name="email" type="email" 
                       class="form-control" 
                       value="{{ old('email', $user->email) }}" 
                       required autocomplete="username" 
                       placeholder="tu@email.com">
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                </div>
            </div>
            @error('email')
                <div class="error-message">
                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                </div>
            @enderror

            <!-- Verificación de Email -->
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="email-verification-alert">
                    <div class="alert alert-warning border-0">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-exclamation-triangle me-2 mt-1 text-warning"></i>
                            <div>
                                <strong>Tu dirección de email no está verificada.</strong>
                                <p class="mb-2 mt-1">Por favor verifica tu email para acceder a todas las funcionalidades.</p>
                                <button form="send-verification" class="btn btn-warning btn-sm">
                                    <i class="fas fa-paper-plane me-1"></i>Reenviar Email de Verificación
                                </button>
                            </div>
                        </div>
                    </div>

                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success border-0">
                            <i class="fas fa-check-circle me-2"></i>
                            Se ha enviado un nuevo enlace de verificación a tu dirección de email.
                        </div>
                    @endif
                </div>
            @else
                <div class="email-verified-badge">
                    <i class="fas fa-check-circle text-success me-1"></i>
                    <span class="text-success">Email verificado</span>
                </div>
            @endif
        </div>

        <!-- Botones de Acción -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-save">
                <i class="fas fa-save me-2"></i>Guardar Cambios
            </button>

            @if (session('status') === 'profile-updated')
                <div class="success-message" 
                     x-data="{ show: true }" 
                     x-show="show" 
                     x-transition 
                     x-init="setTimeout(() => show = false, 3000)">
                    <i class="fas fa-check-circle me-2"></i>
                    <span>¡Perfil actualizado exitosamente!</span>
                </div>
            @endif
        </div>
    </form>
</div>
