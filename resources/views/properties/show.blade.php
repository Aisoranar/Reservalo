@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('properties.index') }}" class="text-decoration-none">Propiedades</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $property->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <!-- Galería de imágenes -->
            @if($property->images->count() > 0)
                <div class="property-gallery mb-4">
                    <div id="propertyCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            @foreach($property->images as $index => $image)
                                <button type="button" data-bs-target="#propertyCarousel" 
                                        data-bs-slide-to="{{ $index }}" 
                                        class="{{ $index === 0 ? 'active' : '' }}"
                                        aria-label="Slide {{ $index + 1 }}"></button>
                            @endforeach
                        </div>
                        <div class="carousel-inner rounded-3 overflow-hidden shadow">
                            @foreach($property->images as $index => $image)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ $image->full_url }}" class="d-block w-100" 
                                         alt="{{ $image->alt_text }}" style="height: 500px; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                    </div>
                    
                    <!-- Miniaturas -->
                    @if($property->images->count() > 1)
                        <div class="image-thumbnails mt-3 d-flex gap-2 overflow-auto">
                            @foreach($property->images as $index => $image)
                                <img src="{{ $image->full_url }}" 
                                     class="thumbnail-img" 
                                     alt="{{ $image->alt_text }}"
                                     onclick="goToSlide({{ $index }})"
                                     style="width: 80px; height: 60px; object-fit: cover; cursor: pointer; border-radius: 8px; border: 2px solid transparent;"
                                     onmouseover="this.style.borderColor='#007bff'"
                                     onmouseout="this.style.borderColor='transparent'">
                            @endforeach
                        </div>
                    @endif
                </div>
            @else
                <div class="no-image-placeholder mb-4 rounded-3 bg-light d-flex align-items-center justify-content-center" style="height: 400px;">
                    <div class="text-center text-muted">
                        <i class="fas fa-image fa-4x mb-3"></i>
                        <p class="mb-0">No hay imágenes disponibles</p>
                    </div>
                </div>
            @endif

            <!-- Información principal -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="flex-grow-1">
                            <h1 class="h2 mb-2 text-dark">{{ $property->name }}</h1>
                            <div class="d-flex align-items-center mb-3">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <span class="text-muted">{{ $property->location }}</span>
                                @if($property->city)
                                    <span class="text-muted ms-2">• {{ $property->city->name }}, {{ $property->city->department->name }}</span>
                                @endif
                            </div>
                            
                            <!-- Rating -->
                            <div class="d-flex align-items-center mb-3">
                                @if($property->rating > 0)
                                    <div class="d-flex align-items-center me-3">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $property->rating ? 'text-warning' : 'text-muted' }} me-1"></i>
                                        @endfor
                                        <span class="fw-bold me-1">{{ number_format($property->rating, 1) }}</span>
                                        <span class="text-muted">({{ $property->review_count }} reseñas)</span>
                                    </div>
                                @else
                                    <span class="text-muted">Sin calificaciones aún</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Botón de favoritos -->
                        <div class="flex-shrink-0">
                            @auth
                                @php
                                    $isFavorite = $property->favorites->where('user_id', auth()->id())->count() > 0;
                                @endphp
                                <button class="btn {{ $isFavorite ? 'btn-danger' : 'btn-outline-danger' }} btn-lg" 
                                        onclick="toggleFavorite({{ $property->id }})" 
                                        id="favoriteBtn">
                                    <i class="fas fa-heart"></i>
                                </button>
                            @else
                                <button class="btn btn-outline-danger btn-lg" onclick="window.location.href='{{ route('login') }}'" title="Inicia sesión para agregar a favoritos">
                                    <i class="fas fa-heart"></i>
                                </button>
                            @endauth
                        </div>
                    </div>

                                         <!-- Precio destacado -->
                     <div class="price-highlight text-center p-3 bg-primary bg-opacity-10 rounded-3 mb-4">
                         <div class="h2 text-primary mb-1" id="dynamic-price">
                             @if($property->nightlyPrices && $property->nightlyPrices->where('is_active', true)->count() > 0)
                                 ${{ number_format($property->nightlyPrices->where('is_active', true)->count() > 0 ? $property->nightlyPrices->where('is_active', true)->first()->base_price : $property->price, 0) }}
                             @else
                                 ${{ number_format($property->price, 0) }}
                             @endif
                         </div>
                         <div class="text-muted mb-1 small">por noche</div>
                        
                        <!-- Información de precios especiales -->
                        @if($property->nightlyPrices && $property->nightlyPrices->count() > 0)
                            <div class="price-details mt-3">
                                @foreach($property->nightlyPrices->take(3) as $nightlyPrice)
                                    @if($nightlyPrice->is_active)
                                        <div class="small text-muted">
                                            @if($nightlyPrice->weekend_price)
                                                <i class="fas fa-calendar-week me-1"></i>Fines de semana: ${{ number_format($nightlyPrice->weekend_price, 0) }}
                                            @endif
                                            @if($nightlyPrice->holiday_price)
                                                <i class="fas fa-star me-1"></i>Festivos: ${{ number_format($nightlyPrice->holiday_price, 0) }}
                                            @endif
                                            @if($nightlyPrice->seasonal_price)
                                                <i class="fas fa-sun me-1"></i>Temporada: ${{ number_format($nightlyPrice->seasonal_price, 0) }}
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                        
                        <!-- Indicador de descuentos -->
                        <div class="discounts-indicator mt-2" id="discounts-indicator" style="display: none;">
                            <span class="badge bg-success">
                                <i class="fas fa-tag me-1"></i>Descuentos disponibles
                            </span>
                        </div>
                    </div>

                    <!-- Características principales -->
                    <div class="features-grid mb-4">
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <div class="feature-item text-center p-3 bg-light rounded-3">
                                    <i class="fas fa-home fa-2x text-primary mb-2"></i>
                                    <div class="fw-bold">{{ ucfirst($property->type) }}</div>
                                    <small class="text-muted">Tipo</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="feature-item text-center p-3 bg-light rounded-3">
                                    <i class="fas fa-users fa-2x text-primary mb-2"></i>
                                    <div class="fw-bold">{{ $property->capacity }}</div>
                                    <small class="text-muted">Personas</small>
                                </div>
                            </div>
                            @if($property->bedrooms)
                            <div class="col-6 col-md-3">
                                <div class="feature-item text-center p-3 bg-light rounded-3">
                                    <i class="fas fa-bed fa-2x text-primary mb-2"></i>
                                    <div class="fw-bold">{{ $property->bedrooms }}</div>
                                    <small class="text-muted">Habitaciones</small>
                                </div>
                            </div>
                            @endif
                            @if($property->bathrooms)
                            <div class="col-6 col-md-3">
                                <div class="feature-item text-center p-3 bg-light rounded-3">
                                    <i class="fas fa-bath fa-2x text-primary mb-2"></i>
                                    <div class="fw-bold">{{ $property->bathrooms }}</div>
                                    <small class="text-muted">Baños</small>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="description-section mb-4">
                        <h4 class="mb-3 text-dark">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Descripción
                        </h4>
                        <p class="text-muted lh-lg">{{ $property->description }}</p>
                    </div>

                    <!-- Amenities -->
                    @if($property->amenities && count($property->amenities) > 0)
                        <div class="amenities-section mb-4">
                            <h4 class="mb-3 text-dark">
                                <i class="fas fa-star text-primary me-2"></i>
                                Amenities
                            </h4>
                            <div class="row g-3">
                                @foreach($property->amenities as $amenity)
                                    <div class="col-6 col-md-4">
                                        <div class="amenity-item d-flex align-items-center p-2 bg-light rounded-3">
                                            <i class="fas fa-check text-success me-2"></i>
                                            <span class="small">{{ ucwords(str_replace('_', ' ', $amenity)) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Características especiales -->
                    @if($property->features && count($property->features) > 0)
                        <div class="features-section mb-4">
                            <h4 class="mb-3 text-dark">
                                <i class="fas fa-gem text-primary me-2"></i>
                                Características especiales
                            </h4>
                            <div class="row g-3">
                                @foreach($property->features as $feature)
                                    <div class="col-6 col-md-4">
                                        <div class="feature-item d-flex align-items-center p-2 bg-light rounded-3">
                                            <i class="fas fa-star text-warning me-2"></i>
                                            <span class="small">{{ ucwords(str_replace('_', ' ', $feature)) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Información de precios -->
                    <div class="pricing-info mb-4">
                        <h4 class="mb-3 text-dark">
                            <i class="fas fa-dollar-sign text-primary me-2"></i>
                            Información de precios
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="price-type-card text-center p-3 bg-light rounded-3">
                                    <i class="fas fa-calendar-day text-primary fa-2x mb-2"></i>
                                    <div class="fw-bold">Días normales</div>
                                    <div class="text-primary">
                                        @if($property->nightlyPrices && $property->nightlyPrices->where('is_active', true)->count() > 0)
                                            ${{ number_format($property->nightlyPrices->where('is_active', true)->first()->base_price, 0) }}
                                        @else
                                            ${{ number_format($property->price, 0) }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($property->nightlyPrices && $property->nightlyPrices->where('weekend_price', '>', 0)->where('is_active', true)->count() > 0)
                            <div class="col-md-6">
                                <div class="price-type-card text-center p-3 bg-light rounded-3">
                                    <i class="fas fa-calendar-week text-warning fa-2x mb-2"></i>
                                    <div class="fw-bold">Fines de semana</div>
                                    <div class="text-warning">${{ number_format($property->nightlyPrices->where('weekend_price', '>', 0)->where('is_active', true)->first()->weekend_price, 0) }}</div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Calendario de disponibilidad -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0 text-dark">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                Calendario de disponibilidad
                            </h5>
                            <small class="text-muted">Selecciona fechas para ver disponibilidad</small>
                        </div>
                        <div class="card-body p-3">
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>¿Cómo reservar?</strong> Haz clic en una fecha de inicio y luego en una fecha de fin en el calendario, o usa los campos de fecha del formulario.
                            </div>
                            <div id="calendar" class="availability-calendar"></div>
                            <div class="mt-3">
                                <div class="d-flex align-items-center gap-3 small">
                                    <div class="d-flex align-items-center">
                                        <div class="availability-legend bg-success me-2" style="width: 16px; height: 16px; border-radius: 3px;"></div>
                                        <span>Disponible</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="availability-legend bg-warning me-2" style="width: 16px; height: 16px; border-radius: 3px;"></div>
                                        <span>Pendiente de aprobación</span>
                                    </div>
                                    <div class="availability-legend bg-danger me-2" style="width: 16px; height: 16px; border-radius: 3px;"></div>
                                    <span>Ocupado</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Formulario de reserva -->
            @auth
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white border-0 py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-plus me-2"></i>
                            Reservar ahora
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        <form action="{{ route('reservations.store', $property) }}" method="POST" id="reservationForm" onsubmit="return validateForm()">
                            @csrf
                            
                            <!-- Fechas -->
                            <div class="mb-3">
                                <label for="start_date" class="form-label fw-bold small">Fecha de llegada</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                       min="{{ date('Y-m-d') }}" required>
                                <small class="text-muted">Debe ser hoy o una fecha futura</small>
                            </div>
 
                            <div class="mb-3">
                                <label for="end_date" class="form-label fw-bold small">Fecha de salida</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                       min="{{ date('Y-m-d') }}" required>
                                <small class="text-muted">Debe ser posterior a la fecha de llegada</small>
                            </div>

                            <!-- Noches -->
                            <div class="mb-3">
                                <label for="nights" class="form-label fw-bold small">Noches</label>
                                <input type="text" class="form-control bg-light" id="nights" readonly>
                                
                                <!-- Precio por noche -->
                                <div class="price-per-night mt-2" id="price-per-night" style="display: none;">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Precio promedio por noche: <span id="avg-night-price" class="fw-bold text-primary">$0</span>
                                    </small>
                                </div>
                            </div>

                            <!-- Precio total -->
                            <div class="mb-3">
                                <label for="total_price" class="form-label fw-bold small">Precio total</label>
                                <input type="text" class="form-control bg-light fw-bold text-primary" id="total_price" readonly>
                                
                                <!-- Desglose de precios -->
                                <div class="price-breakdown mt-2" id="price-breakdown" style="display: none;">
                                    <div class="small text-muted">
                                        <div class="d-flex justify-content-between">
                                            <span>Subtotal:</span>
                                            <span id="subtotal-amount">$0</span>
                                        </div>
                                        <div class="d-flex justify-content-between text-success" id="discounts-line" style="display: none;">
                                            <span>Descuentos:</span>
                                            <span id="discounts-amount">-$0</span>
                                        </div>
                                        <hr class="my-1">
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total:</span>
                                            <span id="final-amount">$0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Solicitudes especiales -->
                            <div class="mb-3">
                                <label for="special_requests" class="form-label fw-bold small">Solicitudes especiales</label>
                                <textarea class="form-control" id="special_requests" name="special_requests" 
                                           rows="2" placeholder="Algún requerimiento especial..."></textarea>
                            </div>
                            
                            <!-- Información de descuentos -->
                            <div id="discounts-info" class="mb-3"></div>

                            <!-- Botón de envío -->
                            <button type="submit" class="btn btn-primary w-100" id="submitBtn" disabled>
                                <i class="fas fa-calendar-check me-2"></i>
                                Enviar solicitud
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center p-3">
                        <i class="fas fa-lock fa-2x text-muted mb-2"></i>
                        <h6 class="text-dark">Inicia sesión para reservar</h6>
                        <p class="text-muted small mb-3">Necesitas una cuenta para hacer reservas</p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Iniciar Sesión</a>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary btn-sm">Registrarse</a>
                        </div>
                    </div>
                </div>
            @endauth

            <!-- Información importante -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-2">
                    <h6 class="mb-0 text-dark">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Información importante
                    </h6>
                </div>
                <div class="card-body p-3">
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2 d-flex align-items-start">
                            <i class="fas fa-clock text-primary me-2 mt-1"></i>
                            <div>
                                <strong>Check-in:</strong> {{ $property->check_in_time ?? '15:00' }}<br>
                                <strong>Check-out:</strong> {{ $property->check_out_time ?? '11:00' }}
                            </div>
                        </li>
                        <li class="mb-2 d-flex align-items-start">
                            <i class="fas fa-credit-card text-primary me-2 mt-1"></i>
                            <div>
                                <strong>Pago:</strong> Al confirmar la reserva
                            </div>
                        </li>
                        <li class="mb-2 d-flex align-items-start">
                            <i class="fas fa-undo text-primary me-2 mt-1"></i>
                            <div>
                                <strong>Cancelación:</strong> {{ $property->cancellation_policy ?? 'Gratuita hasta 24h antes' }}
                            </div>
                        </li>
                        <li class="d-flex align-items-start">
                            <i class="fas fa-shield-alt text-primary me-2 mt-1"></i>
                            <div>
                                <strong>Seguridad:</strong> Reserva segura y confirmada
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Información del anfitrión -->
            @if($property->user)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-2">
                    <h6 class="mb-0 text-dark">
                        <i class="fas fa-user text-primary me-2"></i>
                        Sobre el anfitrión
                    </h6>
                </div>
                <div class="card-body p-3 text-center">
                    <div class="avatar-placeholder mb-2">
                        <i class="fas fa-user-circle fa-2x text-muted"></i>
                    </div>
                    <h6 class="text-dark small">{{ $property->user->name }}</h6>
                    <p class="text-muted small mb-2">Miembro desde {{ $property->user->created_at->format('M Y') }}</p>
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-envelope me-1"></i>
                        Contactar
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Botón de volver arriba -->
<button id="backToTop" class="btn btn-primary position-fixed" style="bottom: 20px; right: 20px; z-index: 1000; display: none; border-radius: 50%; width: 50px; height: 50px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- Estilos personalizados -->
<style>
.property-gallery .carousel {
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
}

.thumbnail-img:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}

.price-highlight {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border: 2px solid #dee2e6;
    position: relative;
    overflow: hidden;
}

.price-highlight::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: left 0.5s;
}

.price-highlight:hover::before {
    left: 100%;
}

 .price-highlight .h2 {
     font-size: 2.5rem;
     font-weight: 700;
     text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
 }

.feature-item {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.feature-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #007bff;
}

.amenity-item, .feature-item {
    transition: all 0.3s ease;
}

.amenity-item:hover, .feature-item:hover {
    background-color: #e3f2fd !important;
    transform: translateX(5px);
}

.price-type-card {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.price-type-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #007bff;
}

 .availability-calendar {
     min-height: 300px;
 }
 
 /* Calendario compacto */
 .fc {
     font-size: 0.875rem;
 }
 
 .fc .fc-toolbar {
     padding: 0.5rem;
 }
 
 .fc .fc-toolbar-title {
     font-size: 1.1rem;
 }
 
 .fc .fc-button {
     padding: 0.25rem 0.5rem;
     font-size: 0.75rem;
 }
 
 .fc .fc-daygrid-day {
     min-height: 2.5rem;
 }
 
 .fc .fc-daygrid-day-number {
     font-size: 0.8rem;
     padding: 0.25rem;
 }
 
 .fc .fc-event {
     font-size: 0.7rem;
     padding: 0.1rem 0.2rem;
 }
 
 /* Leyenda de disponibilidad */
 .availability-legend {
     display: inline-block;
     border: 1px solid rgba(0,0,0,0.1);
 }
 
 /* Hacer el sidebar más compacto */
 .col-lg-4 .card {
     margin-bottom: 1rem !important;
 }
 
 .col-lg-4 .card-body {
     padding: 1rem !important;
 }
 
 .col-lg-4 .card-header {
     padding: 0.75rem 1rem !important;
 }
 
 /* Ajustar tamaños de formulario */
 .col-lg-4 .form-control {
     padding: 0.375rem 0.75rem;
     font-size: 0.875rem;
 }
 
 .col-lg-4 .form-label {
     font-size: 0.875rem;
     margin-bottom: 0.25rem;
 }
 
 /* Responsive para móviles */
 @media (max-width: 991px) {
     .col-lg-4 {
         margin-top: 2rem;
     }
     
     .col-lg-4 .card {
         margin-bottom: 1rem !important;
     }
 }

 /* Responsive */
 @media (max-width: 768px) {
     .price-highlight .h2 {
         font-size: 1.75rem;
     }
     
     .sticky-top {
         position: relative !important;
         top: 0 !important;
     }
     
     .col-lg-4 .card {
         margin-bottom: 1rem !important;
     }
     
     .availability-calendar {
         min-height: 250px;
     }
     
     .fc .fc-toolbar {
         flex-direction: column;
         gap: 0.5rem;
     }
     
     .fc .fc-toolbar-title {
         font-size: 1rem;
     }
 }
</style>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script cargado completamente');
    
    // Inicializar calendario
    initializeCalendar();
    
    // Cargar precios nocturnos
    loadNightlyPrices();
    
    // Configurar event listeners
    setupEventListeners();
    
    // Configurar botón de volver arriba
    setupBackToTop();
});

// Función para ir a una slide específica
function goToSlide(index) {
    const carousel = new bootstrap.Carousel(document.getElementById('propertyCarousel'));
    carousel.to(index);
}

// Inicializar calendario
function initializeCalendar() {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;
    
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        height: 350,
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'today'
        },
        buttonText: {
            today: 'Hoy'
        },
        selectable: true,
        selectConstraint: {
            start: new Date().toISOString().split('T')[0],
            end: '2100-01-01'
        },
        validRange: {
            start: new Date().toISOString().split('T')[0]
        },
        select: function(info) {
            const startDate = info.startStr;
            const endDate = info.endStr;
            
            // Validar que las fechas sean futuras considerando la hora actual
            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            const startDateObj = new Date(startDate);
            const endDateObj = new Date(endDate);
            
            // Si es hoy, verificar que sea después de la hora actual
            if (startDateObj.getTime() === today.getTime()) {
                const currentHour = now.getHours();
                const currentMinute = now.getMinutes();
                
                // Si es muy tarde en el día (después de las 18:00), no permitir reservas para hoy
                if (currentHour >= 18) {
                    showAvailabilityMessage('Es muy tarde para reservar hoy. Por favor selecciona mañana o una fecha futura.', 'danger');
                    calendar.unselect();
                    return;
                }
            }
            
            if (startDateObj < today) {
                showAvailabilityMessage('La fecha de inicio debe ser hoy o posterior', 'danger');
                calendar.unselect();
                return;
            }
            
            if (endDateObj <= startDateObj) {
                showAvailabilityMessage('La fecha de salida debe ser posterior a la fecha de llegada', 'danger');
                calendar.unselect();
                return;
            }
            
            // Actualizar formulario de reserva
            document.getElementById('start_date').value = startDate;
            document.getElementById('end_date').value = endDate;
            
            // Calcular total
            calculateTotal();
            
            // Mostrar mensaje de éxito
            showAvailabilityMessage('Fechas seleccionadas correctamente', 'success');
            
            // Limpiar selección del calendario
            calendar.unselect();
        },
        dayCellDidMount: function(arg) {
            // Personalizar celdas del calendario
            const date = arg.date;
            const now = new Date();
            const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            
            if (date < today) {
                arg.el.style.backgroundColor = '#f8f9fa';
                arg.el.style.color = '#6c757d';
                arg.el.style.cursor = 'not-allowed';
                arg.el.title = 'Fecha no disponible';
            } else if (date.getTime() === today.getTime()) {
                // Si es hoy, verificar la hora
                const currentHour = now.getHours();
                if (currentHour >= 18) {
                    arg.el.style.backgroundColor = '#f8f9fa';
                    arg.el.style.color = '#6c757d';
                    arg.el.style.cursor = 'not-allowed';
                    arg.el.title = 'Es muy tarde para reservar hoy';
                }
            }
        },
        events: function(info, successCallback, failureCallback) {
            // Cargar eventos de disponibilidad desde el servidor
            loadAvailabilityEvents(info.start, info.end, successCallback, failureCallback);
        },
        eventDidMount: function(info) {
            // Personalizar eventos
            const event = info.event;
            const element = info.el;
            
            if (event.extendedProps.status === 'pending') {
                element.style.backgroundColor = '#ffc107';
                element.style.borderColor = '#ffc107';
            } else if (event.extendedProps.status === 'approved') {
                element.style.backgroundColor = '#dc3545';
                element.style.borderColor = '#dc3545';
            }
        }
    });
    
    calendar.render();
    
    // Guardar referencia global
    window.propertyCalendar = calendar;
}

// Cargar eventos de disponibilidad (simplificada)
function loadAvailabilityEvents(start, end, successCallback, failureCallback) {
    // Por ahora, no cargar eventos para evitar errores
    // Los eventos se pueden agregar después cuando la API esté funcionando
    successCallback([]);
}

// Verificar disponibilidad de fechas (simplificada)
function checkAvailability(startDate, endDate) {
    // Por ahora, permitir todas las fechas futuras
    // La validación real se hará al enviar el formulario
    const start = new Date(startDate);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    return start >= today;
}

// Mostrar mensaje de disponibilidad
function showAvailabilityMessage(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto-remover después de 3 segundos
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 3000);
}

// Cargar precios nocturnos
function loadNightlyPrices() {
    const hasNightlyPrices = {{ $property->nightlyPrices && $property->nightlyPrices->count() > 0 ? 'true' : 'false' }};
    
    if (hasNightlyPrices) {
        const discountsIndicator = document.getElementById('discounts-indicator');
        if (discountsIndicator) {
            discountsIndicator.style.display = 'block';
        }
        
        const basePrice = {{ $property->nightlyPrices && $property->nightlyPrices->where('is_active', true)->count() > 0 ? $property->nightlyPrices->where('is_active', true)->first()->base_price : $property->price ?? 100000 }};
        const dynamicPrice = document.getElementById('dynamic-price');
        if (dynamicPrice) {
            dynamicPrice.textContent = '$' + basePrice.toLocaleString();
        }
    }
}

// Configurar event listeners
function setupEventListeners() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    console.log('Start date input:', startDateInput);
    console.log('End date input:', endDateInput);
    
    if (startDateInput && endDateInput) {
        // Actualizar atributos min basado en la hora actual
        updateDateInputsMin();
        
        startDateInput.addEventListener('change', function() {
            console.log('Start date cambiada:', startDateInput.value);
            calculateTotal();
        });
        endDateInput.addEventListener('change', function() {
            console.log('End date cambiada:', endDateInput.value);
            calculateTotal();
        });
        console.log('Event listeners agregados');
        
        // Verificar que los elementos del formulario existan
        console.log('Elemento nights:', document.getElementById('nights'));
        console.log('Elemento total_price:', document.getElementById('total_price'));
        console.log('Elemento submitBtn:', document.getElementById('submitBtn'));
        console.log('Elemento price-per-night:', document.getElementById('price-per-night'));
        console.log('Elemento avg-night-price:', document.getElementById('avg-night-price'));
    } else {
        console.error('No se encontraron los inputs de fecha');
    }
}

// Actualizar atributos min de los campos de fecha
function updateDateInputsMin() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    if (startDateInput && endDateInput) {
        const now = new Date();
        const currentHour = now.getHours();
        
        // Si es después de las 18:00, no permitir reservas para hoy
        if (currentHour >= 18) {
            const tomorrow = new Date(now);
            tomorrow.setDate(tomorrow.getDate() + 1);
            const tomorrowStr = tomorrow.toISOString().split('T')[0];
            
            startDateInput.min = tomorrowStr;
            endDateInput.min = tomorrowStr;
        } else {
            // Si es antes de las 18:00, permitir reservas para hoy
            const todayStr = now.toISOString().split('T')[0];
            startDateInput.min = todayStr;
            endDateInput.min = todayStr;
        }
    }
}

// Calcular total
function calculateTotal() {
    console.log('=== CALCULATE TOTAL EJECUTADO ===');
    console.log('Función calculateTotal llamada');
    
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    console.log('Start date input value:', startDateInput ? startDateInput.value : 'NO ENCONTRADO');
    console.log('End date input value:', endDateInput ? endDateInput.value : 'NO ENCONTRADO');
    
    if (!startDateInput || !endDateInput) {
        console.error('No se encontraron los inputs de fecha');
        return;
    }
    
    const startDate = new Date(startDateInput.value);
    const endDate = new Date(endDateInput.value);
    
    console.log('Start date:', startDate);
    console.log('End date:', endDate);
    
    // Validar que las fechas sean futuras considerando la hora actual
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    
    // Si es hoy, verificar que sea después de la hora actual
    if (startDate.getTime() === today.getTime()) {
        const currentHour = now.getHours();
        
        // Si es muy tarde en el día (después de las 18:00), no permitir reservas para hoy
        if (currentHour >= 18) {
            showAvailabilityMessage('Es muy tarde para reservar hoy. Por favor selecciona mañana o una fecha futura.', 'danger');
            resetPriceFields();
            return;
        }
    }
    
    if (startDate < today) {
        showAvailabilityMessage('La fecha de inicio debe ser hoy o posterior', 'danger');
        resetPriceFields();
        return;
    }
    
    if (endDate <= startDate) {
        showAvailabilityMessage('La fecha de salida debe ser posterior a la fecha de llegada', 'danger');
        resetPriceFields();
        return;
    }
    
    if (startDate && endDate && startDate <= endDate) {
         let nights = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
         
         // Si es el mismo día, contar como 1 noche
         if (nights === 0) {
             nights = 1;
         }
         
         console.log('Noches calculadas:', nights);
         
         // Mostrar inmediatamente el número de noches
         document.getElementById('nights').value = nights + ' noche(s)';
        
        // Obtener precio base por noche desde la página
        const basePricePerNight = {{ $property->nightlyPrices && $property->nightlyPrices->where('is_active', true)->count() > 0 ? $property->nightlyPrices->where('is_active', true)->first()->base_price : $property->price }};
        console.log('Precio base por noche:', basePricePerNight);
        
        // Mostrar precio por noche
        const avgNightPriceEl = document.getElementById('avg-night-price');
        if (avgNightPriceEl) {
            avgNightPriceEl.textContent = '$' + basePricePerNight.toLocaleString();
        }
        
        // Mostrar el contenedor de precio por noche
        const pricePerNightEl = document.getElementById('price-per-night');
        if (pricePerNightEl) {
            pricePerNightEl.style.display = 'block';
        }
        
        // Calcular precio total básico
        const totalPrice = nights * basePricePerNight;
        document.getElementById('total_price').value = '$' + totalPrice.toLocaleString();
        
        // Habilitar botón
        document.getElementById('submitBtn').disabled = false;
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-calendar-check me-2"></i>Enviar solicitud';
        
        console.log('Precio total calculado:', totalPrice);
        console.log('Botón habilitado');
        
        // Intentar usar el servicio de precios para calcular el total con descuentos
        fetch('/admin/pricing/calculate-price', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                property_id: {{ $property->id }},
                check_in: document.getElementById('start_date').value,
                check_out: document.getElementById('end_date').value
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Respuesta de la API:', data);
            if (data.success) {
                document.getElementById('total_price').value = '$' + data.data.total_price.toLocaleString();
                
                // Mostrar desglose de precios
                showPriceBreakdown(data.data);
                
                // Mostrar descuentos aplicados si los hay
                if (data.data.applied_discounts && data.data.applied_discounts.length > 0) {
                    showDiscountsInfo(data.data.applied_discounts);
                }
                
                // Actualizar precio dinámico en el header
                updateDynamicPrice(data.data);
                
                // Mostrar descuentos disponibles
                showAvailableDiscounts(data.data);
            }
        })
        .catch(error => {
            console.error('Error al calcular precio con API:', error);
            // Ya tenemos el precio básico calculado, no es necesario hacer nada más
        });
    } else {
        console.log('Fechas inválidas, reseteando campos');
        resetPriceFields();
    }
}

// Función de fallback para cálculo básico
function fallbackCalculation(nights) {
    // Usar el precio real de la migración si está disponible
    const pricePerNight = {{ $property->nightlyPrices && $property->nightlyPrices->where('is_active', true)->count() > 0 ? $property->nightlyPrices->where('is_active', true)->first()->base_price : $property->price }};
    
    // Si es el mismo día, calcular como 1 noche
    const actualNights = nights === 0 ? 1 : nights;
    const totalPrice = actualNights * pricePerNight;
    
    document.getElementById('nights').value = actualNights + ' noche(s)';
    document.getElementById('total_price').value = '$' + totalPrice.toLocaleString();
    document.getElementById('submitBtn').disabled = false;
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-calendar-check me-2"></i>Enviar solicitud';
    
    // Mostrar precio por noche
    const pricePerNightEl = document.getElementById('price-per-night');
    const avgNightPriceEl = document.getElementById('avg-night-price');
    if (pricePerNightEl && avgNightPriceEl) {
        avgNightPriceEl.textContent = '$' + pricePerNight.toLocaleString();
        pricePerNightEl.style.display = 'block';
    }
    
    document.getElementById('price-breakdown').style.display = 'none';
}

// Resetear campos de precio
function resetPriceFields() {
    document.getElementById('nights').value = '';
    document.getElementById('total_price').value = '';
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-calendar-check me-2"></i>Enviar solicitud';
    document.getElementById('price-breakdown').style.display = 'none';
    document.getElementById('discounts-info').innerHTML = '';
    
    const pricePerNightEl = document.getElementById('price-per-night');
    if (pricePerNightEl) {
        pricePerNightEl.style.display = 'none';
    }
}

// Mostrar desglose de precios
function showPriceBreakdown(pricingData) {
    const breakdown = document.getElementById('price-breakdown');
    if (breakdown && pricingData.subtotal) {
        document.getElementById('subtotal-amount').textContent = '$' + pricingData.subtotal.toLocaleString();
        document.getElementById('final-amount').textContent = '$' + pricingData.total_price.toLocaleString();
        
        if (pricingData.applied_discounts && pricingData.applied_discounts.length > 0) {
            const totalDiscounts = pricingData.applied_discounts.reduce((sum, discount) => sum + discount.amount, 0);
            document.getElementById('discounts-amount').textContent = '-$' + totalDiscounts.toLocaleString();
            document.getElementById('discounts-line').style.display = 'flex';
        }
        
        breakdown.style.display = 'block';
    }
}

// Mostrar información de descuentos
function showDiscountsInfo(discounts) {
    const container = document.getElementById('discounts-info');
    if (container && discounts.length > 0) {
        let html = '<div class="alert alert-success"><h6><i class="fas fa-tag me-2"></i>Descuentos aplicados:</h6>';
        discounts.forEach(discount => {
            html += `<div class="small">• ${discount.name}: -$${discount.amount.toLocaleString()}</div>`;
        });
        html += '</div>';
        container.innerHTML = html;
    }
}

// Actualizar precio dinámico
function updateDynamicPrice(pricingData) {
    const dynamicPrice = document.getElementById('dynamic-price');
    if (dynamicPrice && pricingData.nights && pricingData.total_price) {
        const avgPrice = pricingData.total_price / pricingData.nights;
        dynamicPrice.textContent = '$' + Math.round(avgPrice).toLocaleString();
        
        const basePriceCard = document.querySelector('.price-type-card .text-primary');
        if (basePriceCard) {
            basePriceCard.textContent = '$' + Math.round(avgPrice).toLocaleString();
        }
    }
}

// Mostrar descuentos disponibles
function showAvailableDiscounts(pricingData) {
    if (pricingData.available_discounts && pricingData.available_discounts.length > 0) {
        const container = document.getElementById('discounts-info');
        if (container) {
            let html = '<div class="alert alert-info"><h6><i class="fas fa-info-circle me-2"></i>Descuentos disponibles:</h6>';
            pricingData.available_discounts.forEach(discount => {
                const discountAmount = discount.calculateDiscountAmount ? discount.calculateDiscountAmount(pricingData.subtotal) : 0;
                html += `<div class="small">• ${discount.name}: ${discount.type === 'percentage' ? discount.value + '%' : '$' + discount.value.toLocaleString()}</div>`;
            });
            html += '</div>';
            container.innerHTML = html;
        }
    }
}

// Cargar descuentos disponibles
function loadAvailableDiscounts() {
    fetch('/admin/pricing/get-available-discounts', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            nights: 1,
            total_amount: {{ $property->nightlyPrices && $property->nightlyPrices->where('is_active', true)->count() > 0 ? $property->nightlyPrices->where('is_active', true)->first()->base_price : $property->price ?? 100000 }},
            check_in: new Date().toISOString().split('T')[0]
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAvailableDiscounts(data.data);
        }
    })
    .catch(error => {
        console.error('Error al cargar descuentos:', error);
    });
}

// Toggle favorito
function toggleFavorite(propertyId) {
    fetch(`/favoritos/${propertyId}/toggle`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const btn = document.getElementById('favoriteBtn');
            if (data.isFavorite) {
                btn.classList.remove('btn-outline-danger');
                btn.classList.add('btn-danger');
            } else {
                btn.classList.remove('btn-danger');
                btn.classList.add('btn-outline-danger');
            }
        }
    })
    .catch(error => {
        console.error('Error al toggle favorito:', error);
    });
}

// Validar formulario antes de enviar
function validateForm() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    if (!startDateInput.value || !endDateInput.value) {
        showAvailabilityMessage('Por favor selecciona las fechas de llegada y salida', 'danger');
        return false;
    }
    
    const startDate = new Date(startDateInput.value);
    const endDate = new Date(endDateInput.value);
    const now = new Date();
    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
    
    // Si es hoy, verificar que sea después de la hora actual
    if (startDate.getTime() === today.getTime()) {
        const currentHour = now.getHours();
        
        // Si es muy tarde en el día (después de las 18:00), no permitir reservas para hoy
        if (currentHour >= 18) {
            showAvailabilityMessage('Es muy tarde para reservar hoy. Por favor selecciona mañana o una fecha futura.', 'danger');
            return false;
        }
    }
    
    if (startDate < today) {
        showAvailabilityMessage('La fecha de inicio debe ser hoy o posterior', 'danger');
        return false;
    }
    
    if (endDate <= startDate) {
        showAvailabilityMessage('La fecha de salida debe ser posterior a la fecha de llegada', 'danger');
        return false;
    }
    
    return true;
}

// Configurar botón de volver arriba
function setupBackToTop() {
    const backToTopBtn = document.getElementById('backToTop');
    if (!backToTopBtn) return;
    
    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            backToTopBtn.style.display = 'block';
        } else {
            backToTopBtn.style.display = 'none';
        }
    });
    
    backToTopBtn.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}
</script>
@endsection
