@extends('layouts.app')

@section('title', 'Mis Reservas - Reservalo')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Mejorado -->
    <div class="reservations-header mb-5">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-3">
                    <div class="header-icon me-3">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div>
                        <h1 class="h2 mb-1">Mis Reservas</h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Gestiona todas tus reservas en un solo lugar
                        </p>
                    </div>
                </div>
                
                <!-- Estadísticas rápidas -->
                <div class="stats-row">
                    <div class="stat-item">
                        <div class="stat-number">{{ $reservations->total() }}</div>
                        <div class="stat-label">Total Reservas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $reservations->where('status', 'approved')->count() }}</div>
                        <div class="stat-label">Confirmadas</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $reservations->where('status', 'pending')->count() }}</div>
                        <div class="stat-label">Pendientes</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">{{ $reservations->where('status', 'completed')->count() }}</div>
                        <div class="stat-label">Completadas</div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 text-md-end">
                <div class="header-actions">
                    <a href="{{ route('properties.index') }}" class="btn btn-primary me-2">
                        <i class="fas fa-search me-2"></i>Nueva Reserva
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-home me-2"></i>Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($reservations->count() > 0)
        <!-- Filtros y Ordenamiento -->
        <div class="filters-section mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <label class="form-label fw-bold text-primary mb-0 me-3">
                                    <i class="fas fa-filter me-2"></i>Filtrar por:
                                </label>
                                <select class="form-select form-select-sm" id="statusFilter" style="width: auto;">
                                    <option value="">Todos los estados</option>
                                    <option value="pending">Pendientes</option>
                                    <option value="approved">Aprobadas</option>
                                    <option value="completed">Completadas</option>
                                    <option value="cancelled">Canceladas</option>
                                    <option value="rejected">Rechazadas</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <div class="d-flex align-items-center justify-content-md-end">
                                <label class="form-label fw-bold text-primary mb-0 me-3">
                                    <i class="fas fa-sort me-2"></i>Ordenar por:
                                </label>
                                <select class="form-select form-select-sm" id="sortFilter" style="width: auto;">
                                    <option value="latest">Más recientes</option>
                                    <option value="oldest">Más antiguas</option>
                                    <option value="start_date">Fecha de llegada</option>
                                    <option value="price">Precio</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Reservas Mejorada -->
        <div class="reservations-grid">
            <div class="row row-cols-1 row-cols-lg-2 g-4">
                @foreach($reservations as $reservation)
                    <div class="col">
                        <div class="reservation-card-modern h-100">
                            <!-- Header de la tarjeta -->
                            <div class="reservation-header">
                                <div class="reservation-status">
                                    {!! $reservation->status_badge !!}
                                </div>
                                <div class="reservation-actions">
                                    <div class="dropdown">
                                        <button class="btn btn-link btn-sm text-muted" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('properties.show', $reservation->property) }}">
                                                    <i class="fas fa-eye me-2"></i>Ver Propiedad
                                                </a>
                                            </li>
                                            @if($reservation->canBeCancelled())
                                                <li>
                                                    <button class="dropdown-item text-danger" 
                                                            onclick="cancelReservation({{ $reservation->id }})">
                                                        <i class="fas fa-times me-2"></i>Cancelar Reserva
                                                    </button>
                                                </li>
                                            @endif
                                            <li>
                                                <a class="dropdown-item" href="#" onclick="showReservationDetails({{ $reservation->id }})">
                                                    <i class="fas fa-info-circle me-2"></i>Ver Detalles
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Imagen de la propiedad -->
                            <div class="property-image-section">
                                @if($reservation->property->primaryImage)
                                    <img src="{{ $reservation->property->primaryImage->full_url }}" 
                                         class="property-image" 
                                         alt="{{ $reservation->property->name }}">
                                @else
                                    <div class="property-placeholder">
                                        <i class="fas fa-home"></i>
                                    </div>
                                @endif
                                
                                <!-- Overlay con información rápida -->
                                <div class="image-overlay">
                                    <div class="overlay-content">
                                        <div class="property-type-badge">
                                            {{ ucfirst($reservation->property->type) }}
                                        </div>
                                        <div class="price-badge">
                                            <div class="price-amount">${{ number_format($reservation->total_price, 0) }}</div>
                                            <div class="price-period">{{ $reservation->nights }} noche(s)</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contenido de la reserva -->
                            <div class="reservation-content">
                                <!-- Información de la propiedad -->
                                <div class="property-info mb-3">
                                    <h5 class="property-name">{{ $reservation->property->name }}</h5>
                                    <div class="property-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        @if($reservation->property->city)
                                            <strong>{{ $reservation->property->city->name }}</strong>, {{ $reservation->property->city->department->name }}
                                        @else
                                            {{ $reservation->property->location }}
                                        @endif
                                    </div>
                                </div>

                                <!-- Fechas de la reserva -->
                                <div class="dates-section mb-3">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="date-item">
                                                <div class="date-icon arrival">
                                                    <i class="fas fa-sign-in-alt"></i>
                                                </div>
                                                <div class="date-info">
                                                    <div class="date-label">Llegada</div>
                                                    <div class="date-value">{{ $reservation->start_date->format('d/m/Y') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="date-item">
                                                <div class="date-icon departure">
                                                    <i class="fas fa-sign-out-alt"></i>
                                                </div>
                                                <div class="date-info">
                                                    <div class="date-label">Salida</div>
                                                    <div class="date-value">{{ $reservation->end_date->format('d/m/Y') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Detalles adicionales -->
                                @if($reservation->special_requests)
                                    <div class="special-requests mb-3">
                                        <div class="requests-header">
                                            <i class="fas fa-comment-dots me-2"></i>
                                            <span class="fw-semibold">Solicitudes especiales:</span>
                                        </div>
                                        <div class="requests-content">
                                            {{ $reservation->special_requests }}
                                        </div>
                                    </div>
                                @endif

                                @if($reservation->status === 'rejected' && $reservation->rejection_reason)
                                    <div class="rejection-alert mb-3">
                                        <div class="alert alert-danger border-0">
                                            <div class="d-flex align-items-start">
                                                <i class="fas fa-exclamation-triangle me-2 mt-1"></i>
                                                <div>
                                                    <strong>Motivo del rechazo:</strong>
                                                    <p class="mb-0 mt-1">{{ $reservation->rejection_reason }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Footer de la tarjeta -->
                                <div class="reservation-footer">
                                    <div class="reservation-meta">
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            Solicitado el {{ $reservation->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                    
                                    <div class="reservation-buttons">
                                        <a href="{{ route('properties.show', $reservation->property) }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye me-1"></i>Ver Propiedad
                                        </a>
                                        
                                        @if($reservation->canBeCancelled())
                                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                                    onclick="cancelReservation({{ $reservation->id }})">
                                                <i class="fas fa-times me-1"></i>Cancelar
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Paginación Mejorada -->
        @if($reservations->hasPages())
            <div class="pagination-section mt-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <nav aria-label="Navegación de reservas" class="d-flex justify-content-center">
                            {{ $reservations->links() }}
                        </nav>
                    </div>
                </div>
                </div>
        @endif
    @else
        <!-- Estado vacío mejorado -->
        <div class="empty-state-container">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h3 class="empty-state-title">No tienes reservas aún</h3>
                <p class="empty-state-description">
                    Comienza explorando las propiedades disponibles y haz tu primera reserva para vivir experiencias únicas.
                </p>
                <div class="empty-state-actions">
                    <a href="{{ route('properties.index') }}" class="btn btn-primary btn-lg me-3">
                        <i class="fas fa-search me-2"></i>Explorar Propiedades
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-home me-2"></i>Ir al Inicio
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Modal de confirmación para cancelar mejorado -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Cancelación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="warning-icon mb-3">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h6 class="text-danger">¿Estás seguro de que quieres cancelar esta reserva?</h6>
                </div>
                
                <div class="alert alert-warning border-0">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-info-circle me-2 mt-1"></i>
                        <div>
                            <strong>Importante:</strong> Las cancelaciones están sujetas a las políticas de la propiedad y pueden tener cargos asociados.
                        </div>
                    </div>
                </div>
                
                <div class="text-center">
                    <p class="text-muted small mb-0">
                        Esta acción no se puede deshacer una vez confirmada.
                    </p>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-arrow-left me-2"></i>Mantener Reserva
                </button>
                <form id="cancelForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger px-4">
                        <i class="fas fa-times me-2"></i>Cancelar Reserva
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de detalles de reserva -->
<div class="modal fade" id="reservationDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>Detalles de la Reserva
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="reservationDetailsContent">
                <!-- El contenido se cargará dinámicamente -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<!-- Estilos CSS modernos para la página de reservas -->
<style>
/* Header de Reservas */
.reservations-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 20px;
    position: relative;
    overflow: hidden;
}

.reservations-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.reservations-header > * {
    position: relative;
    z-index: 2;
}

.header-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    backdrop-filter: blur(10px);
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

/* Estadísticas */
.stats-row {
    display: flex;
    gap: 2rem;
    margin-top: 1.5rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    backdrop-filter: blur(10px);
    min-width: 100px;
}

.stat-number {
    font-size: 1.75rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.85rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Filtros */
.filters-section .card {
    border-radius: 16px;
    overflow: hidden;
}

.filters-section .form-label {
    color: #495057;
    margin-bottom: 0;
}

.filters-section .form-select {
    border-radius: 20px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.filters-section .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

/* Tarjetas de Reservas Modernas */
.reservations-grid {
    margin-top: 2rem;
}

.reservation-card-modern {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    height: 100%;
}

.reservation-card-modern:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

/* Header de la tarjeta */
.reservation-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.5rem;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.reservation-status .badge {
    font-size: 0.8rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
}

.reservation-actions .dropdown-toggle {
    padding: 0.25rem 0.5rem;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.reservation-actions .dropdown-toggle:hover {
    background: #e9ecef;
}

/* Sección de imagen */
.property-image-section {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.property-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.reservation-card-modern:hover .property-image {
    transform: scale(1.05);
}

.property-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 3rem;
}

/* Overlay de la imagen */
.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, transparent 30%, transparent 70%, rgba(0,0,0,0.2) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.reservation-card-modern:hover .image-overlay {
    opacity: 1;
}

.overlay-content {
    position: absolute;
    top: 1rem;
    left: 1rem;
    right: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.property-type-badge {
    background: rgba(102, 126, 234, 0.9);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
}

.price-badge {
    background: rgba(255, 255, 255, 0.95);
    padding: 0.5rem 0.8rem;
    border-radius: 15px;
    text-align: center;
    backdrop-filter: blur(10px);
}

.price-amount {
    font-size: 1.1rem;
    font-weight: bold;
    color: #28a745;
    margin-bottom: 0.25rem;
}

.price-period {
    font-size: 0.75rem;
    color: #6c757d;
}

/* Contenido de la reserva */
.reservation-content {
    padding: 1.5rem;
}

.property-info {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 1rem;
}

.property-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.property-location {
    color: #6c757d;
    font-size: 0.95rem;
}

.property-location i {
    color: #667eea;
    margin-right: 0.5rem;
}

.property-location strong {
    color: #495057;
}

/* Sección de fechas */
.dates-section {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1rem;
    margin: 1rem 0;
}

.date-item {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.date-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
}

.date-icon.arrival {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.date-icon.departure {
    background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
}

.date-info {
    flex: 1;
}

.date-label {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.date-value {
    font-weight: 600;
    color: #2c3e50;
}

/* Solicitudes especiales */
.special-requests {
    background: #e3f2fd;
    border-radius: 12px;
    padding: 1rem;
    border-left: 4px solid #2196f3;
}

.requests-header {
    color: #1976d2;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.requests-content {
    color: #1565c0;
    font-size: 0.9rem;
    line-height: 1.5;
}

/* Alerta de rechazo */
.rejection-alert .alert {
    border-radius: 12px;
    background: #fff5f5;
    color: #c53030;
}

/* Footer de la tarjeta */
.reservation-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid #e9ecef;
    margin-top: 1rem;
}

.reservation-meta {
    flex: 1;
}

.reservation-buttons {
    display: flex;
    gap: 0.5rem;
}

.reservation-buttons .btn {
    border-radius: 20px;
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.reservation-buttons .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
}

/* Paginación */
.pagination-section .card {
    border-radius: 16px;
    overflow: hidden;
}

/* Estado vacío */
.empty-state-container {
    padding: 4rem 2rem;
}

.empty-state {
    max-width: 500px;
    margin: 0 auto;
    text-align: center;
}

.empty-state-icon {
    font-size: 5rem;
    color: #dee2e6;
    margin-bottom: 2rem;
}

.empty-state-title {
    color: #495057;
    margin-bottom: 1rem;
    font-weight: 600;
}

.empty-state-description {
    color: #6c757d;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.empty-state-actions .btn {
    border-radius: 25px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.empty-state-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Modales mejorados */
.modal-content {
    border-radius: 20px;
    overflow: hidden;
}

.modal-header {
    padding: 1.5rem 2rem;
}

.modal-body {
    padding: 2rem;
}

.modal-footer {
    padding: 1.5rem 2rem;
}

.warning-icon {
    font-size: 3rem;
    color: #dc3545;
}

/* Responsive */
@media (max-width: 768px) {
    .reservations-header {
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
    
    .stat-item {
        min-width: 80px;
    }
    
    .filters-section .row {
        text-align: center;
    }
    
    .filters-section .col-md-6:first-child {
        margin-bottom: 1rem;
    }
    
    .reservation-footer {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .reservation-buttons {
        justify-content: center;
    }
    
    .empty-state-container {
        padding: 2rem 1rem;
    }
    
    .empty-state-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .empty-state-actions .btn {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .stats-row {
        flex-direction: column;
        align-items: center;
    }
    
    .stat-item {
        width: 100%;
        max-width: 200px;
    }
    
    .property-image-section {
        height: 150px;
    }
    
    .reservation-content {
        padding: 1rem;
    }
    
    .dates-section {
        padding: 0.75rem;
    }
    
    .date-item {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtros y ordenamiento
    const statusFilter = document.getElementById('statusFilter');
    const sortFilter = document.getElementById('sortFilter');
    
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            applyFilters();
        });
    }
    
    if (sortFilter) {
        sortFilter.addEventListener('change', function() {
            applyFilters();
        });
    }
    
    function applyFilters() {
        const status = statusFilter ? statusFilter.value : '';
        const sort = sortFilter ? sortFilter.value : '';
        
        let url = new URL(window.location);
        if (status) {
            url.searchParams.set('status', status);
        } else {
            url.searchParams.delete('status');
        }
        
        if (sort) {
            url.searchParams.set('sort', sort);
        } else {
            url.searchParams.delete('sort');
        }
        
        window.location.href = url.toString();
    }
    
    // Animaciones de entrada para las tarjetas
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
    
    // Observar todas las tarjetas de reservas
    document.querySelectorAll('.reservation-card-modern').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
});

function cancelReservation(reservationId) {
    const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
    const form = document.getElementById('cancelForm');
    form.action = `/reservas/${reservationId}`;
    modal.show();
}

function showReservationDetails(reservationId) {
    // Aquí puedes implementar la lógica para cargar los detalles de la reserva
    console.log('Mostrando detalles de reserva:', reservationId);
    
    // Ejemplo básico - puedes expandir esto según tus necesidades
    const modal = new bootstrap.Modal(document.getElementById('reservationDetailsModal'));
    const content = document.getElementById('reservationDetailsContent');
    
    content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-3 text-muted">Cargando detalles de la reserva...</p>
        </div>
    `;
    
    modal.show();
    
    // Simular carga de datos (reemplaza con tu lógica real)
    setTimeout(() => {
        content.innerHTML = `
            <div class="reservation-details">
                <h6 class="text-primary mb-3">Información de la Reserva #${reservationId}</h6>
                <p>Aquí puedes mostrar información detallada de la reserva, incluyendo:</p>
                <ul>
                    <li>Historial de cambios de estado</li>
                    <li>Comunicaciones con el anfitrión</li>
                    <li>Detalles de facturación</li>
                    <li>Políticas de cancelación</li>
                </ul>
                <p class="text-muted small">Esta funcionalidad se puede expandir según tus necesidades específicas.</p>
            </div>
        `;
    }, 1000);
}
</script>
@endpush
