@extends('layouts.app')

@section('title', 'Dashboard - Reservalo')

@section('content')
<div class="container-fluid py-4">
    <!-- Header del Dashboard -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-dark fw-bold">
                        <i class="fas fa-tachometer-alt me-2 text-primary"></i>Dashboard
                    </h1>
                    <p class="text-muted mb-0">Bienvenido de vuelta, {{ Auth::user()->name }}!</p>
                </div>
                <div class="text-end">
                    <p class="text-muted mb-0 small">Último acceso</p>
                    <p class="mb-0 fw-semibold">{{ now()->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tarjetas de Estadísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 rounded-circle" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <i class="fas fa-calendar-check fa-2x text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title text-muted mb-1">Mis Reservas</h5>
                            <h3 class="mb-0 fw-bold text-dark">{{ $stats['reservations'] }}</h3>
                            <p class="text-success mb-0 small">
                                @if($stats['reservations'] > 0)
                                    <i class="fas fa-arrow-up me-1"></i>{{ $stats['reservations'] }} reserva(s) activa(s)
                                @else
                                    <i class="fas fa-info-circle me-1"></i>Sin reservas activas
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 rounded-circle" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                                <i class="fas fa-heart fa-2x text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title text-muted mb-1">Favoritos</h5>
                            <h3 class="mb-0 fw-bold text-dark">{{ $stats['favorites'] }}</h3>
                            <p class="text-info mb-0 small">
                                @if($stats['favorites'] > 0)
                                    <i class="fas fa-heart me-1"></i>{{ $stats['favorites'] }} propiedad(es) favorita(s)
                                @else
                                    <i class="fas fa-info-circle me-1"></i>No tienes favoritos
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 rounded-circle" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                                <i class="fas fa-eye fa-2x text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title text-muted mb-1">Propiedades Vistas</h5>
                            <h3 class="mb-0 fw-bold text-dark">{{ $stats['properties_viewed'] }}</h3>
                            <p class="text-warning mb-0 small">
                                @if($stats['properties_viewed'] > 0)
                                    <i class="fas fa-eye me-1"></i>{{ $stats['properties_viewed'] }} propiedad(es) vista(s)
                                @else
                                    <i class="fas fa-clock me-1"></i>Comienza a explorar
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="p-3 rounded-circle" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
                                <i class="fas fa-star fa-2x text-white"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="card-title text-muted mb-1">Reseñas</h5>
                            <h3 class="mb-0 fw-bold text-dark">{{ $stats['reviews'] }}</h3>
                            <p class="text-danger mb-0 small">
                                @if($stats['reviews'] > 0)
                                    <i class="fas fa-star me-1"></i>{{ $stats['reviews'] }} reseña(s) escrita(s)
                                @else
                                    <i class="fas fa-comment me-1"></i>Sin reseñas aún
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones Rápidas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-bolt me-2 text-warning"></i>Acciones Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('properties.index') }}" class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" style="border-color: #667eea; color: #667eea; min-height: 120px;">
                                <i class="fas fa-search fa-2x mb-2"></i>
                                <span class="fw-semibold">Buscar Propiedades</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('reservations.index') }}" class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" style="border-color: #28a745; color: #28a745; min-height: 120px;">
                                <i class="fas fa-calendar-alt fa-2x mb-2"></i>
                                <span class="fw-semibold">Mis Reservas</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('profile.edit') }}" class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" style="border-color: #17a2b8; color: #17a2b8; min-height: 120px;">
                                <i class="fas fa-user-edit fa-2x mb-2"></i>
                                <span class="fw-semibold">Editar Perfil</span>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('favorites.index') }}" class="btn btn-outline-danger w-100 h-100 d-flex flex-column align-items-center justify-content-center p-3" style="border-color: #dc3545; color: #dc3545; min-height: 120px;">
                                <i class="fas fa-heart fa-2x mb-2"></i>
                                <span class="fw-semibold">Mis Favoritos</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-history me-2 text-info"></i>Actividad Reciente
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentReservations->count() > 0 || $recentFavorites->count() > 0 || $recentReviews->count() > 0)
                        <!-- Reservas Recientes -->
                        @if($recentReservations->count() > 0)
                            <div class="mb-4">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="fas fa-calendar-check me-2"></i>Reservas Recientes
                                </h6>
                                <div class="row">
                                    @foreach($recentReservations->take(3) as $reservation)
                                        <div class="col-md-4 mb-3">
                                            <div class="card border-0 shadow-sm h-100">
                                                @if($reservation->property->images->count() > 0)
                                                    <img src="{{ $reservation->property->images->first()->full_url }}" 
                                                         class="card-img-top" 
                                                         alt="{{ $reservation->property->name }}"
                                                         style="height: 150px; object-fit: cover;">
                                                @endif
                                                <div class="card-body">
                                                    <h6 class="card-title fw-bold">{{ $reservation->property->name }}</h6>
                                                    <p class="text-muted small mb-2">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        {{ \Carbon\Carbon::parse($reservation->check_in)->format('d/m/Y') }} - 
                                                        {{ \Carbon\Carbon::parse($reservation->check_out)->format('d/m/Y') }}
                                                    </p>
                                                    <span class="badge bg-{{ $reservation->status === 'pending' ? 'warning' : ($reservation->status === 'approved' ? 'success' : 'danger') }}">
                                                        {{ ucfirst($reservation->status) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Favoritos Recientes -->
                        @if($recentFavorites->count() > 0)
                            <div class="mb-4">
                                <h6 class="text-danger fw-bold mb-3">
                                    <i class="fas fa-heart me-2"></i>Favoritos Recientes
                                </h6>
                                <div class="row">
                                    @foreach($recentFavorites->take(3) as $favorite)
                                        <div class="col-md-4 mb-3">
                                            <div class="card border-0 shadow-sm h-100">
                                                @if($favorite->property->images->count() > 0)
                                                    <img src="{{ $favorite->property->images->first()->full_url }}" 
                                                         class="card-img-top" 
                                                         alt="{{ $favorite->property->name }}"
                                                         style="height: 150px; object-fit: cover;">
                                                @endif
                                                <div class="card-body">
                                                    <h6 class="card-title fw-bold">{{ $favorite->property->name }}</h6>
                                                    <p class="text-muted small mb-2">
                                                        <i class="fas fa-map-marker-alt me-1"></i>
                                                        {{ $favorite->property->city->name ?? 'Ubicación no disponible' }}
                                                    </p>
                                                    <a href="{{ route('properties.show', $favorite->property) }}" class="btn btn-sm btn-outline-primary">
                                                        Ver Propiedad
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Reseñas Recientes -->
                        @if($recentReviews->count() > 0)
                            <div class="mb-4">
                                <h6 class="text-warning fw-bold mb-3">
                                    <i class="fas fa-star me-2"></i>Reseñas Recientes
                                </h6>
                                <div class="row">
                                    @foreach($recentReviews->take(3) as $review)
                                        <div class="col-md-4 mb-3">
                                            <div class="card border-0 shadow-sm h-100">
                                                @if($review->property->images->count() > 0)
                                                    <img src="{{ $review->property->images->first()->full_url }}" 
                                                         class="card-img-top" 
                                                         alt="{{ $review->property->name }}"
                                                         style="height: 150px; object-fit: cover;">
                                                @endif
                                                <div class="card-body">
                                                    <h6 class="card-title fw-bold">{{ $review->property->name }}</h6>
                                                    <div class="mb-2">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                        @endfor
                                                    </div>
                                                    <p class="text-muted small">{{ Str::limit($review->comment, 80) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">No hay actividad reciente</h6>
                            <p class="text-muted small">Cuando hagas reservas o interactúes con propiedades, aparecerá aquí.</p>
                            <a href="{{ route('properties.index') }}" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Explorar Propiedades
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Propiedades Destacadas -->
    @if($featuredProperties->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-star me-2 text-warning"></i>Propiedades Destacadas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($featuredProperties as $property)
                            <div class="col-md-4 mb-3">
                                <div class="card border-0 shadow-sm h-100">
                                    @if($property->images->count() > 0)
                                        <img src="{{ $property->images->first()->full_url }}" 
                                             class="card-img-top" 
                                             alt="{{ $property->name }}"
                                             style="height: 200px; object-fit: cover;">
                                    @endif
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold">{{ $property->name }}</h6>
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-map-marker-alt me-1"></i>
                                            {{ $property->city->name ?? 'Ubicación no disponible' }}
                                        </p>
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-users me-1"></i>
                                            Hasta {{ $property->capacity }} personas
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="h6 text-primary mb-0">
                                                ${{ number_format($property->price, 0) }}
                                            </span>
                                            <a href="{{ route('properties.show', $property) }}" class="btn btn-sm btn-primary">
                                                Ver Detalles
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Estilos adicionales para el dashboard -->
<style>
    .card {
        transition: all 0.3s ease;
        border-radius: 12px;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
    }
    
    .btn {
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
    }
    
    /* Estilos específicos para botones Ver Detalles */
    .btn-primary {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
        border: none !important;
        color: white !important;
        font-weight: 600 !important;
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3) !important;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #218838 0%, #1ea085 100%) !important;
        color: white !important;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4) !important;
    }
    
    .rounded-circle {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .text-primary {
        color: #667eea !important;
    }
    
    .card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .rounded-circle {
            width: 50px;
            height: 50px;
        }
        
        .fa-2x {
            font-size: 1.5em;
        }
    }
</style>
@endsection
