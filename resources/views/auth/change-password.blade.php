@extends('layouts.app')

@section('title', 'Cambiar Contraseña')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-key me-2"></i>
                        Cambio de Contraseña Obligatorio
                    </h4>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>¡Bienvenido!</strong> Se ha creado una cuenta temporal para ti. 
                        Por seguridad, debes cambiar tu contraseña temporal antes de continuar.
                    </div>

                    <form method="POST" action="{{ route('password.change.update') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="current_password" class="form-label">
                                Contraseña Temporal <span class="text-danger">*</span>
                            </label>
                            <input id="current_password" type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   name="current_password" required autofocus>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Ingresa la contraseña temporal que recibiste por correo electrónico.
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Nueva Contraseña <span class="text-danger">*</span>
                            </label>
                            <input id="password" type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password" required>
                            <div class="form-text">
                                <i class="fas fa-shield-alt me-1"></i>
                                La contraseña debe tener al menos 8 caracteres.
                            </div>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">
                                Confirmar Nueva Contraseña <span class="text-danger">*</span>
                            </label>
                            <input id="password_confirmation" type="password" 
                                   class="form-control" 
                                   name="password_confirmation" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save me-2"></i>
                                Cambiar Contraseña
                            </button>
                        </div>
                    </form>
                </div>

                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-lock me-1"></i>
                                Tu información está protegida
                            </small>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">
                                <i class="fas fa-question-circle me-1"></i>
                                ¿Problemas? Contacta al administrador
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card-header.bg-warning {
    background-color: #ffc107 !important;
    border-bottom: 2px solid #ffb300;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
    font-weight: 600;
    padding: 12px 24px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}
</style>
@endsection
