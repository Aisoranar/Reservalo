@extends('layouts.app')

@section('title', 'Configuraciones del Sistema - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-sliders-h text-primary me-2"></i>
                        Configuraciones del Sistema
                    </h1>
                    <p class="text-muted mb-0">Administra la configuración global del sistema</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de Configuraciones -->
    <div class="row">
        <div class="col-12">
            <form method="POST" action="{{ route('superadmin.settings.update') }}">
                @csrf

                <!-- Configuraciones Generales -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-cogs me-2"></i>Configuraciones Generales
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Nombre del Sitio</label>
                                    <input type="text" class="form-control" name="site_name" 
                                           value="{{ $settings['site_name'] ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Email de Contacto</label>
                                    <input type="email" class="form-control" name="contact_email" 
                                           value="{{ $settings['contact_email'] ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Teléfono de Contacto</label>
                                    <input type="text" class="form-control" name="contact_phone" 
                                           value="{{ $settings['contact_phone'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label">Dirección</label>
                                    <input type="text" class="form-control" name="site_address" 
                                           value="{{ $settings['site_address'] ?? '' }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Descripción del Sitio</label>
                            <textarea class="form-control" name="site_description" rows="3">{{ $settings['site_description'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Botón de Guardar Rápido -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-info d-flex justify-content-between align-items-center">
                            <div>
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>¿Listo para guardar?</strong> Haz clic en el botón verde para guardar todas las configuraciones.
                            </div>
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="fas fa-save me-2"></i>GUARDAR AHORA
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Configuraciones de Sistema -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-server me-2"></i>Estado del Sistema
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="site_active" 
                                           {{ ($settings['site_active'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        <strong>Sitio Activo</strong>
                                        <br><small class="text-muted">Permite el acceso general al sitio</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="maintenance_mode" 
                                           {{ ($settings['maintenance_mode'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        <strong>Modo Mantenimiento</strong>
                                        <br><small class="text-muted">Muestra página de mantenimiento</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="registration_enabled" 
                                           {{ ($settings['registration_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        <strong>Registro Habilitado</strong>
                                        <br><small class="text-muted">Permite nuevos registros de usuarios</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="email_verification_required" 
                                           {{ ($settings['email_verification_required'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        <strong>Verificación de Email Requerida</strong>
                                        <br><small class="text-muted">Requiere verificación de email para activar cuenta</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Mensaje de Mantenimiento</label>
                            <textarea class="form-control" name="maintenance_message" rows="3" 
                                      placeholder="Mensaje que se mostrará durante el mantenimiento...">{{ $settings['maintenance_message'] ?? '' }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Configuraciones de Membresías -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-gem me-2"></i>Configuraciones de Membresías
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label">Días de Notificación</label>
                                    <input type="number" class="form-control" name="membership_notification_days" 
                                           value="{{ $settings['membership_notification_days'] ?? 7 }}" min="1" max="30">
                                    <small class="text-muted">Días antes de expirar para notificar</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label">Período de Gracia (días)</label>
                                    <input type="number" class="form-control" name="membership_grace_period" 
                                           value="{{ $settings['membership_grace_period'] ?? 3 }}" min="0" max="30">
                                    <small class="text-muted">Días de gracia después de expirar</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label class="form-label">Máximo Períodos de Prueba</label>
                                    <input type="number" class="form-control" name="max_trial_periods" 
                                           value="{{ $settings['max_trial_periods'] ?? 1 }}" min="0" max="5">
                                    <small class="text-muted">Máximo períodos de prueba por usuario</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="auto_renewal_enabled" 
                                           {{ ($settings['auto_renewal_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        <strong>Renovación Automática Habilitada</strong>
                                        <br><small class="text-muted">Permite renovación automática de membresías</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="membership_required" 
                                           {{ ($settings['membership_required'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        <strong>Membresía Requerida</strong>
                                        <br><small class="text-muted">Requiere membresía activa para usar el sistema</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuraciones de Notificaciones -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-bell me-2"></i>Configuraciones de Notificaciones
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="email_notifications_enabled" 
                                           {{ ($settings['email_notifications_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        <strong>Notificaciones por Email</strong>
                                        <br><small class="text-muted">Habilita el envío de notificaciones por email</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="push_notifications_enabled" 
                                           {{ ($settings['push_notifications_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        <strong>Notificaciones Push</strong>
                                        <br><small class="text-muted">Habilita notificaciones push en el navegador</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="admin_notifications_enabled" 
                                           {{ ($settings['admin_notifications_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        <strong>Notificaciones para Admins</strong>
                                        <br><small class="text-muted">Notifica a administradores sobre eventos importantes</small>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="user_notifications_enabled" 
                                           {{ ($settings['user_notifications_enabled'] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        <strong>Notificaciones para Usuarios</strong>
                                        <br><small class="text-muted">Notifica a usuarios sobre eventos relevantes</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-lg border-0">
                            <div class="card-body bg-light">
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="{{ route('superadmin.dashboard') }}" class="btn btn-secondary btn-lg w-100">
                                            <i class="fas fa-arrow-left me-2"></i>Volver al Dashboard
                                        </a>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-outline-warning btn-lg w-100 mb-2" onclick="resetToDefaults()">
                                            <i class="fas fa-undo me-2"></i>Restaurar Valores por Defecto
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Botón de Guardar Principal -->
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-success btn-lg w-100 py-3" style="font-size: 1.2rem; font-weight: bold;">
                                            <i class="fas fa-save me-3"></i>GUARDAR CONFIGURACIONES
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Los cambios se aplicarán inmediatamente después de guardar
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Botón de Guardar Flotante -->
                <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1000;">
                    <button type="submit" class="btn btn-success btn-lg shadow-lg" style="border-radius: 50px; padding: 15px 30px;">
                        <i class="fas fa-save me-2"></i>GUARDAR
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Mostrar mensaje de éxito si existe
@if(session('success'))
    document.addEventListener('DOMContentLoaded', function() {
        alert('{{ session('success') }}');
    });
@endif

// Mostrar mensaje de error si existe
@if(session('error'))
    document.addEventListener('DOMContentLoaded', function() {
        alert('{{ session('error') }}');
    });
@endif

function resetToDefaults() {
    if (confirm('¿Estás seguro de que quieres restaurar todas las configuraciones a sus valores por defecto? Esta acción no se puede deshacer.')) {
        fetch('{{ route("superadmin.settings.reset") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'No se pudieron restaurar las configuraciones'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al restaurar las configuraciones');
        });
    }
}

// Confirmar antes de enviar el formulario
document.querySelector('form').addEventListener('submit', function(e) {
    if (!confirm('¿Estás seguro de que quieres guardar estas configuraciones?')) {
        e.preventDefault();
    }
});
</script>
@endpush
@endsection
