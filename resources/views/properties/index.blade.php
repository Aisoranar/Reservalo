@extends('layouts.app')

@section('title', 'Propiedades - Reservalo')

@section('content')
<div class="container-fluid py-4">
    <!-- Hero Section Mejorado -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="hero-section text-white rounded-4 p-5 text-center position-relative overflow-hidden">
                <div class="hero-overlay"></div>
                <div class="hero-content position-relative">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-home me-3"></i>Encuentra tu lugar perfecto
                    </h1>
                    <p class="lead mb-4">Descubre las mejores propiedades turísticas en Colombia</p>
                    
                    <!-- Búsqueda rápida mejorada -->
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <form method="GET" action="{{ route('properties.index') }}" id="heroSearchForm" class="search-form">
                                <div class="input-group input-group-lg shadow-lg">
                                    <span class="input-group-text bg-white border-0">
                                        <i class="fas fa-search text-primary"></i>
                                    </span>
                                    <input type="text" class="form-control border-0 py-3" name="search" 
                                           placeholder="¿Dónde quieres ir? Busca por ciudad, departamento o nombre..." 
                                           id="searchInput" value="{{ request('search') }}">
                                    <button class="btn btn-primary px-4" type="submit">
                                        <i class="fas fa-search me-2"></i>Buscar
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Estadísticas rápidas -->
                    <div class="row mt-4 justify-content-center">
                        <div class="col-md-3 col-6 mb-3">
                            <div class="stat-item">
                                <div class="stat-number">{{ $properties->total() }}</div>
                                <div class="stat-label">Propiedades</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="stat-item">
                                <div class="stat-number">{{ \App\Models\Department::active()->count() }}</div>
                                <div class="stat-label">Departamentos</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="stat-item">
                                <div class="stat-number">{{ \App\Models\City::active()->count() }}</div>
                                <div class="stat-label">Ciudades</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-6 mb-3">
                            <div class="stat-item">
                                <div class="stat-number">24/7</div>
                                <div class="stat-label">Soporte</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar de Filtros Mejorado -->
        <div class="col-lg-3 mb-4">
            <div class="filters-sidebar">
                <div class="card shadow-sm border-0 sticky-top" style="top: 100px;">
                    <div class="card-header bg-gradient-primary text-white border-0">
                        <h5 class="mb-0">
                            <i class="fas fa-filter me-2"></i>Filtros Avanzados
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('properties.index') }}" id="filterForm">
                            <!-- Indicador de carga mejorado -->
                            <div id="loadingIndicator" class="d-none text-center py-3">
                                <div class="loading-spinner">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <p class="mt-2 text-muted mb-0">Aplicando filtros...</p>
                                </div>
                            </div>

                            <!-- Búsqueda -->
                            <div class="filter-section mb-4">
                                <label class="form-label fw-bold text-primary">
                                    <i class="fas fa-map-marker-alt me-2"></i>Ubicación
                                </label>
                                <select name="department" id="department" class="form-select form-select-sm mb-2">
                                    <option value="">Todos los departamentos</option>
                                    @foreach(\App\Models\Department::active()->ordered()->get() as $dept)
                                        <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <select name="city" id="city" class="form-select form-select-sm" {{ !request('department') ? 'disabled' : '' }}>
                                    <option value="">Todas las ciudades</option>
                                    @if(request('department'))
                                        @foreach(\App\Models\City::active()->byDepartment(request('department'))->ordered()->get() as $city)
                                            <option value="{{ $city->id }}" {{ request('city') == $city->id ? 'selected' : '' }}>
                                                {{ $city->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <!-- Tipo de Propiedad -->
                            <div class="filter-section mb-4">
                                <label class="form-label fw-bold text-primary">
                                    <i class="fas fa-building me-2"></i>Tipo de Propiedad
                                </label>
                                <div class="type-buttons">
                                    <input type="radio" class="btn-check" name="type" id="type_all" value="" {{ !request('type') ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary btn-sm" for="type_all">Todos</label>
                                    
                                    <input type="radio" class="btn-check" name="type" id="type_casa" value="casa" {{ request('type') == 'casa' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary btn-sm" for="type_casa">Casa</label>
                                    
                                    <input type="radio" class="btn-check" name="type" id="type_apartamento" value="apartamento" {{ request('type') == 'apartamento' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary btn-sm" for="type_apartamento">Apto</label>
                                    
                                    <input type="radio" class="btn-check" name="type" id="type_cabaña" value="cabaña" {{ request('type') == 'cabaña' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary btn-sm" for="type_cabaña">Cabaña</label>
                                    
                                    <input type="radio" class="btn-check" name="type" id="type_hotel" value="hotel" {{ request('type') == 'hotel' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary btn-sm" for="type_hotel">Hotel</label>
                                </div>
                            </div>

                            <!-- Precio con slider -->
                            <div class="filter-section mb-4">
                                <label class="form-label fw-bold text-primary">
                                    <i class="fas fa-dollar-sign me-2"></i>Rango de Precio
                                </label>
                                <div class="price-range">
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <input type="number" class="form-control form-control-sm" name="min_price" 
                                                   value="{{ request('min_price') }}" placeholder="Min" min="0">
                                        </div>
                                        <div class="col-6">
                                            <input type="number" class="form-control form-control-sm" name="max_price" 
                                                   value="{{ request('max_price') }}" placeholder="Max" min="0">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">$0</small>
                                        <small class="text-muted">$1,000,000+</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Capacidad -->
                            <div class="filter-section mb-4">
                                <label class="form-label fw-bold text-primary">
                                    <i class="fas fa-users me-2"></i>Capacidad mínima
                                </label>
                                <select name="min_capacity" class="form-select form-select-sm">
                                    <option value="">Cualquiera</option>
                                    <option value="1" {{ request('min_capacity') == '1' ? 'selected' : '' }}>1+ personas</option>
                                    <option value="2" {{ request('min_capacity') == '2' ? 'selected' : '' }}>2+ personas</option>
                                    <option value="4" {{ request('min_capacity') == '4' ? 'selected' : '' }}>4+ personas</option>
                                    <option value="6" {{ request('min_capacity') == '6' ? 'selected' : '' }}>6+ personas</option>
                                    <option value="8" {{ request('min_capacity') == '8' ? 'selected' : '' }}>8+ personas</option>
                                </select>
                            </div>

                            <!-- Habitaciones y Baños -->
                            <div class="filter-section mb-4">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label fw-bold text-primary small">
                                            <i class="fas fa-bed me-1"></i>Habitaciones
                                        </label>
                                        <select name="min_bedrooms" class="form-select form-select-sm">
                                            <option value="">Cualquiera</option>
                                            <option value="1" {{ request('min_bedrooms') == '1' ? 'selected' : '' }}>1+</option>
                                            <option value="2" {{ request('min_bedrooms') == '2' ? 'selected' : '' }}>2+</option>
                                            <option value="3" {{ request('min_bedrooms') == '3' ? 'selected' : '' }}>3+</option>
                                            <option value="4" {{ request('min_bedrooms') == '4' ? 'selected' : '' }}>4+</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold text-primary small">
                                            <i class="fas fa-bath me-1"></i>Baños
                                        </label>
                                        <select name="min_bathrooms" class="form-select form-select-sm">
                                            <option value="">Cualquiera</option>
                                            <option value="1" {{ request('min_bathrooms') == '1' ? 'selected' : '' }}>1+</option>
                                            <option value="2" {{ request('min_bedrooms') == '2' ? 'selected' : '' }}>2+</option>
                                            <option value="3" {{ request('min_bedrooms') == '3' ? 'selected' : '' }}>3+</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Amenities con iconos -->
                            <div class="filter-section mb-4">
                                <label class="form-label fw-bold text-primary">
                                    <i class="fas fa-star me-2"></i>Amenities
                                </label>
                                <div class="amenities-grid">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="amenities[]" value="wifi" 
                                               {{ in_array('wifi', request('amenities', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            <i class="fas fa-wifi me-1"></i>WiFi
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="amenities[]" value="piscina" 
                                               {{ in_array('piscina', request('amenities', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            <i class="fas fa-swimming-pool me-1"></i>Piscina
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="amenities[]" value="aire_acondicionado" 
                                               {{ in_array('aire_acondicionado', request('amenities', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            <i class="fas fa-snowflake me-1"></i>Aire A/C
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="amenities[]" value="cocina" 
                                               {{ in_array('cocina', request('amenities', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label">
                                            <i class="fas fa-utensils me-1"></i>Cocina
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Mascotas y Estacionamiento -->
                            <div class="filter-section mb-4">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label fw-bold text-primary small">
                                            <i class="fas fa-paw me-1"></i>Mascotas
                                        </label>
                                        <select name="pet_friendly" class="form-select form-select-sm">
                                            <option value="">Cualquiera</option>
                                            <option value="true" {{ request('pet_friendly') === 'true' ? 'selected' : '' }}>Permite</option>
                                            <option value="false" {{ request('pet_friendly') === 'false' ? 'selected' : '' }}>No permite</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label fw-bold text-primary small">
                                            <i class="fas fa-parking me-1"></i>Estacionamiento
                                        </label>
                                        <select name="parking" class="form-select form-select-sm">
                                            <option value="">Cualquiera</option>
                                            <option value="gratis" {{ request('parking') == 'gratis' ? 'selected' : '' }}>Gratis</option>
                                            <option value="pago" {{ request('parking') == 'pago' ? 'selected' : '' }}>Pago</option>
                                            <option value="no" {{ request('parking') == 'no' ? 'selected' : '' }}>No disponible</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones de acción -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>Aplicar Filtros
                                </button>
                                <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Limpiar Filtros
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="col-lg-9">
            <!-- Header y Ordenamiento Mejorado -->
            <div class="content-header mb-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center mb-2">
                            <h2 class="h3 mb-0 me-3">
                                <i class="fas fa-home me-2 text-primary"></i>Propiedades Disponibles
                            </h2>
                            <span class="badge bg-primary fs-6">{{ $properties->total() }}</span>
                        </div>
                        <p class="text-muted mb-0">
                            @if(request()->hasAny(['search', 'type', 'department', 'city', 'min_price', 'max_price', 'min_capacity', 'min_bedrooms', 'min_bathrooms', 'amenities', 'pet_friendly', 'parking']))
                                <i class="fas fa-filter me-1"></i>Resultados filtrados
                            @else
                                <i class="fas fa-list me-1"></i>Todas las propiedades
                            @endif
                        </p>
                        
                        <!-- Filtros activos mejorados -->
                        @if(request()->hasAny(['search', 'type', 'department', 'city', 'min_price', 'max_price', 'min_capacity', 'min_bedrooms', 'min_bathrooms', 'amenities', 'pet_friendly', 'parking']))
                            <div class="active-filters mt-3">
                                <div class="d-flex flex-wrap gap-2">
                                    @if(request('search'))
                                        <span class="badge bg-primary d-flex align-items-center">
                                            <i class="fas fa-search me-1"></i>{{ request('search') }}
                                            <button type="button" class="btn-close btn-close-white ms-2" onclick="removeFilter('search')"></button>
                                        </span>
                                    @endif
                                    @if(request('type'))
                                        <span class="badge bg-info d-flex align-items-center">
                                            <i class="fas fa-building me-1"></i>{{ ucfirst(request('type')) }}
                                            <button type="button" class="btn-close btn-close-white ms-2" onclick="removeFilter('type')"></button>
                                        </span>
                                    @endif
                                    @if(request('department'))
                                        <span class="badge bg-secondary d-flex align-items-center">
                                            <i class="fas fa-map-marker-alt me-1"></i>{{ \App\Models\Department::find(request('department'))->name ?? 'N/A' }}
                                            <button type="button" class="btn-close btn-close-white ms-2" onclick="removeFilter('department')"></button>
                                        </span>
                                    @endif
                                    @if(request('city'))
                                        <span class="badge bg-secondary d-flex align-items-center">
                                            <i class="fas fa-city me-1"></i>{{ \App\Models\City::find(request('city'))->name ?? 'N/A' }}
                                            <button type="button" class="btn-close btn-close-white ms-2" onclick="removeFilter('city')"></button>
                                        </span>
                                    @endif
                                    @if(request('min_price') || request('max_price'))
                                        <span class="badge bg-warning text-dark d-flex align-items-center">
                                            <i class="fas fa-dollar-sign me-1"></i>${{ number_format(request('min_price', 0)) }} - ${{ number_format(request('max_price', 999999)) }}
                                            <button type="button" class="btn-close btn-close-white ms-2" onclick="removeFilter('price')"></button>
                                        </span>
                                    @endif
                                    @if(request('min_capacity'))
                                        <span class="badge bg-success d-flex align-items-center">
                                            <i class="fas fa-users me-1"></i>{{ request('min_capacity') }}+ personas
                                            <button type="button" class="btn-close btn-close-white ms-2" onclick="removeFilter('min_capacity')"></button>
                                        </span>
                                    @endif
                                    @if(request('min_bedrooms'))
                                        <span class="badge bg-info d-flex align-items-center">
                                            <i class="fas fa-bed me-1"></i>{{ request('min_bedrooms') }}+ habitaciones
                                            <button type="button" class="btn-close btn-close-white ms-2" onclick="removeFilter('min_bedrooms')"></button>
                                        </span>
                                    @endif
                                    @if(request('min_bathrooms'))
                                        <span class="badge bg-info d-flex align-items-center">
                                            <i class="fas fa-bath me-1"></i>{{ request('min_bathrooms') }}+ baños
                                            <button type="button" class="btn-close btn-close-white ms-2" onclick="removeFilter('min_bathrooms')"></button>
                                        </span>
                                    @endif
                                    @if(request('amenities'))
                                        @foreach(request('amenities') as $amenity)
                                            <span class="badge bg-light text-dark border d-flex align-items-center">
                                                <i class="fas fa-check me-1"></i>{{ ucfirst(str_replace('_', ' ', $amenity)) }}
                                                <button type="button" class="btn-close ms-2" onclick="removeAmenity('{{ $amenity }}')"></button>
                                            </span>
                                        @endforeach
                                    @endif
                                    @if(request('pet_friendly') !== null)
                                        <span class="badge bg-success d-flex align-items-center">
                                            <i class="fas fa-paw me-1"></i>{{ request('pet_friendly') === 'true' ? 'Mascotas permitidas' : 'No mascotas' }}
                                            <button type="button" class="btn-close btn-close-white ms-2" onclick="removeFilter('pet_friendly')"></button>
                                        </span>
                                    @endif
                                    @if(request('parking'))
                                        <span class="badge bg-info d-flex align-items-center">
                                            <i class="fas fa-parking me-1"></i>{{ ucfirst(request('parking')) }}
                                            <button type="button" class="btn-close btn-close-white ms-2" onclick="removeFilter('parking')"></button>
                                        </span>
                                    @endif
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('properties.index') }}" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-times me-1"></i>Limpiar todos los filtros
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="col-md-4 text-md-end">
                        <div class="sorting-controls">
                            <form method="GET" action="{{ route('properties.index') }}" id="sortForm" class="d-flex align-items-center gap-2">
                                <!-- Preservar filtros existentes -->
                                @if(request('department'))
                                    <input type="hidden" name="department" value="{{ request('department') }}">
                                @endif
                                @if(request('city'))
                                    <input type="hidden" name="city" value="{{ request('city') }}">
                                @endif
                                @if(request('type'))
                                    <input type="hidden" name="type" value="{{ request('type') }}">
                                @endif
                                @if(request('min_price'))
                                    <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                                @endif
                                @if(request('max_price'))
                                    <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                                @endif
                                @if(request('min_capacity'))
                                    <input type="hidden" name="min_capacity" value="{{ request('min_capacity') }}">
                                @endif
                                @if(request('min_bedrooms'))
                                    <input type="hidden" name="min_bedrooms" value="{{ request('min_bedrooms') }}">
                                @endif
                                @if(request('min_bathrooms'))
                                    <input type="hidden" name="min_bathrooms" value="{{ request('min_bathrooms') }}">
                                @endif
                                @if(request('amenities'))
                                    @foreach(request('amenities') as $amenity)
                                        <input type="hidden" name="amenities[]" value="{{ $amenity }}">
                                    @endforeach
                                @endif
                                @if(request('pet_friendly'))
                                    <input type="hidden" name="pet_friendly" value="{{ request('pet_friendly') }}">
                                @endif
                                @if(request('parking'))
                                    <input type="hidden" name="parking" value="{{ request('parking') }}">
                                @endif
                                
                                <label class="form-label fw-bold text-primary mb-0 me-2">
                                    <i class="fas fa-sort me-1"></i>Ordenar por:
                                </label>
                                <select name="sort" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Más recientes</option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Precio: menor a mayor</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Precio: mayor a menor</option>
                                    <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Mejor calificadas</option>
                                    <option value="featured" {{ request('sort') == 'featured' ? 'selected' : '' }}>Destacadas</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Propiedades Mejorada -->
            @if($properties->count() > 0)
                <div class="properties-grid">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                        @foreach($properties as $property)
                            <div class="col">
                                <div class="property-card-modern h-100">
                                    <!-- Imagen de la propiedad -->
                                    <div class="property-image-wrapper">
                                        @if($property->primaryImage)
                                            <img src="{{ $property->primaryImage->full_url }}" 
                                                 class="property-image" 
                                                 alt="{{ $property->primaryImage->alt_text ?? $property->name }}"
                                                 loading="lazy">
                                        @else
                                            <div class="property-placeholder-modern">
                                                <div class="placeholder-content">
                                                    <i class="fas fa-home"></i>
                                                    <span>Sin imagen</span>
                                                </div>
                                            </div>
                                        @endif
                                        
                                        <!-- Overlay con información rápida -->
                                        <div class="property-overlay">
                                            <div class="overlay-content">
                                                <div class="overlay-badges">
                                                    @if($property->isFeatured())
                                                        <span class="badge badge-featured">
                                                            <i class="fas fa-star me-1"></i>Destacada
                                                        </span>
                                                    @endif
                                                    <span class="badge badge-type">{{ ucfirst($property->type) }}</span>
                                                </div>
                                                
                                                <!-- Botón de favorito mejorado -->
                                                <button class="favorite-btn-modern" data-property-id="{{ $property->id }}">
                                                    <i class="far fa-heart"></i>
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Precio flotante -->
                                        <div class="price-tag">
                                            <div class="price-amount">{{ $property->formatted_price }}</div>
                                            <div class="price-period">por noche</div>
                                        </div>
                                    </div>

                                    <div class="property-content">
                                        <!-- Header de la tarjeta -->
                                        <div class="property-header">
                                            <h5 class="property-title">{{ $property->name }}</h5>
                                            <div class="property-rating">
                                                @if($property->rating > 0)
                                                    <div class="stars">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $property->rating)
                                                                <i class="fas fa-star filled"></i>
                                                            @elseif($i - $property->rating < 1)
                                                                <i class="fas fa-star-half-alt filled"></i>
                                                            @else
                                                                <i class="far fa-star"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                    <span class="rating-text">{{ $property->rating }}/5</span>
                                                    @if($property->review_count > 0)
                                                        <span class="review-count">({{ $property->review_count }} reseñas)</span>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Ubicación -->
                                        <div class="property-location">
                                            <i class="fas fa-map-marker-alt"></i>
                                            @if($property->city)
                                                <strong>{{ $property->city->name }}</strong>, {{ $property->city->department->name }}
                                            @else
                                                {{ $property->location }}
                                            @endif
                                        </div>

                                        <!-- Descripción -->
                                        <p class="property-description">{{ Str::limit($property->description, 100) }}</p>
                                        
                                        <!-- Características principales -->
                                        <div class="property-features">
                                            <div class="feature-item-modern">
                                                <div class="feature-icon">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                                <div class="feature-info">
                                                    <span class="feature-value">{{ $property->capacity }}</span>
                                                    <span class="feature-label">Personas</span>
                                                </div>
                                            </div>
                                            <div class="feature-item-modern">
                                                <div class="feature-icon">
                                                    <i class="fas fa-bed"></i>
                                                </div>
                                                <div class="feature-info">
                                                    <span class="feature-value">{{ $property->bedrooms ?? 'N/A' }}</span>
                                                    <span class="feature-label">Habitaciones</span>
                                                </div>
                                            </div>
                                            <div class="feature-item-modern">
                                                <div class="feature-icon">
                                                    <i class="fas fa-bath"></i>
                                                </div>
                                                <div class="feature-info">
                                                    <span class="feature-value">{{ $property->bathrooms ?? 'N/A' }}</span>
                                                    <span class="feature-label">Baños</span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Amenities destacados -->
                                        @if($property->amenities && count($property->amenities) > 0)
                                            <div class="property-amenities">
                                                <div class="amenities-grid-modern">
                                                    @foreach(array_slice($property->amenities, 0, 3) as $amenity)
                                                        <span class="amenity-tag">
                                                            <i class="fas fa-check"></i>
                                                            {{ ucfirst(str_replace('_', ' ', $amenity)) }}
                                                        </span>
                                                    @endforeach
                                                    @if(count($property->amenities) > 3)
                                                        <span class="amenity-tag more">
                                                            +{{ count($property->amenities) - 3 }} más
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Botón de acción -->
                                        <div class="property-actions">
                                            <a href="{{ route('properties.show', $property) }}" 
                                               class="btn btn-primary btn-view-details">
                                                <i class="fas fa-eye me-2"></i>Ver Detalles
                                            </a>
                                            <button class="btn btn-outline-primary btn-quick-view" 
                                                    data-property-id="{{ $property->id }}">
                                                <i class="fas fa-search me-2"></i>Vista Rápida
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Paginación -->
                @if($properties->hasPages())
                    <div class="row mt-5">
                        <div class="col-12">
                            <nav aria-label="Navegación de propiedades" class="d-flex justify-content-center">
                                {{ $properties->links() }}
                            </nav>
                        </div>
                    </div>
                @endif
            @else
                <!-- Estado vacío -->
                <div class="text-center py-5">
                    <div class="empty-state">
                        <div class="empty-state-icon mb-4">
                            <i class="fas fa-search"></i>
                        </div>
                        <h4 class="mb-3">No se encontraron propiedades</h4>
                        <p class="text-muted mb-4">
                            @if(request()->hasAny(['type', 'department', 'city', 'min_price', 'max_price', 'min_capacity', 'min_bedrooms', 'min_bathrooms', 'amenities', 'pet_friendly', 'parking']))
                                Intenta ajustar los filtros de búsqueda para encontrar más opciones.
                            @else
                                No hay propiedades disponibles en este momento.
                            @endif
                        </p>
                        <a href="{{ route('properties.index') }}" class="btn btn-primary">
                            <i class="fas fa-refresh me-2"></i>Ver todas las propiedades
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Estilos CSS personalizados mejorados -->
<style>
/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    overflow: hidden;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.search-form .input-group {
    border-radius: 50px;
    overflow: hidden;
}

.search-form .form-control {
    border: none;
    padding: 1rem 1.5rem;
}

.search-form .btn {
    border-radius: 0 50px 50px 0;
    padding: 1rem 2rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

/* Filtros Sidebar */
.filters-sidebar .card {
    border-radius: 16px;
    overflow: hidden;
}

.filters-sidebar .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 1.5rem;
}

.filter-section {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 1rem;
}

.filter-section:last-child {
    border-bottom: none;
}

.filter-section label {
    color: #495057;
    margin-bottom: 0.75rem;
}

.type-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.type-buttons .btn {
    border-radius: 20px;
    font-size: 0.85rem;
    padding: 0.5rem 1rem;
}

.amenities-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
}

.amenities-grid .form-check {
    margin: 0;
}

.amenities-grid .form-check-label {
    font-size: 0.9rem;
}

/* Content Header */
.content-header {
    background: #fff;
    padding: 1.5rem;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border: 1px solid #e9ecef;
}

.active-filters .badge {
    font-size: 0.85rem;
    padding: 0.5rem 0.75rem;
    border-radius: 20px;
}

.active-filters .btn-close {
    font-size: 0.7rem;
    opacity: 0.8;
}

.sorting-controls {
    display: flex;
    align-items: center;
    gap: 1rem;
}

/* Tarjetas de Propiedades Modernas */
.properties-grid {
    margin-top: 2rem;
}

.property-card-modern {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.property-card-modern:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
}

.property-image-wrapper {
    position: relative;
    height: 280px;
    overflow: hidden;
}

.property-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.property-card-modern:hover .property-image {
    transform: scale(1.1);
}

.property-placeholder-modern {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.placeholder-content {
    text-align: center;
    color: #6c757d;
}

.placeholder-content i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.6;
}

.placeholder-content span {
    font-size: 1rem;
    font-weight: 500;
}

/* Overlay de la imagen */
.property-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, transparent 30%, transparent 70%, rgba(0,0,0,0.2) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.property-card-modern:hover .property-overlay {
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

.overlay-badges {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.badge-featured {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
    color: #000;
    border: none;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 20px;
}

.badge-type {
    background: rgba(102, 126, 234, 0.9);
    color: white;
    border: none;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 20px;
}

.favorite-btn-modern {
    background: rgba(255, 255, 255, 0.9);
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.favorite-btn-modern:hover {
    background: #dc3545;
    color: white;
    transform: scale(1.1);
}

.favorite-btn-modern.active {
    background: #dc3545;
    color: white;
}

/* Precio flotante */
.price-tag {
    position: absolute;
    bottom: 1rem;
    right: 1rem;
    background: rgba(255, 255, 255, 0.95);
    padding: 0.75rem 1rem;
    border-radius: 20px;
    text-align: center;
    backdrop-filter: blur(10px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.price-amount {
    font-size: 1.25rem;
    font-weight: bold;
    color: #667eea;
    margin-bottom: 0.25rem;
}

.price-period {
    font-size: 0.8rem;
    color: #6c757d;
}

/* Contenido de la tarjeta */
.property-content {
    padding: 1.5rem;
}

.property-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.property-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    line-height: 1.3;
    flex: 1;
    margin-right: 1rem;
}

.property-rating {
    text-align: right;
    min-width: fit-content;
}

.stars {
    margin-bottom: 0.25rem;
}

.stars i {
    font-size: 0.9rem;
    color: #ffc107;
}

.stars i.filled {
    color: #ffc107;
}

.rating-text {
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
}

.review-count {
    font-size: 0.8rem;
    color: #6c757d;
}

.property-location {
    color: #6c757d;
    margin-bottom: 1rem;
    font-size: 0.95rem;
}

.property-location i {
    color: #667eea;
    margin-right: 0.5rem;
}

.property-location strong {
    color: #495057;
}

.property-description {
    color: #6c757d;
    margin-bottom: 1.5rem;
    line-height: 1.6;
    font-size: 0.95rem;
}

/* Características */
.property-features {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.feature-item-modern {
    text-align: center;
    padding: 1rem 0.5rem;
    background: #f8f9fa;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.feature-item-modern:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.feature-icon {
    margin-bottom: 0.5rem;
}

.feature-icon i {
    font-size: 1.5rem;
    color: #667eea;
}

.feature-value {
    display: block;
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.feature-label {
    font-size: 0.8rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Amenities */
.property-amenities {
    margin-bottom: 1.5rem;
}

.amenities-grid-modern {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.amenity-tag {
    background: #e3f2fd;
    color: #1976d2;
    padding: 0.4rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.amenity-tag i {
    font-size: 0.7rem;
}

.amenity-tag.more {
    background: #f3e5f5;
    color: #7b1fa2;
}

/* Botones de acción */
.property-actions {
    display: flex;
    gap: 0.75rem;
}

.btn-view-details {
    flex: 2;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-quick-view {
    flex: 1;
    border-radius: 12px;
    padding: 0.75rem 0.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-view-details:hover,
.btn-quick-view:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* Estado vacío */
.empty-state {
    max-width: 400px;
    margin: 0 auto;
    text-align: center;
    padding: 3rem 1rem;
}

.empty-state-icon {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 1.5rem;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-section {
        padding: 3rem 1rem !important;
    }
    
    .display-4 {
        font-size: 2rem;
    }
    
    .property-image-wrapper {
        height: 220px;
    }
    
    .property-content {
        padding: 1rem;
    }
    
    .property-features {
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
    }
    
    .property-actions {
        flex-direction: column;
    }
    
    .content-header {
        padding: 1rem;
    }
    
    .sorting-controls {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }
    
    .type-buttons {
        justify-content: center;
    }
    
    .amenities-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 576px) {
    .stat-item {
        padding: 0.5rem;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    .property-features {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .feature-item-modern {
        display: flex;
        align-items: center;
        text-align: left;
        padding: 0.75rem;
    }
    
    .feature-icon {
        margin-bottom: 0;
        margin-right: 1rem;
    }
}
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const departmentSelect = document.getElementById('department');
    const citySelect = document.getElementById('city');
    const filterForm = document.getElementById('filterForm');
    
    // Cargar ciudades cuando cambie el departamento
    departmentSelect.addEventListener('change', function() {
        const departmentId = this.value;
        
        // Limpiar ciudades
        citySelect.innerHTML = '<option value="">Todas las ciudades</option>';
        citySelect.disabled = !departmentId;
        
        if (departmentId) {
            // Cargar ciudades del departamento seleccionado
            fetch(`/api/cities/by-department?department_id=${departmentId}`)
                .then(response => response.json())
                .then(cities => {
                    cities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.id;
                        option.textContent = city.name;
                        citySelect.appendChild(option);
                    });
                    citySelect.disabled = false;
                })
                .catch(error => {
                    console.error('Error cargando ciudades:', error);
                });
        }
    });
    
    // Función para mostrar indicador de carga
    function showLoading() {
        document.getElementById('loadingIndicator').classList.remove('d-none');
        // Scroll suave hacia arriba para mostrar el indicador
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    // Auto-submit form when filters change (except department and amenities)
    const filterInputs = filterForm.querySelectorAll('select[name!="department"], input[type="text"], input[type="number"]');
    
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.name !== 'department') {
                showLoading();
                filterForm.submit();
            }
        });
    });
    
    // Manejar checkboxes de amenities
    const amenityCheckboxes = filterForm.querySelectorAll('input[name="amenities[]"]');
    amenityCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Pequeño delay para permitir que se marque/desmarque
            setTimeout(() => {
                showLoading();
                filterForm.submit();
            }, 100);
        });
    });
    
    // Manejar botones de favorito modernos
    document.querySelectorAll('.favorite-btn-modern').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const propertyId = this.dataset.propertyId;
            const icon = this.querySelector('i');
            
            // Toggle visual
            this.classList.toggle('active');
            if (this.classList.contains('active')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                // Animación de corazón
                this.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 200);
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
            }
            
            // Aquí puedes agregar la lógica para guardar en favoritos
            console.log('Propiedad', propertyId, 'agregada a favoritos');
        });
    });
    
    // Manejar botones de vista rápida
    document.querySelectorAll('.btn-quick-view').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const propertyId = this.dataset.propertyId;
            
            // Aquí puedes implementar un modal de vista rápida
            console.log('Vista rápida de propiedad:', propertyId);
            
            // Ejemplo de modal básico (puedes usar Bootstrap Modal)
            showQuickViewModal(propertyId);
        });
    });
    
    // Función para mostrar modal de vista rápida
    function showQuickViewModal(propertyId) {
        // Crear modal básico
        const modal = document.createElement('div');
        modal.className = 'quick-view-modal';
        modal.innerHTML = `
            <div class="modal-overlay">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Vista Rápida</h5>
                        <button class="close-btn">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>Vista rápida de la propiedad ${propertyId}</p>
                        <p>Aquí puedes mostrar información básica de la propiedad.</p>
                    </div>
                    <div class="modal-footer">
                        <a href="/properties/${propertyId}" class="btn btn-primary">Ver Detalles Completos</a>
                        <button class="btn btn-secondary close-btn">Cerrar</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Cerrar modal
        modal.querySelectorAll('.close-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                modal.remove();
            });
        });
        
        // Cerrar al hacer clic fuera del modal
        modal.querySelector('.modal-overlay').addEventListener('click', (e) => {
            if (e.target === modal.querySelector('.modal-overlay')) {
                modal.remove();
            }
        });
    }
    
    // Búsqueda en tiempo real con debounce
    const searchInput = document.getElementById('searchInput');
    let searchTimeout;
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                // Implementar búsqueda en tiempo real si es necesario
                console.log('Buscando:', this.value);
            }, 300);
        });
    }
    
    // Funciones para remover filtros individuales
    window.removeFilter = function(filterName) {
        const url = new URL(window.location);
        if (filterName === 'price') {
            url.searchParams.delete('min_price');
            url.searchParams.delete('max_price');
        } else {
            url.searchParams.delete(filterName);
        }
        window.location.href = url.toString();
    };
    
    window.removeAmenity = function(amenity) {
        const url = new URL(window.location);
        const amenities = url.searchParams.getAll('amenities[]');
        const newAmenities = amenities.filter(a => a !== amenity);
        
        url.searchParams.delete('amenities[]');
        newAmenities.forEach(a => url.searchParams.append('amenities[]', a));
        
        window.location.href = url.toString();
    };
    
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
    
    // Observar todas las tarjetas de propiedades
    document.querySelectorAll('.property-card-modern').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
    
    // Smooth scroll para el botón de búsqueda
    const heroSearchForm = document.getElementById('heroSearchForm');
    if (heroSearchForm) {
        heroSearchForm.addEventListener('submit', function(e) {
            const searchValue = this.querySelector('#searchInput').value.trim();
            if (searchValue) {
                // Scroll suave a los resultados
                setTimeout(() => {
                    const resultsSection = document.querySelector('.properties-grid');
                    if (resultsSection) {
                        resultsSection.scrollIntoView({ 
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }, 100);
            }
        });
    }
});
</script>

<!-- Estilos para el modal de vista rápida -->
<style>
.quick-view-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1050;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.modal-content {
    background: white;
    border-radius: 16px;
    max-width: 500px;
    width: 100%;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-header {
    padding: 1.5rem 1.5rem 1rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h5 {
    margin: 0;
    color: #2c3e50;
}

.close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #6c757d;
    cursor: pointer;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.3s ease;
}

.close-btn:hover {
    background: #f8f9fa;
    color: #495057;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1rem 1.5rem 1.5rem;
    border-top: 1px solid #e9ecef;
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
}
</style>
@endpush
