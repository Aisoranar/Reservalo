@extends('layouts.app')

@section('title', 'Mi Perfil - Reservalo')

@section('content')
<div class="container-fluid py-4">
    <!-- Header del Perfil -->
    <div class="profile-header mb-5">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-3">
                    <div class="profile-avatar me-4">
                        <div class="avatar-circle">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="h2 mb-1">Mi Perfil</h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Gestiona tu información personal y configuración de cuenta
                        </p>
                    </div>
                </div>
                
                <!-- Información rápida del usuario -->
                <div class="user-info-cards">
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-number">{{ auth()->user()->reservations()->count() }}</div>
                                    <div class="info-label">Reservas</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-number">0</div>
                                    <div class="info-label">Favoritos</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-number">0</div>
                                    <div class="info-label">Reseñas</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-number">{{ auth()->user()->created_at->diffForHumans() }}</div>
                                    <div class="info-label">Miembro desde</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 text-md-end">
                <div class="header-actions">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-home me-2"></i>Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido del Perfil -->
    <div class="profile-content">
        <div class="row">
            <!-- Columna Principal -->
            <div class="col-lg-8">
                <!-- Información del Perfil -->
                <div class="profile-section mb-4">
                    <div class="section-card">
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="fas fa-user-edit"></i>
                            </div>
                            <div class="section-title">
                                <h3>Información del Perfil</h3>
                                <p>Actualiza tu información personal y dirección de email</p>
                            </div>
                        </div>
                        <div class="section-content">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                <!-- Cambio de Contraseña -->
                <div class="profile-section mb-4">
                    <div class="section-card">
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="fas fa-lock"></i>
                            </div>
                            <div class="section-title">
                                <h3>Cambiar Contraseña</h3>
                                <p>Mantén tu cuenta segura con una contraseña fuerte</p>
                            </div>
                        </div>
                        <div class="section-content">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Acciones Rápidas -->
                <div class="profile-section mb-4">
                    <div class="section-card">
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="fas fa-bolt"></i>
                            </div>
                            <div class="section-title">
                                <h3>Acciones Rápidas</h3>
                            </div>
                        </div>
                        <div class="section-content">
                            <div class="quick-actions">
                                <a href="{{ route('reservations.index') }}" class="quick-action-item">
                                    <div class="action-icon">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="action-content">
                                        <div class="action-title">Mis Reservas</div>
                                        <div class="action-subtitle">Gestiona tus reservas</div>
                                    </div>
                                    <div class="action-arrow">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </a>
                                
                                <a href="{{ route('properties.index') }}" class="quick-action-item">
                                    <div class="action-icon">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <div class="action-content">
                                        <div class="action-title">Explorar Propiedades</div>
                                        <div class="action-subtitle">Encuentra tu próximo destino</div>
                                    </div>
                                    <div class="action-arrow">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </a>
                                
                                <a href="{{ route('dashboard') }}" class="quick-action-item">
                                    <div class="action-icon">
                                        <i class="fas fa-tachometer-alt"></i>
                                    </div>
                                    <div class="action-content">
                                        <div class="action-title">Dashboard</div>
                                        <div class="action-subtitle">Panel principal</div>
                                    </div>
                                    <div class="action-arrow">
                                        <i class="fas fa-chevron-right"></i>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas del Usuario -->
                <div class="profile-section mb-4">
                    <div class="section-card">
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div class="section-title">
                                <h3>Mi Actividad</h3>
                            </div>
                        </div>
                        <div class="section-content">
                            <div class="activity-stats">
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                    <div class="stat-info">
                                        <div class="stat-value">{{ auth()->user()->reservations()->where('status', 'confirmed')->count() }}</div>
                                        <div class="stat-label">Reservas Confirmadas</div>
                                    </div>
                                </div>
                                
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="stat-info">
                                        <div class="stat-value">{{ auth()->user()->reservations()->where('status', 'pending')->count() }}</div>
                                        <div class="stat-label">Reservas Pendientes</div>
                                    </div>
                                </div>
                                
                                <div class="stat-item">
                                    <div class="stat-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="stat-info">
                                        <div class="stat-value">{{ auth()->user()->reservations()->where('status', 'completed')->count() }}</div>
                                        <div class="stat-label">Reservas Completadas</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Desactivar Cuenta -->
                <div class="profile-section">
                    <div class="section-card deactivation-zone">
                        <div class="section-header">
                            <div class="section-icon">
                                <i class="fas fa-user-clock"></i>
                            </div>
                            <div class="section-title">
                                <h3>Desactivar Cuenta</h3>
                                <p>Pausa tu cuenta temporalmente o desactívala completamente</p>
                            </div>
                        </div>
                        <div class="section-content">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animaciones de entrada para las secciones
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    // Observar todas las secciones del perfil
    document.querySelectorAll('.profile-section').forEach(section => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        section.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(section);
    });
    
    // Efectos hover para las acciones rápidas
    document.querySelectorAll('.quick-action-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateX(5px)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateX(0)';
        });
    });
});
</script>
@endpush

<!-- Estilos CSS modernos para la página de perfil -->
<style>
/* Header del Perfil */
.profile-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 20px;
    position: relative;
    overflow: hidden;
}

.profile-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.profile-header > * {
    position: relative;
    z-index: 2;
}

.profile-avatar .avatar-circle {
    width: 80px;
    height: 80px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    backdrop-filter: blur(10px);
    border: 3px solid rgba(255, 255, 255, 0.3);
}

.header-actions .btn {
    border-radius: 25px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.header-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Tarjetas de Información */
.user-info-cards {
    margin-top: 2rem;
}

.info-card {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    padding: 1.5rem;
    text-align: center;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.info-card:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-3px);
}

.info-icon {
    font-size: 2rem;
    margin-bottom: 1rem;
    opacity: 0.9;
}

.info-number {
    font-size: 1.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.info-label {
    font-size: 0.9rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Secciones del Perfil */
.profile-section {
    margin-bottom: 2rem;
}

.section-card {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.section-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.12);
}

.section-card.danger-zone {
    border-color: #dc3545;
    box-shadow: 0 5px 20px rgba(220, 53, 69, 0.1);
}

.section-header {
    display: flex;
    align-items: center;
    padding: 1.5rem 2rem;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.section-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    margin-right: 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.danger-zone .section-icon {
    background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
}

.section-title h3 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
    color: #2c3e50;
}

.section-title p {
    margin: 0.25rem 0 0 0;
    color: #6c757d;
    font-size: 0.9rem;
}

.section-content {
    padding: 2rem;
}

/* Formularios */
.profile-form, .password-form, .delete-account-form {
    max-width: 100%;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
}

.input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.input-wrapper .form-control {
    padding: 0.75rem 1rem 0.75rem 3rem;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #fff;
}

.input-wrapper .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    outline: none;
}

.input-icon {
    position: absolute;
    left: 1rem;
    color: #6c757d;
    z-index: 2;
}

.password-toggle {
    position: absolute;
    right: 1rem;
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.password-toggle:hover {
    background: #f8f9fa;
    color: #495057;
}

/* Mensajes de Error y Éxito */
.error-message {
    color: #dc3545;
    font-size: 0.85rem;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
}

.success-message {
    color: #28a745;
    font-size: 0.9rem;
    margin-top: 1rem;
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    background: #d4edda;
    border-radius: 8px;
    border: 1px solid #c3e6cb;
}

/* Verificación de Email */
.email-verification-alert {
    margin-top: 1rem;
}

.email-verified-badge {
    margin-top: 0.5rem;
    font-size: 0.9rem;
    font-weight: 600;
}

/* Indicador de Fortaleza de Contraseña */
.password-strength {
    margin-top: 1rem;
}

.strength-bar {
    width: 100%;
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 0.5rem;
}

.strength-fill {
    height: 100%;
    transition: all 0.3s ease;
    border-radius: 3px;
}

.strength-fill.weak {
    background: #dc3545;
    width: 20%;
}

.strength-fill.medium {
    background: #ffc107;
    width: 60%;
}

.strength-fill.strong {
    background: #28a745;
    width: 100%;
}

.strength-text {
    font-size: 0.85rem;
    font-weight: 600;
}

/* Requisitos de Contraseña */
.password-requirements {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1rem;
    margin-top: 1rem;
}

.requirements-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.75rem;
}

.requirements-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 0.5rem;
}

.requirement-item {
    display: flex;
    align-items: center;
    font-size: 0.85rem;
    color: #6c757d;
    transition: all 0.3s ease;
}

.requirement-item.met {
    color: #28a745;
}

.requirement-item i {
    margin-right: 0.5rem;
    font-size: 0.75rem;
}

/* Botones de Acción */
.form-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e9ecef;
}

.btn-save {
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Acciones Rápidas */
.quick-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.quick-action-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 12px;
    text-decoration: none;
    color: #495057;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.quick-action-item:hover {
    background: #e9ecef;
    color: #2c3e50;
    text-decoration: none;
    transform: translateX(5px);
}

.action-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1rem;
}

.action-content {
    flex: 1;
}

.action-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.action-subtitle {
    font-size: 0.85rem;
    color: #6c757d;
}

.action-arrow {
    color: #6c757d;
    transition: all 0.3s ease;
}

.quick-action-item:hover .action-arrow {
    transform: translateX(3px);
    color: #667eea;
}

/* Estadísticas de Actividad */
.activity-stats {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.stat-item {
    display: flex;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.stat-item:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
    font-size: 1rem;
}

.stat-info {
    flex: 1;
}

.stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.85rem;
    color: #6c757d;
}

/* Formulario de Eliminación de Cuenta */
.danger-warning {
    background: #fff5f5;
    border: 1px solid #fed7d7;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.warning-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.warning-title {
    font-weight: 600;
    color: #c53030;
    margin-left: 0.5rem;
}

.warning-description {
    color: #c53030;
    margin: 0;
    line-height: 1.6;
}

.data-backup-info {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.backup-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.backup-title {
    font-weight: 600;
    color: #0369a1;
    margin-left: 0.5rem;
}

.backup-content p {
    color: #0369a1;
    margin-bottom: 0.75rem;
}

.backup-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.backup-list li {
    color: #0369a1;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.delete-action {
    text-align: center;
}

.btn-delete-account {
    border-radius: 12px;
    padding: 1rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-delete-account:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
}

/* Modal de Eliminación */
.modal-content-delete {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    max-width: 600px;
    margin: 0 auto;
}

.modal-header-delete {
    background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
    color: white;
    padding: 2rem;
    text-align: center;
}

.delete-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
}

.modal-title {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
}

.modal-body-delete {
    padding: 2rem;
}

.confirmation-message {
    margin-bottom: 2rem;
}

.confirmation-text {
    font-size: 1.1rem;
    color: #495057;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.deletion-checklist {
    margin-bottom: 1.5rem;
}

.checklist-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
    color: #6c757d;
}

.checklist-item i {
    margin-right: 0.75rem;
    font-size: 1.1rem;
}

.final-warning {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 1rem;
    color: #856404;
    text-align: center;
}

.password-confirmation {
    margin-bottom: 2rem;
}

.modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
}

.modal-actions .btn {
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.modal-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Formulario de Desactivación de Cuenta */
.deactivation-zone {
    border-color: #ffc107;
    box-shadow: 0 5px 20px rgba(255, 193, 7, 0.1);
}

.deactivation-zone .section-icon {
    background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
}

.deactivation-info {
    background: #e3f2fd;
    border: 1px solid #bbdefb;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.info-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.info-title {
    font-weight: 600;
    color: #1976d2;
    margin-left: 0.5rem;
}

.info-description {
    color: #1976d2;
    margin: 0;
    line-height: 1.6;
}

.deactivation-benefits {
    background: #f3e5f5;
    border: 1px solid #e1bee7;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.benefits-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.benefits-title {
    font-weight: 600;
    color: #7b1fa2;
    margin-left: 0.5rem;
}

.benefits-content p {
    color: #7b1fa2;
    margin-bottom: 0.75rem;
}

.benefits-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.benefits-list li {
    color: #7b1fa2;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.deactivation-options {
    background: #fff3e0;
    border: 1px solid #ffcc80;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.options-header {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.options-title {
    font-weight: 600;
    color: #f57c00;
    margin-left: 0.5rem;
}

.options-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.option-card {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    border: 1px solid #ffcc80;
    transition: all 0.3s ease;
}

.option-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 193, 7, 0.2);
}

.option-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.75rem;
    font-size: 1.1rem;
}

.option-content h6 {
    color: #f57c00;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.option-content p {
    color: #666;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.deactivation-form {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.deactivation-actions {
    display: flex;
    gap: 1rem;
    justify-content: center;
    margin-top: 2rem;
}

.btn-deactivate-account {
    border-radius: 12px;
    padding: 1rem 2rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-deactivate-account:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 193, 7, 0.3);
}

.reactivation-info {
    background: #e8f5e8;
    border: 1px solid #c8e6c9;
    border-radius: 12px;
    padding: 1.5rem;
}

.reactivation-info .info-header {
    color: #2e7d32;
}

.reactivation-info .info-title {
    color: #2e7d32;
}

.reactivation-info .info-content p {
    color: #2e7d32;
    margin-bottom: 1rem;
}

.reactivation-steps {
    color: #2e7d32;
    margin-bottom: 1rem;
    padding-left: 1.5rem;
}

.reactivation-steps li {
    margin-bottom: 0.5rem;
}

.reactivation-note {
    background: #fff3e0;
    border: 1px solid #ffcc80;
    border-radius: 8px;
    padding: 1rem;
    color: #f57c00;
    font-size: 0.9rem;
}

/* Responsive */
@media (max-width: 768px) {
    .profile-header {
        padding: 1.5rem 1rem;
        text-align: center;
    }
    
    .header-actions {
        margin-top: 1rem;
        justify-content: center;
    }
    
    .stats-row {
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
    }
    
    .info-card {
        min-width: 120px;
    }
    
    .section-header {
        flex-direction: column;
        text-align: center;
        padding: 1rem;
    }
    
    .section-icon {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .section-content {
        padding: 1rem;
    }
    
    .form-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .requirements-list {
        grid-template-columns: 1fr;
    }
    
    .modal-actions {
        flex-direction: column;
    }
}

@media (max-width: 576px) {
    .user-info-cards .row {
        margin: 0;
    }
    
    .info-card {
        margin-bottom: 1rem;
    }
    
    .quick-action-item {
        padding: 0.75rem;
    }
    
    .action-icon {
        width: 35px;
        height: 35px;
        font-size: 0.9rem;
    }
}

/* Responsive para desactivación */
@media (max-width: 768px) {
    .options-grid {
        grid-template-columns: 1fr;
    }
    
    .deactivation-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .deactivation-actions .btn {
        width: 100%;
    }
}
</style>
