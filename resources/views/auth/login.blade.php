@extends('layouts.auth')

@section('title', 'Iniciar Sesión - Reservalo')

@section('content')
<div class="container-fluid">
    <div class="row min-vh-100 align-items-center justify-content-center">
        <div class="col-12 col-md-6 col-lg-5 col-xl-4">
            <!-- Card de Login -->
            <div class="card border-0 shadow">
                <div class="card-body p-4">
                    <!-- Header del Login -->
                    <div class="text-center mb-3">
                        <div class="mb-2">
                            <div class="d-inline-block p-2 rounded-circle" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-user-lock fa-lg text-white"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold text-dark mb-1">¡Bienvenido de vuelta!</h4>
                        <p class="text-muted small">Inicia sesión en tu cuenta de Reservalo</p>
                    </div>

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Formulario de Login -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold text-dark small">
                                <i class="fas fa-envelope me-1 text-primary"></i>Correo Electrónico
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-at text-muted small"></i>
                                </span>
                                <input id="email" 
                                       type="email" 
                                       class="form-control border-start-0 @error('email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autofocus 
                                       autocomplete="username"
                                       placeholder="tu@email.com">
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block small">
                                    <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold text-dark small">
                                <i class="fas fa-lock me-1 text-primary"></i>Contraseña
                            </label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fas fa-key text-muted small"></i>
                                </span>
                                <input id="password" 
                                       type="password" 
                                       class="form-control border-start-0 @error('password') is-invalid @enderror" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       placeholder="••••••••">
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block small">
                                    <i class="fas fa-exclamation-triangle me-1"></i>{{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input id="remember_me" 
                                       type="checkbox" 
                                       class="form-check-input" 
                                       name="remember"
                                       style="accent-color: #667eea;">
                                <label class="form-check-label text-muted small" for="remember_me">
                                    Recordarme
                                </label>
                            </div>
                            
                            @if (Route::has('password.request'))
                                <a class="text-decoration-none small" href="{{ route('password.request') }}" style="color: #667eea;">
                                    <i class="fas fa-question-circle me-1"></i>¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>

                        <!-- Login Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary fw-semibold" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                            </button>
                        </div>

                        <!-- Divider -->
                        <div class="text-center mb-3">
                            <span class="text-muted small">¿No tienes una cuenta?</span>
                        </div>

                        <!-- Register Link -->
                        <div class="d-grid">
                            <a href="{{ route('register') }}" class="btn btn-outline-primary fw-semibold" style="border-color: #667eea; color: #667eea;">
                                <i class="fas fa-user-plus me-2"></i>Crear Cuenta
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-3">
                <p class="text-muted mb-0">
                    <small>&copy; {{ date('Y') }} Reservalo. Todos los derechos reservados.</small>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionales para el login -->
<style>
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
    }
    
    .card {
        border-radius: 15px;
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
    }
    
    .form-control {
        border-radius: 8px;
        border: 1px solid #e9ecef;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.15rem rgba(102, 126, 234, 0.25);
        transform: translateY(-1px);
    }
    
    .input-group-text {
        border-radius: 8px;
        border: 1px solid #e9ecef;
        background: #f8f9fa;
        padding: 0.5rem 0.75rem;
    }
    
    .btn {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }
    
    .btn-outline-primary:hover {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: transparent;
        color: white;
    }
    
    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }
    
    .text-primary {
        color: #667eea !important;
    }
    
    .alert {
        border-radius: 8px;
        border: none;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .invalid-feedback {
        color: #dc3545;
        font-size: 0.8rem;
        margin-top: 0.2rem;
    }
    
    /* Animaciones */
    .card {
        animation: slideInUp 0.5s ease;
    }
    
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem !important;
        }
        
        .col-12 {
            padding: 0 0.5rem;
        }
        
        .card {
            margin: 1rem;
        }
    }
</style>
@endsection
