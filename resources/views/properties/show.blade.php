@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <!-- Hero Section con imagen de fondo -->
    <div class="hero-section position-relative" style="height: 60vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="hero-overlay position-absolute w-100 h-100" style="background: rgba(0,0,0,0.4);"></div>
        
        <!-- Breadcrumb flotante -->
        <div class="position-absolute top-0 start-0 p-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-white bg-opacity-90 rounded-pill px-3 py-2 shadow-sm">
                    <li class="breadcrumb-item">
                        <a href="{{ route('properties.index') }}" class="text-decoration-none text-primary">
                            <i class="fas fa-arrow-left me-1"></i>Propiedades
                        </a>
                    </li>
            <li class="breadcrumb-item active" aria-current="page">{{ $property->name }}</li>
        </ol>
    </nav>
        </div>

        <!-- Contenido del hero -->
        <div class="position-absolute bottom-0 start-0 w-100 p-4">
            <div class="container">
                <div class="row align-items-end">
                    <div class="col-lg-8">
                        <h1 class="display-4 text-white fw-bold mb-3 text-shadow">{{ $property->name }}</h1>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-map-marker-alt text-warning me-2 fs-5"></i>
                            <span class="text-white fs-5">{{ $property->location }}</span>
                            @if($property->city)
                                <span class="text-white-50 ms-2 fs-5">• {{ $property->city->name }}, {{ $property->city->department->name }}</span>
                            @endif
                        </div>
                        
                        <!-- Rating mejorado -->
                        @if($property->rating > 0)
                            <div class="d-flex align-items-center mb-3">
                                <div class="rating-stars me-3">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $property->rating ? 'text-warning' : 'text-white-50' }} me-1 fs-5"></i>
                                    @endfor
                                </div>
                                <span class="text-white fw-bold fs-5 me-2">{{ number_format($property->rating, 1) }}</span>
                                <span class="text-white-50 fs-5">({{ $property->review_count }} reseñas)</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="col-lg-4 text-end">
                        <div class="price-card bg-white bg-opacity-95 rounded-4 p-4 shadow-lg">
                            <div class="price-display">
                                <span class="h2 text-primary fw-bold">${{ number_format($effectivePrice, 0, ',', '.') }}</span>
                                <span class="text-muted">/noche</span>
                            </div>
                            <div class="d-flex align-items-center justify-content-center mt-3">
                                @auth
                                    <button class="btn btn-outline-primary btn-sm me-2" id="favoriteBtn">
                                        <i class="far fa-heart me-1"></i>Favorito
                                    </button>
                                @endauth
                                <button class="btn btn-success btn-sm fw-bold shadow-sm" onclick="shareProperty()" title="Compartir esta propiedad">
                                    <i class="fas fa-share me-1"></i>Compartir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="container py-5">
    <div class="row">
        <div class="col-lg-8">
                <!-- Galería de imágenes mejorada -->
            @if($property->images->count() > 0)
                    <div class="property-gallery mb-5">
                        <div class="gallery-container position-relative">
                    <div id="propertyCarousel" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-inner rounded-4 overflow-hidden shadow-lg">
                            @foreach($property->images as $index => $image)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                            <div class="image-container position-relative">
                                                <img src="{{ $image->full_url }}" class="d-block w-100 gallery-image" 
                                                     alt="{{ $image->alt_text }}">
                                                <div class="image-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                                    <div class="image-actions">
                                                        <button class="btn btn-light btn-lg rounded-circle me-2" onclick="openImageModal('{{ $image->full_url }}')">
                                                            <i class="fas fa-expand"></i>
                                                        </button>
                                                        <button class="btn btn-light btn-lg rounded-circle">
                                                            <i class="fas fa-download"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                </div>
                            @endforeach
                        </div>
                                
                                <!-- Controles personalizados -->
                                <button class="carousel-control-prev custom-control" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                                    <i class="fas fa-chevron-left"></i>
                        </button>
                                <button class="carousel-control-next custom-control" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                                    <i class="fas fa-chevron-right"></i>
                        </button>
                    
                                <!-- Indicadores personalizados -->
                                <div class="carousel-indicators custom-indicators">
                            @foreach($property->images as $index => $image)
                                        <button type="button" data-bs-target="#propertyCarousel" 
                                                data-bs-slide-to="{{ $index }}" 
                                                class="{{ $index === 0 ? 'active' : '' }}"
                                                aria-label="Slide {{ $index + 1 }}">
                                            <img src="{{ $image->full_url }}" alt="Thumbnail {{ $index + 1 }}">
                                        </button>
                            @endforeach
                        </div>
                            </div>
                        </div>
                </div>
            @else
                    <div class="no-image-placeholder mb-5 rounded-4 bg-gradient d-flex align-items-center justify-content-center" 
                         style="height: 500px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                    <div class="text-center text-muted">
                            <i class="fas fa-image fa-5x mb-4 text-primary opacity-50"></i>
                            <h4 class="mb-2">No hay imágenes disponibles</h4>
                            <p class="mb-0">Próximamente se agregarán fotos de esta propiedad</p>
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
                             ${{ number_format($effectivePrice, 0, ',', '.') }}
                         </div>
                         <div class="text-muted mb-1 small">por noche</div>
                         
                         @if($activeGlobalPricing)
                             <div class="price-info mt-2">
                                 <small class="text-success">
                                     <i class="fas fa-info-circle me-1"></i>
                                     Precio según: {{ $activeGlobalPricing->name }}
                                 </small>
                             </div>
                         @endif
                        
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

                        </div>
            </div>
        </div>


        <div class="col-lg-4">
                    <!-- Calendario de disponibilidad -->
                    <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient text-white border-0 py-2" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h6 class="mb-0 text-white text-center">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Disponibilidad
                    </h6>
                        </div>
                <div class="card-body p-2">
                    <!-- Calendario Compacto -->
                    <div class="mini-calendar-container">
                        <div class="mini-calendar-header">
                            <button type="button" class="mini-calendar-nav-btn" id="prevMonth">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <h6 class="mini-calendar-month-year" id="currentMonthYear">Sept 2025</h6>
                            <button type="button" class="mini-calendar-nav-btn" id="nextMonth">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                            </div>
                        <div class="mini-calendar-weekdays">
                            <div class="mini-weekday">L</div>
                            <div class="mini-weekday">M</div>
                            <div class="mini-weekday">X</div>
                            <div class="mini-weekday">J</div>
                            <div class="mini-weekday">V</div>
                            <div class="mini-weekday">S</div>
                            <div class="mini-weekday">D</div>
                                    </div>
                        <div class="mini-calendar-days" id="calendarDays">
                            <!-- Los días se generarán dinámicamente -->
                                    </div>
                        
                        <!-- Indicador de fechas ocupadas -->
                        <div class="mini-occupied-indicator" id="occupiedIndicator" style="display: none;">
                            <i class="fas fa-circle"></i>
                            <span>Cargando...</span>
                                </div>
                            </div>
                    
                    <!-- Fechas seleccionadas compactas -->
                    <div class="mini-selected-dates mt-3">
                        <div class="mini-date-input-group mb-2">
                            <label class="form-label small fw-bold">Entrada</label>
                            <input type="text" class="form-control form-control-sm" id="selectedStartDate" 
                                   placeholder="Selecciona fecha" readonly>
                        </div>
                        
                        <div class="mini-date-input-group mb-2">
                            <label class="form-label small fw-bold">Salida</label>
                            <input type="text" class="form-control form-control-sm" id="selectedEndDate" 
                                   placeholder="Selecciona fecha" readonly>
                    </div>
                        
                        <div class="d-grid gap-1 mb-2">
                            <button type="button" class="btn btn-outline-primary btn-sm" id="clearDates">
                                <i class="fas fa-times me-1"></i>Limpiar
                            </button>
                        </div>
                        
                        <!-- Estado de selección -->
                        <div class="mini-selection-status" id="selectionStatus" style="display: none;">
                            <div class="alert alert-info py-2 text-center">
                                <small id="statusText">Selecciona fecha de salida</small>
                </div>
            </div>
        </div>

                    <!-- Leyenda compacta -->
                    <div class="mini-legend mt-2">
                        <div class="d-flex justify-content-between small">
                            <div class="d-flex align-items-center">
                                <div class="mini-legend-item bg-success me-1"></div>
                                <span>Libre</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="mini-legend-item bg-danger me-1"></div>
                                <span>Ocupado</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información importante -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white border-0 py-3">
                        <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Información importante
                        </h5>
                    </div>
                    <div class="card-body p-3">
                            <div class="mb-3">
                        <h6 class="text-primary mb-2">
                            <i class="fas fa-clock me-2"></i>
                            Check-in: 15:00
                        </h6>
                        <h6 class="text-success mb-2">
                            <i class="fas fa-clock me-2"></i>
                            Check-out: 11:00
                        </h6>
                            </div>
 
                            <div class="mb-3">
                        <h6 class="text-info mb-2">
                            <i class="fas fa-credit-card me-2"></i>
                            Pago: Al confirmar la reserva
                        </h6>
                            </div>

                            <div class="mb-3">
                        <h6 class="text-warning mb-2">
                            <i class="fas fa-calendar-times me-2"></i>
                            Cancelación: Cancelación gratuita hasta 7 días antes del check-in.
                        </h6>
                            </div>

                            <div class="mb-3">
                        <h6 class="text-success mb-2">
                            <i class="fas fa-shield-alt me-2"></i>
                            Seguridad: Reserva segura y confirmada
                        </h6>
                                        </div>
                                </div>
                            </div>

            <!-- Hidden form inputs for dates - Always available -->
            <input type="hidden" id="start_date" name="start_date" value="">
            <input type="hidden" id="end_date" name="end_date" value="">

            <!-- Botón de reserva -->
            @auth
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center p-4">
                        <h5 class="text-primary mb-3" id="reservationTitle">
                                <i class="fas fa-calendar-check me-2"></i>
                            ¿Listo para reservar?
                        </h5>
                        <p class="text-muted mb-4" id="reservationSubtitle">Selecciona las fechas en el calendario y completa tu reserva</p>
                        
                        <!-- Precio dinámico -->
                        <div id="dynamicPrice" class="alert alert-success mb-4" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong id="dynamicNights">0 noches</strong>
                                    <div class="small text-muted" id="dynamicDates">Selecciona fechas</div>
                                </div>
                                <div class="text-end">
                                    <div class="h4 mb-0 text-success" id="dynamicTotalPrice">$0</div>
                                    <div class="small text-muted">Total</div>
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-success btn-lg w-100 fw-bold shadow-lg" id="reserveBtn" disabled 
                                style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); 
                                       border: none; 
                                       color: white; 
                                       text-shadow: 0 1px 2px rgba(0,0,0,0.3);
                                       transition: all 0.3s ease;">
                            <i class="fas fa-calendar-check me-2"></i>
                            <span class="fw-bold">Reservar Ahora</span>
                            </button>
                        <small class="text-muted d-block mt-2 fw-semibold">
                            <i class="fas fa-info-circle me-1"></i>Las fechas se sincronizarán automáticamente
                        </small>
                    </div>
                </div>
            @else
                <div class="card border-0 shadow-lg mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-center p-4">
                        <i class="fas fa-lock fa-3x text-white mb-3"></i>
                        <h5 class="text-white fw-bold mb-2">Inicia sesión para reservar</h5>
                        <p class="text-white-50 mb-4">Necesitas una cuenta para hacer reservas</p>
                        <!-- Información de precio seleccionado -->
                        <div id="selectedPriceInfo" class="alert alert-info mb-3" style="display: none;">
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="fw-bold mb-3">
                                        <i class="fas fa-calculator me-2"></i>El precio:
                                    </h6>
                                </div>
                            </div>
                            
                            <!-- Fechas seleccionadas -->
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="text-center">
                                        <div class="small text-muted">Check-in</div>
                                        <div class="fw-bold" id="selectedCheckIn">-</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <div class="small text-muted">Check-out</div>
                                        <div class="fw-bold" id="selectedCheckOut">-</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Desglose de precio -->
                            <div class="price-breakdown">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted">Precio por noche:</span>
                                    <span class="small" id="selectedPricePerNight">$0</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small text-muted">Noches:</span>
                                    <span class="small" id="selectedNights">0</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">Total:</span>
                                    <span class="h5 mb-0 text-primary" id="selectedTotalPrice">$0</span>
                                </div>
                            </div>
                            
                            <!-- Información adicional -->
                            <div class="mt-3">
                                <div class="small text-muted text-center">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Precio calculado automáticamente
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-3">
                            <a href="#" id="loginWithDates" class="btn btn-light btn-lg fw-bold text-dark shadow-sm">
                                <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                            </a>
                            <a href="#" id="registerWithDates" class="btn btn-outline-light btn-lg fw-bold shadow-sm">
                                <i class="fas fa-user-plus me-2"></i>Registrarse
                            </a>
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
/* Estilos para el nuevo diseño dinámico */

/* Hero Section */
.hero-section {
    background-attachment: fixed;
    background-size: cover;
    background-position: center;
}

.text-shadow {
    text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
}

.price-card {
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
}

/* Galería mejorada */
.gallery-container {
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.gallery-image {
    height: 500px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.image-container:hover .gallery-image {
    transform: scale(1.05);
}

.image-overlay {
    background: rgba(0,0,0,0.3);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.image-container:hover .image-overlay {
    opacity: 1;
}

.image-actions {
    transform: translateY(20px);
    transition: transform 0.3s ease;
}

.image-container:hover .image-actions {
    transform: translateY(0);
}

/* Controles personalizados */
.custom-control {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.9);
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: #333;
    transition: all 0.3s ease;
    z-index: 10;
}

.custom-control:hover {
    background: white;
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.carousel-control-prev.custom-control {
    left: 20px;
}

.carousel-control-next.custom-control {
    right: 20px;
}

/* Indicadores personalizados */
.custom-indicators {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 10px;
    z-index: 10;
}

.custom-indicators button {
    width: 60px;
    height: 40px;
    border: none;
    border-radius: 8px;
    overflow: hidden;
    opacity: 0.6;
    transition: all 0.3s ease;
    padding: 0;
}

.custom-indicators button.active,
.custom-indicators button:hover {
    opacity: 1;
    transform: scale(1.1);
}

.custom-indicators button img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Cards de información */
.info-card .card-header {
    border: none;
}

.feature-item {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.feature-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border-color: #e9ecef;
}

.service-item {
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.service-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border-color: #e9ecef;
}

/* Calendario mejorado */
.mini-calendar-container {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

/* Botones mejorados */
.btn {
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Animaciones */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-section {
        height: 50vh;
    }
    
    .display-4 {
        font-size: 2rem;
    }
    
    .custom-control {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .custom-indicators button {
        width: 40px;
        height: 30px;
    }
    
    .gallery-image {
        height: 300px;
    }
}

/* Efectos de hover mejorados */
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
}

/* Estilos para el desglose de precio */
.price-breakdown {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 15px;
    margin: 10px 0;
}

.price-breakdown hr {
    border-color: rgba(255, 255, 255, 0.3);
    margin: 8px 0;
}

#selectedPriceInfo {
    background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
    border: 1px solid #90caf9;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

#selectedPriceInfo h6 {
    color: #1976d2;
    margin-bottom: 15px;
}

#selectedCheckIn, #selectedCheckOut {
    color: #1976d2;
    font-size: 0.9rem;
}

#selectedPricePerNight, #selectedNights {
    color: #424242;
    font-weight: 500;
}

#selectedTotalPrice {
    color: #1976d2;
    font-weight: 700;
}

/* Estilos para la sección dinámica de precio */
#dynamicPrice {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border: 2px solid #28a745;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    animation: slideInDown 0.3s ease-out;
    margin: 15px 0;
    padding: 20px;
    position: relative;
    z-index: 10;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

#dynamicNights {
    color: #155724;
    font-size: 1.1rem;
}

#dynamicDates {
    color: #6c757d;
    font-size: 0.9rem;
}

#dynamicTotalPrice {
    color: #155724;
    font-weight: 700;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

/* Gradientes personalizados */
.bg-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ff6b6b 0%, #ffa726 100%);
}

/* Estilos existentes del calendario */
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

/* Calendario Compacto */
.mini-calendar-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.2);
    color: white;
    margin-bottom: 0;
    position: relative;
    overflow: hidden;
}

.mini-calendar-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
    pointer-events: none;
}

.mini-calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    position: relative;
    z-index: 1;
}

.mini-calendar-nav-btn {
    background: rgba(255,255,255,0.25);
    border: none;
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    font-size: 0.8rem;
}

.mini-calendar-nav-btn:hover {
    background: rgba(255,255,255,0.4);
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.mini-calendar-month-year {
    margin: 0;
    font-weight: 700;
    font-size: 1rem;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.mini-calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
    margin-bottom: 8px;
    position: relative;
    z-index: 1;
}

.mini-weekday {
    text-align: center;
    font-weight: 700;
    font-size: 0.7rem;
    opacity: 0.9;
    padding: 4px 0;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.mini-calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 4px;
    position: relative;
    z-index: 1;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    cursor: pointer;
     font-size: 0.75rem;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    background: rgba(78, 205, 196, 0.3);
    color: white;
    border: 1px dashed #4ecdc4;
    backdrop-filter: blur(10px);
    min-height: 28px;
}

.calendar-day.selectable {
    animation: pulse-glow 2s infinite;
}

.calendar-day:hover {
    background: rgba(78, 205, 196, 0.5);
    transform: scale(1.05);
    box-shadow: 0 0 0 4px rgba(78, 205, 196, 0.3);
}

.calendar-day.other-month {
    opacity: 0.4;
    cursor: not-allowed;
    background: rgba(255,255,255,0.05);
}

.calendar-day.disabled {
    opacity: 0.4;
    cursor: not-allowed;
    background: rgba(255,255,255,0.05);
}

.calendar-day.selected {
    background: linear-gradient(45deg, #28a745, #20c997);
    color: white;
    font-weight: 800;
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
    border: 2px solid rgba(255,255,255,0.8);
    transform: scale(1.05);
}

.calendar-day.occupied {
    background: linear-gradient(45deg, #ff6b6b, #ff8e8e) !important;
    color: #ffffff !important;
    cursor: not-allowed !important;
    opacity: 0.9;
    font-weight: 700;
    border: 2px solid #ff4757 !important;
    box-shadow: 0 2px 8px rgba(255, 71, 87, 0.3);
    position: relative;
    animation: none;
}

.calendar-day.occupied:hover {
    transform: none !important;
    background: linear-gradient(45deg, #ff6b6b, #ff8e8e) !important;
    box-shadow: 0 4px 12px rgba(255, 71, 87, 0.5);
}

.calendar-day.occupied::before {
    content: '';
    position: absolute;
    top: 2px;
    right: 2px;
    width: 6px;
    height: 6px;
    background: #ffffff;
    border-radius: 50%;
    animation: pulse-occupied 1.5s infinite;
    box-shadow: 0 0 0 2px #ff4757;
}

.calendar-day.today {
    background: linear-gradient(45deg, #ff6b6b, #ffa726);
    box-shadow: 0 4px 15px rgba(255,107,107,0.4);
    font-weight: 700;
}

.calendar-day.selected-start {
    background: linear-gradient(45deg, #4ecdc4, #44a08d);
    box-shadow: 0 4px 15px rgba(78,205,196,0.4);
    font-weight: 700;
}

.calendar-day.selected-end {
    background: linear-gradient(45deg, #667eea, #764ba2);
    box-shadow: 0 4px 15px rgba(102,126,234,0.4);
    font-weight: 700;
}

.calendar-day.in-range {
    background: rgba(78, 205, 196, 0.2);
    color: white;
    font-weight: 600;
    border: 1px dashed #4ecdc4;
    animation: none;
}

.calendar-day.blocked-range {
    background: linear-gradient(45deg, #ffa726, #ffb74d) !important;
    color: #ffffff !important;
    cursor: not-allowed !important;
    opacity: 0.7;
    font-weight: 600;
    border: 2px dashed #ff9800 !important;
    position: relative;
}

.calendar-day.blocked-range:hover {
    transform: none !important;
    background: linear-gradient(45deg, #ffa726, #ffb74d) !important;
    box-shadow: 0 2px 8px rgba(255, 152, 0, 0.3);
}

.calendar-day.blocked-range::before {
    content: '⚠';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 10px;
    font-weight: bold;
    color: #ffffff;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

@keyframes pulse-glow {
    0% { 
        box-shadow: 0 0 0 0 rgba(78, 205, 196, 0.7);
        transform: scale(1);
    }
    50% { 
        box-shadow: 0 0 0 4px rgba(78, 205, 196, 0.3);
        transform: scale(1.02);
    }
    100% { 
        box-shadow: 0 0 0 0 rgba(78, 205, 196, 0.7);
        transform: scale(1);
    }
}

@keyframes pulse-occupied {
    0% { 
        opacity: 1;
        transform: scale(1);
    }
    50% { 
        opacity: 0.7;
        transform: scale(1.2);
    }
    100% { 
        opacity: 1;
        transform: scale(1);
    }
}

.mini-occupied-indicator {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 8px;
    font-size: 0.7rem;
    opacity: 0.9;
    position: relative;
    z-index: 1;
}

.mini-occupied-indicator i {
    animation: pulse-occupied 2s infinite;
}

@keyframes pulse-occupied {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Panel de fechas seleccionadas compacto */
.mini-selected-dates {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 12px;
    border: 1px solid #dee2e6;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.mini-date-input-group {
    margin-bottom: 8px;
}

.mini-date-input-group .form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 4px;
     font-size: 0.8rem;
}

.mini-date-input-group .form-control {
    border-color: #dee2e6;
    background: white;
    font-weight: 600;
    font-size: 0.8rem;
    padding: 0.375rem 0.5rem;
}

.mini-date-input-group .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.15rem rgba(102, 126, 234, 0.25);
}

.mini-selection-status {
    margin-top: 8px;
}

.mini-selection-status .alert {
    margin-bottom: 0;
    font-size: 0.75rem;
    border-radius: 6px;
    border: none;
    background: #e3f2fd;
    padding: 0.5rem;
}

/* Leyenda compacta */
.mini-legend {
    margin-top: 8px;
}

.mini-legend-item {
    width: 12px;
    height: 12px;
    border-radius: 2px;
    display: inline-block;
}

/* Responsive para calendario compacto */
@media (max-width: 768px) {
    .mini-calendar-container {
        padding: 12px;
    }
    
    .calendar-day {
     font-size: 0.7rem;
        min-height: 24px;
    }
    
    .mini-calendar-weekdays {
        gap: 2px;
        margin-bottom: 6px;
    }
    
    .mini-calendar-days {
        gap: 2px;
    }
    
    .mini-selected-dates {
        padding: 10px;
    }
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

/* Estilos para el botón de reserva */
#reserveBtn {
    position: relative;
    overflow: hidden;
}

#reserveBtn:not(:disabled):hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4) !important;
    background: linear-gradient(135deg, #218838 0%, #1e7e34 100%) !important;
}

#reserveBtn:not(:disabled):active {
    transform: translateY(0);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3) !important;
}

#reserveBtn:disabled {
    background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%) !important;
    color: #adb5bd !important;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
}

#reserveBtn:disabled:hover {
    transform: none !important;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
 }
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script cargado completamente');
    
    // Variables del calendario dinámico
    let currentDate = new Date();
    let selectedStartDate = null;
    let selectedEndDate = null;
    let isSelectingEndDate = false;
    let occupiedDates = [];
    
    // Inicializar calendario dinámico
    initializeDynamicCalendar();
    
    // Configurar botón de reserva
    const reserveBtn = document.getElementById('reserveBtn');
    if (reserveBtn) {
        reserveBtn.addEventListener('click', function() {
            console.log('=== BOTÓN RESERVA CLICKEADO ===');
            console.log('selectedStartDate:', selectedStartDate);
            console.log('selectedEndDate:', selectedEndDate);
            console.log('selectedStartDate type:', typeof selectedStartDate);
            console.log('selectedEndDate type:', typeof selectedEndDate);
            
            if (selectedStartDate && selectedEndDate) {
                // Crear reserva directamente
                createReservation();
            } else {
                showAvailabilityMessage('Por favor selecciona las fechas de tu reserva', 'warning');
            }
        });
    }
    
    // Cargar precios nocturnos
    loadNightlyPrices();
    
    // Configurar botón de volver arriba
    setupBackToTop();
    
    // Configurar botones de acción del calendario
    setupCalendarActions();
    
    // Configurar navegación del calendario
    function setupCalendarNavigation() {
        const prevBtn = document.getElementById('prevMonth');
        const nextBtn = document.getElementById('nextMonth');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() - 1);
                renderCalendar();
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                currentDate.setMonth(currentDate.getMonth() + 1);
                renderCalendar();
            });
        }
    }
    
    // Llamar a la configuración de navegación
    setupCalendarNavigation();
    
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
        
        // Crear fechas considerando la zona horaria local
        const startDate = createLocalDate(startDateInput.value);
        const endDate = createLocalDate(endDateInput.value);
        
        console.log('Start date:', startDate);
        console.log('End date:', endDate);
        
        // Validar que las fechas sean futuras considerando la hora actual
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        
        console.log('Today (local):', today);
        console.log('Start date (local):', startDate);
        console.log('Comparison - startDate >= today:', startDate >= today);
        
        // Si es hoy, verificar que sea después de la hora actual
        if (startDate.getTime() === today.getTime()) {
            const currentHour = now.getHours();
            
            // Si es muy tarde en el día (después de las 22:00), no permitir reservas para hoy
            if (currentHour >= 22) {
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
             
            // Mostrar inmediatamente el número de noches (solo si el elemento existe)
            const nightsEl = document.getElementById('nights');
            if (nightsEl) {
                nightsEl.value = nights + ' noche(s)';
            }
            
            // Obtener precio base por noche desde la página
            const basePricePerNight = {{ $property->price }};
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
            const totalPriceEl = document.getElementById('total_price');
            if (totalPriceEl) {
                totalPriceEl.value = '$' + totalPrice.toLocaleString();
            }
            
            // Habilitar botón (solo si existe)
            const submitBtnEl = document.getElementById('submitBtn');
            if (submitBtnEl) {
                submitBtnEl.disabled = false;
                submitBtnEl.innerHTML = '<i class="fas fa-calendar-check me-2"></i>Enviar solicitud';
            }
            
            console.log('Precio total calculado:', totalPrice);
            console.log('Botón habilitado');
            
            // Mostrar información de precio para usuarios no autenticados
            if (!document.getElementById('submitBtn')) {
                updatePriceInfoForGuests(nights, totalPrice, selectedStartDate, selectedEndDate);
            } else {
                // Para usuarios autenticados, la sección dinámica ya se actualiza en updateReservationForm
                console.log('Usuario autenticado - sección dinámica de precio ya actualizada');
            }
            
            // Intentar usar el servicio de precios para calcular el total con descuentos
            console.log('Intentando llamar a la API de precios...');
            fetch('/api/properties/{{ $property->id }}/calculate-price', {
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
            .then(response => {
                console.log('Respuesta de la API recibida:', response);
                console.log('Status:', response.status);
                console.log('Status Text:', response.statusText);
                console.log('Headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                return response.text().then(text => {
                    console.log('Respuesta como texto:', text);
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error('Error al parsear JSON:', e);
                        console.error('Texto recibido:', text);
                        throw new Error('La respuesta no es JSON válido');
                    }
                });
            })
            .then(data => {
                console.log('Datos parseados de la API:', data);
                if (data.success) {
                    const totalPriceEl = document.getElementById('total_price');
                    if (totalPriceEl) {
                        totalPriceEl.value = '$' + data.data.total_price.toLocaleString();
                    }
                    
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
                console.error('Error detallado al calcular precio con API:', error);
                console.error('Tipo de error:', error.constructor.name);
                console.error('Mensaje de error:', error.message);
                // Ya tenemos el precio básico calculado, no es necesario hacer nada más
            });
        } else {
            console.log('Fechas inválidas, reseteando campos');
            resetPriceFields();
        }
    }
    
    // Actualizar selección visual del calendario
    function updateCalendarSelection() {
        // Limpiar selecciones anteriores
        document.querySelectorAll('.calendar-day.selected').forEach(day => {
            day.classList.remove('selected');
        });
        
        // Marcar fechas seleccionadas
        document.querySelectorAll('.calendar-day').forEach(day => {
            const dayDate = new Date(day.textContent);
            dayDate.setMonth(currentDate.getMonth());
            dayDate.setFullYear(currentDate.getFullYear());
            
            if (selectedStartDate && dayDate.getTime() === selectedStartDate.getTime()) {
                day.classList.add('selected');
            }
            if (selectedEndDate && dayDate.getTime() === selectedEndDate.getTime()) {
                day.classList.add('selected');
            }
        });
    }
    
    // Actualizar inputs de fecha
    function updateDateInputs() {
        const startInput = document.getElementById('selectedStartDate');
        const endInput = document.getElementById('selectedEndDate');
        
        console.log('=== UPDATE DATE INPUTS ===');
        console.log('selectedStartDate:', selectedStartDate);
        console.log('selectedEndDate:', selectedEndDate);
        console.log('startInput found:', !!startInput);
        console.log('endInput found:', !!endInput);
        
        if (startInput) {
            const startValue = selectedStartDate ? selectedStartDate.toLocaleDateString() : '';
            startInput.value = startValue;
            console.log('Start input updated to:', startValue);
        }
        
        if (endInput) {
            const endValue = selectedEndDate ? selectedEndDate.toLocaleDateString() : '';
            endInput.value = endValue;
            console.log('End input updated to:', endValue);
        }
    }
    
    // Función para actualizar información de precio para usuarios no autenticados
    function updatePriceInfoForGuests(nights, totalPrice, startDate, endDate) {
        const priceInfo = document.getElementById('selectedPriceInfo');
        const selectedNights = document.getElementById('selectedNights');
        const selectedCheckIn = document.getElementById('selectedCheckIn');
        const selectedCheckOut = document.getElementById('selectedCheckOut');
        const selectedPricePerNight = document.getElementById('selectedPricePerNight');
        const selectedTotalPrice = document.getElementById('selectedTotalPrice');
        
        console.log('=== UPDATE PRICE INFO FOR GUESTS ===');
        console.log('priceInfo found:', !!priceInfo);
        console.log('selectedNights found:', !!selectedNights);
        console.log('selectedCheckIn found:', !!selectedCheckIn);
        console.log('selectedCheckOut found:', !!selectedCheckOut);
        console.log('selectedPricePerNight found:', !!selectedPricePerNight);
        console.log('selectedTotalPrice found:', !!selectedTotalPrice);
        
        if (priceInfo && selectedNights && selectedCheckIn && selectedCheckOut && selectedPricePerNight && selectedTotalPrice) {
            // Calcular precio por noche
            const pricePerNight = Math.round(totalPrice / nights);
            
            // Actualizar elementos
            selectedNights.textContent = `${nights} noche${nights !== 1 ? 's' : ''}`;
            selectedCheckIn.textContent = startDate.toLocaleDateString('es-ES', { 
                weekday: 'short', 
                day: 'numeric', 
                month: 'short' 
            });
            selectedCheckOut.textContent = endDate.toLocaleDateString('es-ES', { 
                weekday: 'short', 
                day: 'numeric', 
                month: 'short' 
            });
            selectedPricePerNight.textContent = `$${pricePerNight.toLocaleString()}`;
            selectedTotalPrice.textContent = `$${totalPrice.toLocaleString()}`;
            
            // Mostrar la sección
            priceInfo.style.display = 'block';
            
            console.log('Precio mostrado:', `$${totalPrice.toLocaleString()}`);
            
            // Guardar datos en localStorage para después del login
            localStorage.setItem('reservationData', JSON.stringify({
                property_id: {{ $property->id }},
                start_date: formatDateToLocalString(startDate),
                end_date: formatDateToLocalString(endDate),
                nights: nights,
                total_price: totalPrice,
                property_name: '{{ $property->name }}'
            }));
        } else {
            console.error('No se encontraron todos los elementos necesarios para mostrar el precio');
        }
    }
    
    // Limpiar selección
    function clearSelection() {
        selectedStartDate = null;
        selectedEndDate = null;
        isSelectingEndDate = false;
        
        // Limpiar visualización
        document.querySelectorAll('.calendar-day.selected').forEach(day => {
            day.classList.remove('selected');
        });
        
        // Limpiar inputs
        updateDateInputs();
        hideSelectionStatus();
        
        // Limpiar formulario de reserva
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        
        if (startDateInput) startDateInput.value = '';
        if (endDateInput) endDateInput.value = '';
        
        // Deshabilitar botón de reserva
        const reserveBtn = document.getElementById('reserveBtn');
        if (reserveBtn) {
            reserveBtn.disabled = true;
            reserveBtn.classList.remove('btn-primary');
            reserveBtn.classList.add('btn-secondary');
        }
        
        // Resetear campos de precio
        resetPriceFields();
        
        // Limpiar información de precio para usuarios no autenticados
        clearPriceInfoForGuests();
    }
    
    // Actualizar formulario de reserva
    function updateReservationForm() {
        if (selectedStartDate && selectedEndDate) {
            // Actualizar inputs ocultos del formulario
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            
            if (startDateInput && endDateInput) {
                startDateInput.value = formatDateToLocalString(selectedStartDate);
                endDateInput.value = formatDateToLocalString(selectedEndDate);
            }
            
            // Habilitar botón de reserva
            const reserveBtn = document.getElementById('reserveBtn');
            if (reserveBtn) {
                reserveBtn.disabled = false;
                reserveBtn.classList.remove('btn-secondary');
                reserveBtn.classList.add('btn-primary');
            }
            
            // Actualizar sección dinámica de precio
            updateDynamicPriceSection();
            
            // Calcular total si existe la función (solo para usuarios no autenticados)
            if (typeof calculateTotal === 'function' && !document.getElementById('reserveBtn')) {
                calculateTotal();
            }
        } else {
            // Deshabilitar botón de reserva
            const reserveBtn = document.getElementById('reserveBtn');
            if (reserveBtn) {
                reserveBtn.disabled = true;
                reserveBtn.classList.remove('btn-primary');
                reserveBtn.classList.add('btn-secondary');
            }
            
            // Ocultar sección dinámica de precio
            hideDynamicPriceSection();
        }
    }
    
    // Actualizar sección dinámica de precio
    function updateDynamicPriceSection() {
        console.log('=== UPDATE DYNAMIC PRICE SECTION ===');
        console.log('selectedStartDate:', selectedStartDate);
        console.log('selectedEndDate:', selectedEndDate);
        
        if (selectedStartDate && selectedEndDate) {
            // Calcular noches
            const nights = Math.ceil((selectedEndDate - selectedStartDate) / (1000 * 60 * 60 * 24));
            const pricePerNight = {{ $property->price }};
            const totalPrice = nights * pricePerNight;
            
            console.log('Noches calculadas:', nights);
            console.log('Precio por noche:', pricePerNight);
            console.log('Precio total:', totalPrice);
            
            // Actualizar elementos
            const dynamicPrice = document.getElementById('dynamicPrice');
            const dynamicNights = document.getElementById('dynamicNights');
            const dynamicDates = document.getElementById('dynamicDates');
            const dynamicTotalPrice = document.getElementById('dynamicTotalPrice');
            
            console.log('dynamicPrice found:', !!dynamicPrice);
            console.log('dynamicNights found:', !!dynamicNights);
            console.log('dynamicDates found:', !!dynamicDates);
            console.log('dynamicTotalPrice found:', !!dynamicTotalPrice);
            
            if (dynamicPrice && dynamicNights && dynamicDates && dynamicTotalPrice) {
                dynamicNights.textContent = `${nights} noche${nights !== 1 ? 's' : ''}`;
                dynamicDates.textContent = `${selectedStartDate.toLocaleDateString()} - ${selectedEndDate.toLocaleDateString()}`;
                dynamicTotalPrice.textContent = `$${totalPrice.toLocaleString()}`;
                dynamicPrice.style.display = 'block';
                
                console.log('Sección dinámica de precio actualizada');
                console.log('Contenido actualizado:', {
                    nights: dynamicNights.textContent,
                    dates: dynamicDates.textContent,
                    total: dynamicTotalPrice.textContent
                });
                console.log('Elemento visible:', dynamicPrice.style.display);
            } else {
                console.error('No se encontraron todos los elementos de la sección dinámica de precio');
            }
        }
    }
    
    // Ocultar sección dinámica de precio
    function hideDynamicPriceSection() {
        const dynamicPrice = document.getElementById('dynamicPrice');
        if (dynamicPrice) {
            dynamicPrice.style.display = 'none';
        }
    }
    
    // Manejar clic en día
    function handleDayClick(dayDate, dayElement) {
        // No permitir selección de días ocupados o deshabilitados
        if (dayElement.classList.contains('occupied')) {
            showOccupiedDateWarning(dayDate);
            return;
        }
        
        if (dayElement.classList.contains('disabled') || 
            dayElement.classList.contains('other-month')) {
            return;
        }
        
        // Lógica de selección
        if (!selectedStartDate) {
            // Primera selección: fecha de inicio
            selectedStartDate = dayDate;
            dayElement.classList.add('selected');
            isSelectingEndDate = true;
            
            // Actualizar inputs
            updateDateInputs();
            showSelectionStatus('Selecciona la fecha de salida');
            
            console.log('Fecha de inicio seleccionada:', selectedStartDate.toLocaleDateString());
        } else if (!selectedEndDate && isSelectingEndDate) {
            // Segunda selección: fecha de fin - VALIDAR RANGO COMPLETO
            const startDate = selectedStartDate;
            const endDate = dayDate;
            
            // Determinar el rango correcto (inicio y fin)
            let actualStartDate, actualEndDate;
            if (dayDate.getTime() === startDate.getTime()) {
                // Misma fecha: reserva de un día
                actualStartDate = startDate;
                actualEndDate = dayDate;
            } else if (dayDate < startDate) {
                // Fecha anterior: intercambiar
                actualStartDate = dayDate;
                actualEndDate = startDate;
            } else {
                // Fecha posterior: rango normal
                actualStartDate = startDate;
                actualEndDate = dayDate;
            }
            
            // VALIDAR QUE NO HAYA FECHAS OCUPADAS EN EL RANGO
            if (validateDateRange(actualStartDate, actualEndDate)) {
                selectedStartDate = actualStartDate;
                selectedEndDate = actualEndDate;
                isSelectingEndDate = false;
                
                // Actualizar visualización
                updateCalendarSelection();
                updateDateInputs();
                hideSelectionStatus();
                
                // Actualizar formulario de reserva
                updateReservationForm();
                
                console.log('Rango válido seleccionado - Inicio:', selectedStartDate.toLocaleDateString(), 'Fin:', selectedEndDate.toLocaleDateString());
            } else {
                // Mostrar error y sugerir fechas alternativas
                suggestAlternativeDates(actualStartDate, actualEndDate);
                return;
            }
        } else {
            // Nueva selección: reiniciar
            clearSelection();
            selectedStartDate = dayDate;
            dayElement.classList.add('selected');
            isSelectingEndDate = true;
            
            updateDateInputs();
            showSelectionStatus('Selecciona la fecha de salida');
            
            console.log('Nueva selección iniciada:', dayDate.toLocaleDateString());
        }
    }
    
    // Validar rango de fechas
    function validateDateRange(startDate, endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        
        // Verificar que no haya fechas ocupadas en el rango
        for (let d = new Date(start); d <= end; d.setDate(d.getDate() + 1)) {
            const dateString = d.toISOString().split('T')[0];
            if (occupiedDates.includes(dateString)) {
                return false;
            }
        }
        
        return true;
    }
    
    // Mostrar advertencia de fecha ocupada
    function showOccupiedDateWarning(date) {
        showAvailabilityMessage(`La fecha ${date.toLocaleDateString()} está ocupada`, 'warning');
    }
    
    // Crear elemento de día
    function createDayElement(dayDate) {
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        dayElement.textContent = dayDate.getDate();
        
        const today = new Date();
        const isCurrentMonth = dayDate.getMonth() === currentDate.getMonth();
        const isToday = dayDate.toDateString() === today.toDateString();
        
        // Permitir seleccionar el día de hoy, solo bloquear fechas pasadas
        const todayOnly = new Date(today.getFullYear(), today.getMonth(), today.getDate());
        const dayDateOnly = new Date(dayDate.getFullYear(), dayDate.getMonth(), dayDate.getDate());
        const isPast = dayDateOnly < todayOnly;
        const dateString = dayDate.toISOString().split('T')[0];
        const isOccupied = isDateOccupied(dateString);
        
        // Aplicar clases según el estado
        if (!isCurrentMonth) {
            dayElement.classList.add('other-month');
        }
        
        if (isPast) {
            dayElement.classList.add('disabled');
        }
        
        if (isOccupied) {
            dayElement.classList.add('occupied');
            dayElement.title = 'Fecha ocupada - No disponible';
        } else if (!isPast && isCurrentMonth) {
            // Solo las fechas disponibles y del mes actual tienen animación
            dayElement.classList.add('selectable');
        }
        
        if (isToday) {
            dayElement.classList.add('today');
        }
        
        // Verificar si está seleccionado
        if (selectedStartDate && dayDate.getTime() === selectedStartDate.getTime()) {
            dayElement.classList.add('selected-start');
        }
        if (selectedEndDate && dayDate.getTime() === selectedEndDate.getTime()) {
            dayElement.classList.add('selected-end');
        }
        
        // Verificar si está en el rango seleccionado
        if (selectedStartDate && selectedEndDate) {
            const start = selectedStartDate.getTime();
            const end = selectedEndDate.getTime();
            const dayTime = dayDate.getTime();
            
            if (dayTime > start && dayTime < end) {
                dayElement.classList.add('in-range');
            }
        }
        
        // Verificar si debe estar bloqueado para rango
        if (selectedStartDate && !selectedEndDate) {
            const startDateOnly = new Date(selectedStartDate.getFullYear(), selectedStartDate.getMonth(), selectedStartDate.getDate());
            const dayDateOnly = new Date(dayDate.getFullYear(), dayDate.getMonth(), dayDate.getDate());
            
            if (dayDateOnly > startDateOnly) {
                // Buscar si hay fechas ocupadas entre la fecha de inicio y la fecha actual
                let currentDate = new Date(startDateOnly);
                currentDate.setDate(currentDate.getDate() + 1); // Empezar desde el día siguiente
                
                let hasOccupiedInRange = false;
                let lastOccupiedDate = null;
                
                while (currentDate <= dayDateOnly) {
                    const currentDateString = currentDate.toISOString().split('T')[0];
                    if (isDateOccupied(currentDateString)) {
                        hasOccupiedInRange = true;
                        lastOccupiedDate = new Date(currentDate);
                    }
                    currentDate.setDate(currentDate.getDate() + 1);
                }
                
                // Si hay fechas ocupadas en el rango, bloquear todas las fechas posteriores
                if (hasOccupiedInRange) {
                    // Bloquear todas las fechas posteriores a la última fecha ocupada
                    if (lastOccupiedDate && dayDateOnly > lastOccupiedDate) {
                        dayElement.classList.add('blocked-range');
                        dayElement.title = 'Rango bloqueado - Hay fechas ocupadas en el rango';
                    }
                }
            }
        }
        
        // Agregar evento de clic
        dayElement.addEventListener('click', () => handleDayClick(dayDate, dayElement));
        
        return dayElement;
    }
    
    // Configurar botones de acción
    function setupCalendarActions() {
        const clearBtn = document.getElementById('clearDates');
        const todayBtn = document.getElementById('selectToday');
        const weekendBtn = document.getElementById('selectWeekend');
        
        if (clearBtn) {
            clearBtn.addEventListener('click', clearSelection);
        }
        
        if (todayBtn) {
            todayBtn.addEventListener('click', () => {
                const today = new Date();
                const dayElement = document.querySelector(`.calendar-day:not(.disabled):not(.occupied)`);
                if (dayElement) {
                    handleDayClick(today, dayElement);
                }
            });
        }
        
        if (weekendBtn) {
            weekendBtn.addEventListener('click', () => {
                const today = new Date();
                const nextSaturday = new Date(today);
                nextSaturday.setDate(today.getDate() + (6 - today.getDay()));
                const nextSunday = new Date(nextSaturday);
                nextSunday.setDate(nextSaturday.getDate() + 1);
                
                // Seleccionar fin de semana
                clearSelection();
                selectedStartDate = nextSaturday;
                selectedEndDate = nextSunday;
                isSelectingEndDate = false;
                
                updateCalendarSelection();
                updateDateInputs();
                updateReservationForm();
            });
        }
    }
    
    // Función para verificar si una fecha está ocupada
    function isDateOccupied(dateString) {
        return occupiedDates.includes(dateString);
    }
    
    // Actualizar el mes y año en el header
    function updateMonthYear() {
        const monthYearElement = document.getElementById('currentMonthYear');
        if (monthYearElement) {
            const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 
                               'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            const month = monthNames[currentDate.getMonth()];
            const year = currentDate.getFullYear();
            monthYearElement.textContent = `${month} ${year}`;
        }
    }
    
    // Renderizar calendario
    function renderCalendar() {
        const calendarDays = document.getElementById('calendarDays');
        const monthYear = document.getElementById('currentMonthYear');
        
        if (!calendarDays || !monthYear) return;
        
        // Actualizar título del mes
        updateMonthYear();
        
        // Limpiar días existentes
        calendarDays.innerHTML = '';
        
        // Obtener primer día del mes y número de días
        const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
        const startDate = new Date(firstDay);
        startDate.setDate(startDate.getDate() - firstDay.getDay());
        
        // Generar días del calendario
        for (let i = 0; i < 42; i++) {
            const dayDate = new Date(startDate);
            dayDate.setDate(startDate.getDate() + i);
            
            const dayElement = createDayElement(dayDate);
            calendarDays.appendChild(dayElement);
        }
    }
    
    // Inicializar calendario dinámico
    function initializeDynamicCalendar() {
        console.log('Inicializando calendario dinámico');
        
        // Cargar fechas ocupadas
        fetchOccupiedDates();
        
        // Renderizar calendario
        renderCalendar();
        
        // Configurar navegación
        setupCalendarNavigation();
        
        // Configurar botones de acción
        setupCalendarActions();
    }
    
    // Cargar fechas ocupadas
    async function fetchOccupiedDates() {
        const propertyId = {{ $property->id }};
        
        try {
            // Mostrar indicador de carga
            showLoadingIndicator();
            
            const response = await fetch(`/api/properties/${propertyId}/occupied-dates`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            occupiedDates = data.occupied_dates || [];
            console.log('Fechas ocupadas cargadas:', occupiedDates);
            
            // Mostrar información de fechas ocupadas
            showOccupiedDatesInfo(occupiedDates.length);
            
            // Re-renderizar calendario con fechas ocupadas
            renderCalendar();
        } catch (error) {
            console.error('Error al cargar fechas ocupadas:', error);
            occupiedDates = [];
            showErrorIndicator('Error cargando fechas ocupadas');
        }
    }
    
    // Función para mostrar indicador de carga
    function showLoadingIndicator() {
        const indicator = document.getElementById('occupiedIndicator');
        if (indicator) {
            indicator.style.display = 'flex';
            indicator.innerHTML = '<i class="fas fa-circle"></i><span>Cargando fechas ocupadas...</span>';
        }
    }
    
    // Función para mostrar información de fechas ocupadas
    function showOccupiedDatesInfo(count) {
        const indicator = document.getElementById('occupiedIndicator');
        if (indicator) {
            if (count > 0) {
                indicator.innerHTML = `<i class="fas fa-circle"></i><span>${count} fecha(s) ocupada(s) encontrada(s)</span>`;
            } else {
                indicator.innerHTML = '<i class="fas fa-circle"></i><span>No hay fechas ocupadas</span>';
            }
            indicator.style.display = 'flex';
        }
    }
});

// Funcionalidades dinámicas mejoradas

// Función para ir a una slide específica
function goToSlide(index) {
    const carousel = new bootstrap.Carousel(document.getElementById('propertyCarousel'));
    carousel.to(index);
}

// Función para abrir modal de imagen
function openImageModal(imageUrl) {
    // Crear modal dinámico
    const modalHtml = `
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content bg-dark">
                    <div class="modal-header border-0">
                        <h5 class="modal-title text-white" id="imageModalLabel">Vista ampliada</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0 text-center">
                        <img src="${imageUrl}" class="img-fluid" alt="Imagen ampliada" style="max-height: 80vh; object-fit: contain;">
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remover modal existente si existe
    const existingModal = document.getElementById('imageModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Agregar nuevo modal al body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
    
    // Limpiar modal cuando se cierre
    document.getElementById('imageModal').addEventListener('hidden.bs.modal', function() {
        this.remove();
    });
}

// Función para compartir propiedad
function shareProperty() {
    if (navigator.share) {
        navigator.share({
            title: '{{ $property->name }}',
            text: 'Mira esta increíble propiedad en Reservalo',
            url: window.location.href
        });
    } else {
        // Fallback para navegadores que no soportan Web Share API
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('¡Enlace copiado al portapapeles!');
        });
    }
}

// Función para scroll suave
function smoothScrollTo(element) {
    element.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
}

// Función para animar elementos al hacer scroll
function animateOnScroll() {
    const elements = document.querySelectorAll('.fade-in-up');
    elements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const elementVisible = 150;
        
        if (elementTop < window.innerHeight - elementVisible) {
            element.classList.add('fade-in-up');
        }
    });
}

// Función para el botón de volver arriba
function toggleBackToTop() {
    const backToTopBtn = document.getElementById('backToTop');
    if (window.scrollY > 300) {
        backToTopBtn.style.display = 'block';
    } else {
        backToTopBtn.style.display = 'none';
    }
}

// Función para actualizar precio dinámico
function updateDynamicPrice() {
    const priceElement = document.getElementById('dynamic-price');
    if (priceElement) {
        // Aquí puedes agregar lógica para actualizar el precio basado en fechas seleccionadas
        // Por ahora mantenemos el precio base
    }
}

// Inicializar funcionalidades cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Agregar clases de animación a elementos
    const animatedElements = document.querySelectorAll('.card, .feature-item, .service-item');
    animatedElements.forEach((element, index) => {
        element.style.animationDelay = `${index * 0.1}s`;
        element.classList.add('fade-in-up');
    });
    
    // Configurar scroll listener
    window.addEventListener('scroll', () => {
        animateOnScroll();
        toggleBackToTop();
    });
    
    // Configurar botón de volver arriba
    const backToTopBtn = document.getElementById('backToTop');
    if (backToTopBtn) {
        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
    
    // Configurar botones de compartir
    const shareButtons = document.querySelectorAll('[onclick*="shareProperty"]');
    shareButtons.forEach(button => {
        button.addEventListener('click', shareProperty);
    });
    
    // Inicializar animaciones
    animateOnScroll();
    
    // Configurar event listeners después de que todo el DOM esté cargado
    setupEventListeners();
    
    // Configurar botones de login/registro con fechas
    setupGuestButtons();
    
    // Cargar datos de reserva si existen (después del login)
    loadReservationDataFromStorage();
});

// Variables globales para el calendario dinámico (declaradas dentro de DOMContentLoaded)

// Función auxiliar para crear fechas en zona horaria local
function createLocalDate(dateString) {
    if (!dateString) return null;
    // Si ya tiene formato completo, usarlo directamente
    if (dateString.includes('T')) {
        return new Date(dateString);
    }
    // Si es solo fecha (YYYY-MM-DD), agregar hora local
    return new Date(dateString + 'T00:00:00');
}

// Función auxiliar para formatear fecha a string local
function formatDateToLocalString(date) {
    if (!date) return '';
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}


// Función para limpiar información de precio
function clearPriceInfoForGuests() {
    const priceInfo = document.getElementById('selectedPriceInfo');
    if (priceInfo) {
        priceInfo.style.display = 'none';
    }
    localStorage.removeItem('reservationData');
}

// Configurar botones de login/registro para usuarios no autenticados
function setupGuestButtons() {
    const loginBtn = document.getElementById('loginWithDates');
    const registerBtn = document.getElementById('registerWithDates');
    
    if (loginBtn) {
        loginBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const reservationData = localStorage.getItem('reservationData');
            if (reservationData) {
                // Crear formulario temporal para enviar datos
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = '{{ route("login") }}';
                
                const redirectInput = document.createElement('input');
                redirectInput.type = 'hidden';
                redirectInput.name = 'redirect_to';
                redirectInput.value = window.location.href;
                
                const dataInput = document.createElement('input');
                dataInput.type = 'hidden';
                dataInput.name = 'reservation_data';
                dataInput.value = reservationData;
                
                form.appendChild(redirectInput);
                form.appendChild(dataInput);
                document.body.appendChild(form);
                form.submit();
            } else {
                window.location.href = '{{ route("login") }}?redirect_to=' + encodeURIComponent(window.location.href);
            }
        });
    }
    
    if (registerBtn) {
        registerBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const reservationData = localStorage.getItem('reservationData');
            if (reservationData) {
                // Crear formulario temporal para enviar datos
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = '{{ route("register") }}';
                
                const redirectInput = document.createElement('input');
                redirectInput.type = 'hidden';
                redirectInput.name = 'redirect_to';
                redirectInput.value = window.location.href;
                
                const dataInput = document.createElement('input');
                dataInput.type = 'hidden';
                dataInput.name = 'reservation_data';
                dataInput.value = reservationData;
                
                form.appendChild(redirectInput);
                form.appendChild(dataInput);
                document.body.appendChild(form);
                form.submit();
            } else {
                window.location.href = '{{ route("register") }}?redirect_to=' + encodeURIComponent(window.location.href);
            }
        });
    }
}

// Cargar datos de reserva desde localStorage (después del login)
function loadReservationDataFromStorage() {
    const reservationData = localStorage.getItem('reservationData');
    if (reservationData) {
        try {
            const data = JSON.parse(reservationData);
            console.log('Datos de reserva encontrados:', data);
            
            // Verificar que los datos sean para esta propiedad
            if (data.property_id == {{ $property->id }}) {
                // Cargar las fechas seleccionadas
                selectedStartDate = createLocalDate(data.start_date);
                selectedEndDate = createLocalDate(data.end_date);
                
                if (selectedStartDate && selectedEndDate) {
                    // Actualizar la visualización del calendario
                    updateCalendarSelection();
                    updateDateInputs();
                    updateReservationForm();
                    
                    // Mostrar mensaje de bienvenida
                    showAvailabilityMessage(`¡Bienvenido! Tus fechas seleccionadas han sido cargadas: ${data.nights} noche${data.nights !== 1 ? 's' : ''} por $${data.total_price.toLocaleString()}`, 'success');
                    
                    // Limpiar los datos del localStorage ya que se han cargado
                    localStorage.removeItem('reservationData');
                }
            }
        } catch (error) {
            console.error('Error al cargar datos de reserva:', error);
            localStorage.removeItem('reservationData');
        }
    }
}




// Función para mostrar error
function showErrorIndicator(message) {
    const indicator = document.getElementById('occupiedIndicator');
    if (indicator) {
        indicator.innerHTML = `<i class="fas fa-circle"></i><span>${message}</span>`;
        indicator.style.display = 'flex';
    }
}


// Función para validar que no haya fechas ocupadas en un rango
function validateDateRange(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    
    // Verificar cada día del rango
    while (start <= end) {
        const dateString = start.toISOString().split('T')[0];
        if (isDateOccupied(dateString)) {
            return false; // Hay al menos una fecha ocupada
        }
        start.setDate(start.getDate() + 1);
    }
    
    return true; // El rango está completamente disponible
}

// Función para encontrar fechas ocupadas en un rango
function findOccupiedDatesInRange(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    const occupiedInRange = [];
    
    while (start <= end) {
        const dateString = start.toISOString().split('T')[0];
        if (isDateOccupied(dateString)) {
            occupiedInRange.push(dateString);
        }
        start.setDate(start.getDate() + 1);
    }
    
    return occupiedInRange;
}

// Función para mostrar advertencia de fecha ocupada
function showOccupiedDateWarning(dayDate) {
    const dateFormatted = dayDate.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    
    alert(`Fecha ocupada: ${dateFormatted}\nEsta fecha no está disponible para reservas`);
}

// Función para mostrar advertencia de rango ocupado
function showRangeOccupiedWarning(startDate, endDate) {
    const startFormatted = startDate.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    const endFormatted = endDate.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    
    // Encontrar fechas ocupadas en el rango
    const occupiedInRange = findOccupiedDatesInRange(startDate, endDate);
    const occupiedDatesFormatted = occupiedInRange.map(date => 
        new Date(date).toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit'
        })
    ).join(', ');
    
    alert(`Rango no disponible: ${startFormatted} - ${endFormatted}\nFechas ocupadas: ${occupiedDatesFormatted}`);
}

// Función para mostrar advertencia de fecha bloqueada para rango
function showBlockedRangeWarning(dayDate) {
    const dateFormatted = dayDate.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    
    const startFormatted = selectedStartDate.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    
    // Encontrar fechas ocupadas en el rango
    const occupiedInRange = findOccupiedDatesInRange(selectedStartDate, dayDate);
    const occupiedDatesFormatted = occupiedInRange.map(date => 
        new Date(date).toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit'
        })
    ).join(', ');
    
    alert(`Rango bloqueado: ${startFormatted} - ${dateFormatted}\nFechas ocupadas: ${occupiedDatesFormatted}`);
}

// Función para sugerir fechas alternativas
function suggestAlternativeDates(startDate, endDate) {
    const startFormatted = startDate.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    const endFormatted = endDate.toLocaleDateString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
    
    // Encontrar fechas ocupadas en el rango
    const occupiedInRange = findOccupiedDatesInRange(startDate, endDate);
    const occupiedDatesFormatted = occupiedInRange.map(date => 
        new Date(date).toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit'
        })
    ).join(', ');
    
    // Buscar fechas alternativas
    const alternatives = findAlternativeRanges(startDate, endDate);
    
    let alternativesText = '';
    if (alternatives.length > 0) {
        alternativesText = '\n\nFechas alternativas sugeridas:\n' + 
            alternatives.map(alt => `• ${alt.start} - ${alt.end}`).join('\n');
    }
    
    alert(`Rango no disponible: ${startFormatted} - ${endFormatted}\nFechas ocupadas: ${occupiedDatesFormatted}${alternativesText}`);
}

// Función para encontrar rangos alternativos
function findAlternativeRanges(startDate, endDate) {
    const alternatives = [];
    const start = new Date(startDate);
    const end = new Date(endDate);
    const rangeLength = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
    
    // Buscar antes del rango original
    for (let i = 1; i <= 14; i++) { // Aumentar el rango de búsqueda
        const altStart = new Date(start);
        altStart.setDate(start.getDate() - i);
        const altEnd = new Date(altStart);
        altEnd.setDate(altStart.getDate() + rangeLength - 1);
        
        // Verificar que el rango alternativo esté completamente libre
        if (validateDateRange(altStart, altEnd) && !hasOccupiedDatesInRange(altStart, altEnd)) {
            alternatives.push({
                start: altStart.toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit'
                }),
                end: altEnd.toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit'
                })
            });
            if (alternatives.length >= 2) break;
        }
    }
    
    // Buscar después del rango original
    for (let i = 1; i <= 14; i++) { // Aumentar el rango de búsqueda
        const altStart = new Date(start);
        altStart.setDate(start.getDate() + i);
        const altEnd = new Date(altStart);
        altEnd.setDate(altStart.getDate() + rangeLength - 1);
        
        // Verificar que el rango alternativo esté completamente libre
        if (validateDateRange(altStart, altEnd) && !hasOccupiedDatesInRange(altStart, altEnd)) {
            alternatives.push({
                start: altStart.toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit'
                }),
                end: altEnd.toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: '2-digit'
                })
            });
            if (alternatives.length >= 4) break;
        }
    }
    
    return alternatives.slice(0, 3); // Máximo 3 alternativas
}

// Función auxiliar para verificar si hay fechas ocupadas en un rango
function hasOccupiedDatesInRange(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    
    while (start <= end) {
        const dateString = start.toISOString().split('T')[0];
        if (isDateOccupied(dateString)) {
            return true; // Hay al menos una fecha ocupada
        }
        start.setDate(start.getDate() + 1);
    }
    
    return false; // El rango está completamente libre
}





// Mostrar advertencia de rango ocupado
function showRangeOccupiedWarning(startDate, endDate) {
    showAvailabilityMessage(`El rango del ${startDate.toLocaleDateString()} al ${endDate.toLocaleDateString()} contiene fechas ocupadas`, 'danger');
}

// Mostrar advertencia de fecha bloqueada
function showBlockedRangeWarning(date) {
    showAvailabilityMessage(`La fecha ${date.toLocaleDateString()} no se puede seleccionar como fecha de fin debido a fechas ocupadas en el rango`, 'warning');
}

// Sugerir fechas alternativas
function suggestAlternativeDates(startDate, endDate) {
    const alternatives = findAlternativeRanges(startDate, endDate);
    
    if (alternatives.length > 0) {
        let message = 'Fechas alternativas disponibles:\n';
        alternatives.forEach((alt, index) => {
            message += `${index + 1}. ${alt.start.toLocaleDateString()} - ${alt.end.toLocaleDateString()}\n`;
        });
        showAvailabilityMessage(message, 'info');
    } else {
        showAvailabilityMessage('No hay fechas alternativas disponibles para este rango', 'info');
    }
}

// Encontrar rangos alternativos
function findAlternativeRanges(startDate, endDate) {
    const alternatives = [];
    const rangeLength = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
    
    // Buscar antes del rango original
    for (let i = 1; i <= 7; i++) {
        const newStart = new Date(startDate);
        newStart.setDate(newStart.getDate() - i);
        const newEnd = new Date(newStart);
        newEnd.setDate(newEnd.getDate() + rangeLength);
        
        if (validateDateRange(newStart, newEnd)) {
            alternatives.push({ start: newStart, end: newEnd });
            if (alternatives.length >= 3) break;
        }
    }
    
    // Buscar después del rango original
    for (let i = 1; i <= 7; i++) {
        const newStart = new Date(startDate);
        newStart.setDate(newStart.getDate() + i);
        const newEnd = new Date(newStart);
        newEnd.setDate(newEnd.getDate() + rangeLength);
        
        if (validateDateRange(newStart, newEnd)) {
            alternatives.push({ start: newStart, end: newEnd });
            if (alternatives.length >= 3) break;
        }
    }
    
    return alternatives;
}



// Mostrar estado de selección
function showSelectionStatus(message) {
    const status = document.getElementById('selectionStatus');
    const statusText = document.getElementById('statusText');
    
    if (status && statusText) {
        statusText.textContent = message;
        status.style.display = 'block';
    }
}

// Ocultar estado de selección
function hideSelectionStatus() {
    const status = document.getElementById('selectionStatus');
    if (status) {
        status.style.display = 'none';
    }
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
        
        const basePrice = {{ $effectivePrice }};
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
        
        // Si es después de las 22:00, no permitir reservas para hoy
        if (currentHour >= 22) {
            const tomorrow = new Date(now);
            tomorrow.setDate(tomorrow.getDate() + 1);
            const tomorrowStr = formatDateToLocalString(tomorrow);
            
            startDateInput.min = tomorrowStr;
            endDateInput.min = tomorrowStr;
        } else {
            // Si es antes de las 22:00, permitir reservas para hoy
            const todayStr = formatDateToLocalString(now);
            startDateInput.min = todayStr;
            endDateInput.min = todayStr;
        }
    }
}


// Función de fallback para cálculo básico
function fallbackCalculation(nights) {
    // Usar el precio efectivo calculado por el sistema de precios
    const pricePerNight = {{ $effectivePrice }};
    
    // Si es el mismo día, calcular como 1 noche
    const actualNights = nights === 0 ? 1 : nights;
    const totalPrice = actualNights * pricePerNight;
    
    const nightsEl = document.getElementById('nights');
    const totalPriceEl = document.getElementById('total_price');
    const submitBtnEl = document.getElementById('submitBtn');
    const priceBreakdownEl = document.getElementById('price-breakdown');
    const pricePerNightEl = document.getElementById('price-per-night');
    const avgNightPriceEl = document.getElementById('avg-night-price');
    
    if (nightsEl) nightsEl.value = actualNights + ' noche(s)';
    if (totalPriceEl) totalPriceEl.value = '$' + totalPrice.toLocaleString();
    if (submitBtnEl) {
        submitBtnEl.disabled = false;
        submitBtnEl.innerHTML = '<i class="fas fa-calendar-check me-2"></i>Enviar solicitud';
    }
    
    // Mostrar precio por noche
    if (pricePerNightEl && avgNightPriceEl) {
        avgNightPriceEl.textContent = '$' + pricePerNight.toLocaleString();
        pricePerNightEl.style.display = 'block';
    }
    
    if (priceBreakdownEl) priceBreakdownEl.style.display = 'none';
}

// Resetear campos de precio
function resetPriceFields() {
    const nightsEl = document.getElementById('nights');
    const totalPriceEl = document.getElementById('total_price');
    const submitBtnEl = document.getElementById('submitBtn');
    const priceBreakdownEl = document.getElementById('price-breakdown');
    const discountsInfoEl = document.getElementById('discounts-info');
    const pricePerNightEl = document.getElementById('price-per-night');
    
    if (nightsEl) nightsEl.value = '';
    if (totalPriceEl) totalPriceEl.value = '';
    if (submitBtnEl) {
        submitBtnEl.disabled = true;
        submitBtnEl.innerHTML = '<i class="fas fa-calendar-check me-2"></i>Enviar solicitud';
    }
    if (priceBreakdownEl) priceBreakdownEl.style.display = 'none';
    if (discountsInfoEl) discountsInfoEl.innerHTML = '';
    if (pricePerNightEl) pricePerNightEl.style.display = 'none';
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
            total_amount: {{ $effectivePrice }},
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
        
        // Si es muy tarde en el día (después de las 22:00), no permitir reservas para hoy
        if (currentHour >= 22) {
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

// Crear reserva directamente
async function createReservation() {
    // Obtener fechas desde los inputs ocultos
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    if (!startDateInput || !endDateInput || !startDateInput.value || !endDateInput.value) {
        showAvailabilityMessage('Por favor selecciona las fechas de tu reserva', 'warning');
        return;
    }
    
    const startDate = startDateInput.value;
    const endDate = endDateInput.value;

    // Mostrar indicador de carga
    const reserveBtn = document.getElementById('reserveBtn');
    if (reserveBtn) {
        reserveBtn.disabled = true;
        reserveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creando reserva...';
    }

    try {
        // Calcular noches
        const start = new Date(startDate);
        const end = new Date(endDate);
        const nights = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
        const totalPrice = nights * {{ $property->price }};

        // Crear datos de la reserva
        const reservationData = {
            property_id: {{ $property->id }},
            start_date: startDate,
            end_date: endDate,
            guests: 1, // Valor por defecto
            total_price: totalPrice,
            special_requests: '',
            _token: '{{ csrf_token() }}'
        };

        // Enviar solicitud de reserva
        const response = await fetch('{{ route("reservations.store.public") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(reservationData)
        });

        if (response.ok) {
            const result = await response.json();
            showAvailabilityMessage('¡Reserva creada exitosamente! Te redirigiremos a tus reservas.', 'success');
            
            // Redirigir a la página de reservas después de 2 segundos
            setTimeout(() => {
                window.location.href = '{{ route("reservations.index") }}';
            }, 2000);
        } else {
            const error = await response.json();
            showAvailabilityMessage('Error al crear la reserva: ' + (error.message || 'Error desconocido'), 'danger');
        }
    } catch (error) {
        console.error('Error al crear reserva:', error);
        showAvailabilityMessage('Error al crear la reserva. Por favor intenta nuevamente.', 'danger');
    } finally {
        // Restaurar botón
        if (reserveBtn) {
            reserveBtn.disabled = false;
            reserveBtn.innerHTML = '<i class="fas fa-calendar-check me-2"></i>Reservar Ahora';
        }
    }
}
</script>
@endsection
