@extends('layouts.app')

@section('title', 'Propiedades - Reservalo')

@section('content')
<div class="container-fluid py-4">
         <!-- Hero Section Mejorado -->
     <div class="row mb-5">
         <div class="col-12">
             <div class="hero-section text-white rounded-4 p-5 text-center position-relative overflow-hidden">
                 <div class="hero-content position-relative">
                     <div class="hero-badge mb-4">
                         <span class="badge bg-white bg-opacity-20 px-3 py-2 rounded-pill">
                             <i class="fas fa-star me-2"></i>Destinos Turísticos Premium
                         </span>
                     </div>
                     
                     <h1 class="display-4 fw-bold mb-3">
                         <i class="fas fa-home me-3 hero-icon"></i>Encuentra tu lugar perfecto
                     </h1>
                     <p class="lead mb-4">Descubre las mejores propiedades turísticas en Colombia</p>
                     
                     <!-- Búsqueda rápida -->
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
                     <div class="hero-stats mt-5">
                         <div class="row justify-content-center">
                             <div class="col-md-3 col-6">
                                 <div class="stat-item">
                                     <div class="stat-number">{{ $properties->total() }}+</div>
                                     <div class="stat-label">Propiedades</div>
                                 </div>
                             </div>
                             <div class="col-md-3 col-6">
                                 <div class="stat-item">
                                     <div class="stat-number">32</div>
                                     <div class="stat-label">Departamentos</div>
                                 </div>
                             </div>
                             <div class="col-md-3 col-6">
                                 <div class="stat-item">
                                     <div class="stat-number">1000+</div>
                                     <div class="stat-label">Ciudades</div>
                                 </div>
                             </div>
                             <div class="col-md-3 col-6">
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
     </div>

    <div class="row">
        <!-- Sidebar de Filtros -->
        <div class="col-lg-3 mb-4">
            <div class="filters-sidebar">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white border-0">
                        <h5 class="mb-0">
                            <i class="fas fa-filter me-2"></i>Filtros
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('properties.index') }}" id="filterForm">
                                                         <!-- Búsqueda -->
                             <div class="mb-4">
                                 <label class="form-label fw-bold mb-3">
                                     <i class="fas fa-map-marker-alt me-2 text-primary"></i>Ubicación
                                 </label>
                                 <div class="location-filters">
                                     <div class="form-floating mb-3">
                                         <select name="department" id="department" class="form-select location-select">
                                             <option value="">Selecciona un departamento</option>
                                             @foreach(\App\Models\Department::active()->ordered()->get() as $dept)
                                                 <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                                     {{ $dept->name }}
                                                 </option>
                                             @endforeach
                                         </select>
                                         <label for="department">
                                             <i class="fas fa-map me-1"></i>Departamento
                                         </label>
                                     </div>
                                     <div class="form-floating">
                                         <select name="city" id="city" class="form-select location-select" {{ !request('department') ? 'disabled' : '' }}>
                                             <option value="">Selecciona una ciudad</option>
                                             @if(request('department'))
                                                 @foreach(\App\Models\City::active()->byDepartment(request('department'))->ordered()->get() as $city)
                                                     <option value="{{ $city->id }}" {{ request('city') == $city->id ? 'selected' : '' }}>
                                                         {{ $city->name }}
                                                     </option>
                                                 @endforeach
                                             @endif
                                         </select>
                                         <label for="city">
                                             <i class="fas fa-city me-1"></i>Ciudad
                                         </label>
                                     </div>
                                 </div>
                             </div>

                                                         <!-- Tipo de Propiedad -->
                             <div class="mb-4">
                                 <label class="form-label fw-bold mb-3">
                                     <i class="fas fa-building me-2 text-primary"></i>Tipo de Propiedad
                                 </label>
                                 <div class="d-flex flex-wrap gap-2">
                                     <input type="radio" class="btn-check" name="type" id="type_all" value="" {{ !request('type') ? 'checked' : '' }}>
                                     <label class="btn btn-outline-primary btn-sm filter-type-btn" for="type_all">
                                         <i class="fas fa-th-large me-1"></i>Todos
                                     </label>
                                     
                                     <input type="radio" class="btn-check" name="type" id="type_casa" value="casa" {{ request('type') == 'casa' ? 'checked' : '' }}>
                                     <label class="btn btn-outline-primary btn-sm filter-type-btn" for="type_casa">
                                         <i class="fas fa-home me-1"></i>Casa
                                     </label>
                                     
                                     <input type="radio" class="btn-check" name="type" id="type_apartamento" value="apartamento" {{ request('type') == 'apartamento' ? 'checked' : '' }}>
                                     <label class="btn btn-outline-primary btn-sm filter-type-btn" for="type_apartamento">
                                         <i class="fas fa-building me-1"></i>Apto
                                     </label>
                                 </div>
                             </div>

                                                         <!-- Precio -->
                             <div class="mb-4">
                                 <label class="form-label fw-bold mb-3">
                                     <i class="fas fa-dollar-sign me-2 text-primary"></i>Rango de Precio
                                 </label>
                                 <div class="price-range-wrapper">
                                     <div class="row g-3">
                                         <div class="col-6">
                                             <div class="form-floating">
                                                 <input type="number" class="form-control price-input" 
                                                        name="min_price" id="min_price"
                                                        value="{{ request('min_price') }}" 
                                                        placeholder="Precio mínimo" min="0">
                                                 <label for="min_price">
                                                     <i class="fas fa-arrow-down me-1"></i>Mínimo
                                                 </label>
                                             </div>
                                         </div>
                                         <div class="col-6">
                                             <div class="form-floating">
                                                 <input type="number" class="form-control price-input" 
                                                        name="max_price" id="max_price"
                                                        value="{{ request('max_price') }}" 
                                                        placeholder="Precio máximo" min="0">
                                                 <label for="max_price">
                                                     <i class="fas fa-arrow-up me-1"></i>Máximo
                                                 </label>
                                             </div>
                                         </div>
                                     </div>
                                     <div class="price-slider mt-3">
                                         <div class="price-display">
                                             <span class="price-min">$0</span>
                                             <span class="price-max">$1,000,000+</span>
                                         </div>
                                     </div>
                                 </div>
                             </div>

                                                         <!-- Botones de acción -->
                             <div class="d-grid gap-3">
                                 <button type="submit" class="btn btn-primary filter-action-btn">
                                     <i class="fas fa-search me-2"></i>Aplicar Filtros
                                 </button>
                                 <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary filter-action-btn">
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
                         <!-- Header Mejorado -->
             <div class="content-header mb-4">
                 <div class="row align-items-center">
                     <div class="col-md-8">
                         <div class="d-flex align-items-center mb-2">
                             <div class="header-icon-wrapper me-3">
                                 <i class="fas fa-home"></i>
                             </div>
                             <div>
                                 <h2 class="h3 mb-0 fw-bold">
                                     Propiedades Disponibles
                                 </h2>
                                 <p class="text-muted mb-0">
                                     <i class="fas fa-search me-1"></i>
                                     {{ $properties->total() }} propiedades encontradas
                                 </p>
                             </div>
                         </div>
                     </div>
                     
                     <div class="col-md-4 text-md-end">
                         <form method="GET" action="{{ route('properties.index') }}" id="sortForm">
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
                             
                             <div class="sort-wrapper">
                                 <label for="sortSelect" class="form-label fw-bold text-muted mb-2 d-block">
                                     <i class="fas fa-sort-amount-down me-1"></i>Ordenar por
                                 </label>
                                 <select name="sort" id="sortSelect" class="form-select sort-select" onchange="this.form.submit()">
                                     <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Más recientes</option>
                                     <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Precio: menor a mayor</option>
                                     <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Precio: mayor a menor</option>
                                 </select>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>

            <!-- Lista de Propiedades -->
            @if($properties->count() > 0)
                <div class="properties-grid">
                    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                        @foreach($properties as $property)
                            <div class="col">
                                <div class="property-card h-100">
                                    <!-- Imagen de la propiedad -->
                                    <div class="property-image-wrapper">
                                        @if($property->primaryImage)
                                            <img src="{{ $property->primaryImage->full_url }}" 
                                                 class="property-image" 
                                                 alt="{{ $property->primaryImage->alt_text ?? $property->name }}"
                                                 loading="lazy">
                                        @else
                                            <div class="property-placeholder">
                                                <i class="fas fa-home"></i>
                                                <span>Sin imagen</span>
                                            </div>
                                        @endif
                                        
                                        <!-- Botón de favorito -->
                                        <button class="favorite-btn" 
                                                data-property-id="{{ $property->id }}"
                                                onclick="toggleFavorite(this, {{ $property->id }})"
                                                title="{{ auth()->check() ? 'Agregar a favoritos' : 'Inicia sesión para agregar a favoritos' }}">
                                            <i class="far fa-heart"></i>
                                            @if(!auth()->check())
                                                <span class="login-required-indicator">🔒</span>
                                            @endif
                                        </button>
                                        
                                        <!-- Precio -->
                                        <div class="price-tag">
                                            <div class="price-amount">{{ $property->formatted_price }}</div>
                                            <div class="price-period">por noche</div>
                                        </div>
                                    </div>

                                    <div class="property-content">
                                        <!-- Título -->
                                        <h5 class="property-title">{{ $property->name }}</h5>
                                        
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
                                        
                                        <!-- Características -->
                                        <div class="property-features">
                                            <div class="feature-item">
                                                <i class="fas fa-users"></i>
                                                <span>{{ $property->capacity }} personas</span>
                                            </div>
                                            <div class="feature-item">
                                                <i class="fas fa-bed"></i>
                                                <span>{{ $property->bedrooms ?? 'N/A' }} habitaciones</span>
                                            </div>
                                            <div class="feature-item">
                                                <i class="fas fa-bath"></i>
                                                <span>{{ $property->bathrooms ?? 'N/A' }} baños</span>
                                            </div>
                                        </div>

                                        <!-- Botones de acción -->
                                        <div class="property-actions">
                                            <a href="{{ route('properties.show', $property) }}" 
                                               class="btn btn-primary btn-view-details">
                                                <i class="fas fa-eye me-2"></i>Ver Detalles
                                            </a>
                                            <button class="btn btn-outline-primary btn-quick-view" 
                                                    onclick="showQuickView({{ $property->id }})">
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
                            Intenta ajustar los filtros de búsqueda para encontrar más opciones.
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

<!-- Estilos CSS Mejorados -->
<style>
/* Hero Section Mejorado */
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    position: relative;
    border-radius: 20px;
    margin-bottom: 3rem;
    padding: 4rem 2rem !important;
}

.hero-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.hero-content {
    position: relative;
    z-index: 2;
}

.hero-badge {
    animation: fadeInDown 1s ease-out;
}

.hero-badge .badge {
    font-size: 0.9rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.hero-icon {
    animation: bounce 2s infinite;
    display: inline-block;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
    40% { transform: translateY(-10px); }
    60% { transform: translateY(-5px); }
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hero-stats {
    animation: fadeInUp 1s ease-out 0.5s both;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.stat-item:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.stat-number {
    font-size: 2rem;
    font-weight: 900;
    color: white;
    margin-bottom: 0.5rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-label {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Filtros Mejorados */
.filters-sidebar .card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.filters-sidebar .card-header {
    border-radius: 20px 20px 0 0 !important;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    padding: 1.5rem;
}

.filters-sidebar .form-select {
    border-radius: 12px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.filters-sidebar .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.filters-sidebar .btn {
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.filters-sidebar .btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.filters-sidebar .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

/* Tarjetas de Propiedades Mejoradas */
.property-card {
    background: #fff;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: none;
    position: relative;
}

.property-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 20px 50px rgba(0,0,0,0.15);
}

.property-image-wrapper {
    position: relative;
    height: 280px;
    overflow: hidden;
    background: linear-gradient(45deg, #f8f9fa, #e9ecef);
}

.property-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.property-card:hover .property-image {
    transform: scale(1.15);
}

.property-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, #f8f9fa, #e9ecef);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.property-placeholder i {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.4;
    color: #667eea;
}

/* Botón de Favorito Mejorado */
.favorite-btn {
    position: absolute !important;
    top: 1rem;
    right: 1rem;
    background: rgba(255, 255, 255, 0.95);
    border: 2px solid rgba(255, 255, 255, 0.8);
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #dc3545;
    transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    backdrop-filter: blur(10px);
    z-index: 10;
    font-size: 1.1rem;
}

.favorite-btn:hover {
    background: #dc3545;
    color: white;
    transform: scale(1.15);
    border-color: #dc3545;
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
}

.favorite-btn.active {
    background: #dc3545;
    color: white;
    border-color: #dc3545;
    animation: heartBeat 0.6s ease-in-out;
}

@keyframes heartBeat {
    0% { transform: scale(1); }
    14% { transform: scale(1.3); }
    28% { transform: scale(1); }
    42% { transform: scale(1.3); }
    70% { transform: scale(1); }
}

.login-required-indicator {
    position: absolute;
    top: -8px;
    right: -8px;
    font-size: 0.8rem;
    opacity: 0.9;
    pointer-events: none;
    background: #ffc107;
    color: #000;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

/* Precio Mejorado */
.price-tag {
    position: absolute;
    bottom: 1rem;
    left: 1rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.75rem 1.25rem;
    border-radius: 25px;
    text-align: center;
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.price-amount {
    font-size: 1.4rem;
    font-weight: 800;
    color: white;
    margin-bottom: 0.25rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.price-period {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
}

/* Contenido de la Tarjeta */
.property-content {
    padding: 2rem;
    background: linear-gradient(180deg, #fff 0%, #f8f9fa 100%);
}

.property-title {
    font-size: 1.3rem;
    font-weight: 800;
    color: #2c3e50;
    margin-bottom: 1rem;
    line-height: 1.3;
    text-shadow: 0 1px 2px rgba(0,0,0,0.05);
}

.property-location {
    color: #6c757d;
    margin-bottom: 1.25rem;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.property-location i {
    color: #667eea;
    font-size: 1.1rem;
}

.property-description {
    color: #6c757d;
    margin-bottom: 1.75rem;
    line-height: 1.7;
    font-size: 0.95rem;
    font-weight: 400;
}

/* Características Mejoradas */
.property-features {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.feature-item {
    text-align: center;
    padding: 1.25rem 0.75rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 16px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.feature-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
    transition: left 0.5s ease;
}

.feature-item:hover::before {
    left: 100%;
}

.feature-item:hover {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    transform: translateY(-4px);
    border-color: #667eea;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
}

.feature-item i {
    font-size: 1.6rem;
    color: #667eea;
    margin-bottom: 0.75rem;
    display: block;
    transition: all 0.3s ease;
}

.feature-item:hover i {
    transform: scale(1.1);
    color: #5a6fd8;
}

.feature-item span {
    font-size: 0.85rem;
    color: #495057;
    font-weight: 600;
    display: block;
}

/* Botones de Acción Mejorados */
.property-actions {
    display: flex;
    gap: 1rem;
}

.btn-view-details {
    flex: 2;
    border-radius: 16px;
    padding: 1rem 1.5rem;
    font-weight: 700;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.9rem;
}

.btn-view-details:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
    color: white;
}

.btn-quick-view {
    flex: 1;
    border-radius: 16px;
    padding: 1rem 1rem;
    font-weight: 600;
    border: 2px solid #667eea;
    color: #667eea;
    background: transparent;
    transition: all 0.3s ease;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.85rem;
}

.btn-quick-view:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

/* Estado Vacío Mejorado */
.empty-state {
    max-width: 500px;
    margin: 0 auto;
    text-align: center;
    padding: 4rem 2rem;
}

.empty-state-icon {
    font-size: 5rem;
    color: #dee2e6;
    margin-bottom: 2rem;
    opacity: 0.6;
}

/* Header de Contenido */
.content-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 2rem;
    border-radius: 20px;
    margin-bottom: 2rem;
    border: 2px solid #e9ecef;
}

.content-header h2 {
    color: #2c3e50;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.content-header .text-primary {
    color: #667eea !important;
}

/* Paginación Mejorada */
.pagination {
    gap: 0.5rem;
}

.page-link {
    border-radius: 12px;
    border: 2px solid #e9ecef;
    color: #667eea;
    font-weight: 600;
    transition: all 0.3s ease;
}

.page-link:hover {
    background: #667eea;
    border-color: #667eea;
    color: white;
    transform: translateY(-2px);
}

.page-item.active .page-link {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-color: #667eea;
    color: white;
}

/* Responsive Mejorado */
@media (max-width: 1200px) {
    .property-image-wrapper {
        height: 250px;
    }
    
    .property-content {
        padding: 1.5rem;
    }
}

@media (max-width: 768px) {
    .hero-section {
        padding: 3rem 1rem !important;
        margin-bottom: 2rem;
    }
    
    .hero-section h1 {
        font-size: 2.5rem !important;
    }
    
    .property-image-wrapper {
        height: 220px;
    }
    
    .property-content {
        padding: 1.25rem;
    }
    
    .property-features {
        grid-template-columns: 1fr;
        gap: 0.75rem;
    }
    
    .property-actions {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    .filters-sidebar {
        margin-bottom: 2rem;
    }
    
    .content-header {
        padding: 1.5rem;
        text-align: center;
    }
}

@media (max-width: 576px) {
    .hero-section h1 {
        font-size: 2rem !important;
    }
    
    .property-image-wrapper {
        height: 200px;
    }
    
    .property-content {
        padding: 1rem;
    }
    
    .favorite-btn {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .price-tag {
        padding: 0.5rem 1rem;
    }
    
    .price-amount {
        font-size: 1.2rem;
    }
}

/* Animaciones de Entrada */
.property-card {
    animation: fadeInUp 0.6s ease-out;
}

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

/* Efectos de Hover en Filtros */
.filters-sidebar .btn-outline-primary {
    border-color: #667eea;
    color: #667eea;
    transition: all 0.3s ease;
}

.filters-sidebar .btn-outline-primary:hover,
.filters-sidebar .btn-outline-primary:focus {
    background: #667eea;
    border-color: #667eea;
    color: white;
    transform: translateY(-2px);
}

 .filters-sidebar .btn-outline-primary:checked + label {
     background: #667eea;
     border-color: #667eea;
     color: white;
 }

/* Header Icon Wrapper */
.header-icon-wrapper {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

/* Sort Wrapper */
.sort-wrapper {
    background: white;
    padding: 1.5rem;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    border: 2px solid #e9ecef;
}

.sort-select {
    border-radius: 12px;
    border: 2px solid #e9ecef;
    padding: 0.75rem 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.sort-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    background: white;
}

/* Mejoras en el Hero */
.hero-section .input-group {
    border-radius: 25px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.hero-section .form-control {
    border: none;
    padding: 1rem 1.5rem;
    font-size: 1.1rem;
}

.hero-section .btn {
    border-radius: 0 25px 25px 0;
    padding: 1rem 2rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Mejoras en los filtros de tipo */
.filters-sidebar .btn-outline-primary {
    border-radius: 25px;
    padding: 0.5rem 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
    border-width: 2px;
}

.filters-sidebar .btn-outline-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.filter-type-btn {
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
}

.filter-type-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
    transition: left 0.5s ease;
}

.filter-type-btn:hover::before {
    left: 100%;
}

.filter-type-btn i {
    transition: all 0.3s ease;
}

.filter-type-btn:hover i {
    transform: scale(1.1);
}

/* Filtros de ubicación mejorados */
.location-filters .form-floating {
    position: relative;
}

.location-select {
    border-radius: 12px;
    border: 2px solid #e9ecef;
    padding: 1rem 0.75rem;
    font-weight: 600;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    height: auto;
    min-height: 3.5rem;
}

.location-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    background: white;
    transform: translateY(-2px);
}

.location-select:disabled {
    background: #f8f9fa;
    color: #6c757d;
    cursor: not-allowed;
}

.form-floating > label {
    padding: 1rem 0.75rem;
    color: #6c757d;
    font-weight: 600;
}

.form-floating > .form-select:focus ~ label,
.form-floating > .form-select:not(:placeholder-shown) ~ label {
    color: #667eea;
    transform: scale(0.85) translateY(-1rem) translateX(0.15rem);
}

.form-floating > .form-select:focus ~ label i,
.form-floating > .form-select:not(:placeholder-shown) ~ label i {
    color: #667eea;
}

/* Filtro de precio mejorado */
.price-range-wrapper {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 1.5rem;
    border-radius: 16px;
    border: 2px solid #e9ecef;
}

.price-input {
    border-radius: 12px;
    border: 2px solid #e9ecef;
    padding: 1rem 0.75rem;
    font-weight: 600;
    transition: all 0.3s ease;
    background: white;
    height: auto;
    min-height: 3.5rem;
}

.price-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    transform: translateY(-2px);
}

.price-slider {
    text-align: center;
}

.price-display {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 1rem;
    background: rgba(102, 126, 234, 0.1);
    border-radius: 12px;
    border: 1px solid rgba(102, 126, 234, 0.2);
}

.price-min, .price-max {
    font-weight: 700;
    color: #667eea;
    font-size: 0.9rem;
}

.price-min::before {
    content: '💰 ';
}

.price-max::before {
    content: '💎 ';
}

/* Botones de acción de filtros mejorados */
.filter-action-btn {
    border-radius: 16px;
    padding: 1rem 1.5rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.filter-action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}

.filter-action-btn:hover::before {
    left: 100%;
}

.filter-action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.filter-action-btn.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.filter-action-btn.btn-primary:hover {
    background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
}

.filter-action-btn.btn-outline-secondary {
    border: 2px solid #6c757d;
    color: #6c757d;
    background: transparent;
}

.filter-action-btn.btn-outline-secondary:hover {
    background: #6c757d;
    border-color: #6c757d;
    color: white;
}

/* Mejoras en las características */
.feature-item {
    position: relative;
    overflow: hidden;
}

.feature-item::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05) 0%, transparent 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.feature-item:hover::after {
    opacity: 1;
}

/* Mejoras en el estado vacío */
.empty-state {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 20px;
    border: 2px solid #e9ecef;
}

.empty-state-icon i {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Mejoras en la paginación */
.pagination .page-link {
    margin: 0 0.25rem;
    min-width: 40px;
    text-align: center;
    font-weight: 600;
}

/* Efectos de hover mejorados */
.property-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.02) 0%, transparent 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    pointer-events: none;
}

.property-card:hover::before {
    opacity: 1;
}

/* Mejoras en el indicador de login */
.login-required-indicator {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* Mejoras en el precio */
.price-tag::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 25px;
    z-index: -1;
    opacity: 0.3;
}

/* Mejoras en los botones de acción */
.property-actions .btn {
    position: relative;
    overflow: hidden;
}

.property-actions .btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}

.property-actions .btn:hover::before {
    left: 100%;
}

/* Mejoras en el hero section */
.hero-section h1 {
    text-shadow: 0 4px 8px rgba(0,0,0,0.1);
    font-weight: 900;
}

.hero-section .lead {
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    font-weight: 500;
}

/* Mejoras en los filtros */
.filters-sidebar .card {
    position: relative;
    overflow: hidden;
}

.filters-sidebar .card::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(102, 126, 234, 0.03), transparent);
    transform: rotate(45deg);
    transition: all 0.6s ease;
}

.filters-sidebar .card:hover::before {
    transform: rotate(45deg) translate(50%, 50%);
}
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script cargado correctamente');
    
    const departmentSelect = document.getElementById('department');
    const citySelect = document.getElementById('city');
    
    // Cargar ciudades cuando cambie el departamento
    if (departmentSelect && citySelect) {
        departmentSelect.addEventListener('change', function() {
            const departmentId = this.value;
            console.log('Departamento seleccionado:', departmentId);
            
            // Limpiar ciudades
            citySelect.innerHTML = '<option value="">Todas las ciudades</option>';
            citySelect.disabled = !departmentId;
            
            if (departmentId) {
                // Cargar ciudades del departamento seleccionado
                fetch(`/api/cities/by-department?department_id=${departmentId}`)
                    .then(response => response.json())
                    .then(cities => {
                        console.log('Ciudades cargadas:', cities);
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
    }
    
    // Función para manejar favoritos
    window.toggleFavorite = function(button, propertyId) {
        console.log('=== toggleFavorite iniciado ===');
        console.log('Propiedad ID:', propertyId);
        console.log('Botón:', button);
        
        const icon = button.querySelector('i');
        console.log('Icono encontrado:', icon);
        
        // Verificar si el usuario está autenticado
        const userAuthenticatedMeta = document.querySelector('meta[name="user-authenticated"]');
        console.log('Meta tag de autenticación:', userAuthenticatedMeta);
        
        if (!userAuthenticatedMeta || userAuthenticatedMeta.getAttribute('content') !== 'true') {
            console.log('Usuario no autenticado, redirigiendo al login');
            showNotification('Debes iniciar sesión para agregar favoritos', 'warning');
            setTimeout(() => {
                window.location.href = '/login';
            }, 1500);
            return;
        }
        
        console.log('Usuario autenticado, procediendo con toggle...');
        
        // Toggle visual inmediato
        const isFavorite = button.classList.contains('active');
        console.log('Estado actual del botón (isFavorite):', isFavorite);
        
        if (!isFavorite) {
            console.log('Agregando a favoritos visualmente...');
            button.classList.add('active');
            icon.classList.remove('far');
            icon.classList.add('fas');
            button.style.transform = 'scale(1.2)';
            setTimeout(() => {
                button.style.transform = 'scale(1)';
            }, 200);
        } else {
            console.log('Removiendo de favoritos visualmente...');
            button.classList.remove('active');
            icon.classList.remove('fas');
            icon.classList.add('far');
        }
        
        console.log('Enviando petición AJAX...');
        
        // Llamada AJAX para guardar/quitar favorito
        fetch(`/favoritos/${propertyId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Respuesta del servidor recibida:', response);
            console.log('Status:', response.status);
            console.log('Headers:', response.headers);
            
            if (response.status === 401) {
                console.log('Error 401: Sesión expirada');
                showNotification('Sesión expirada. Por favor, inicia sesión nuevamente.', 'warning');
                setTimeout(() => {
                    window.location.href = '/login';
                }, 1500);
                return;
            }
            return response.json();
        })
        .then(data => {
            if (!data) {
                console.log('No hay datos para procesar');
                return;
            }
            
            console.log('Datos JSON recibidos:', data);
            if (data.success) {
                console.log('Operación exitosa, actualizando UI...');
                if (data.isFavorite) {
                    button.classList.add('active');
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                } else {
                    button.classList.remove('active');
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                }
                showNotification(data.message, 'success');
            } else {
                console.log('Operación fallida, revirtiendo cambios...');
                // Revertir cambios visuales si hay error
                if (isFavorite) {
                    button.classList.add('active');
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                } else {
                    button.classList.remove('active');
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                }
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error en toggleFavorite:', error);
            console.log('Revertiendo cambios visuales debido al error...');
            // Revertir cambios visuales
            if (isFavorite) {
                button.classList.add('active');
                icon.classList.remove('far');
                icon.classList.add('fas');
            } else {
                button.classList.remove('active');
                icon.classList.remove('fas');
                icon.classList.add('far');
            }
            showNotification('Error al procesar la solicitud', 'error');
        });
        
        console.log('=== toggleFavorite completado ===');
    };
    
    // Función para cargar el estado inicial de los favoritos
    function loadFavoritesState() {
        console.log('Cargando estado inicial de favoritos...');
        const userAuthenticatedMeta = document.querySelector('meta[name="user-authenticated"]');
        if (!userAuthenticatedMeta || userAuthenticatedMeta.getAttribute('content') !== 'true') {
            console.log('Usuario no autenticado, saltando carga de favoritos');
            return; // Usuario no autenticado
        }
        
        console.log('Usuario autenticado, verificando favoritos...');
        
        // Obtener todos los botones de favorito
        const favoriteButtons = document.querySelectorAll('.favorite-btn');
        console.log('Botones de favorito encontrados:', favoriteButtons.length);
        
        favoriteButtons.forEach((button, index) => {
            const propertyId = button.getAttribute('data-property-id');
            console.log(`Verificando favorito ${index + 1}: Propiedad ID ${propertyId}`);
            
            // Verificar si la propiedad está en favoritos
            fetch(`/favoritos/${propertyId}/check`)
                .then(response => response.json())
                .then(data => {
                    console.log(`Respuesta para propiedad ${propertyId}:`, data);
                    if (data.isFavorite) {
                        console.log(`Propiedad ${propertyId} está en favoritos, marcando como activa`);
                        button.classList.add('active');
                        const icon = button.querySelector('i');
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                    }
                })
                .catch(error => {
                    console.error(`Error verificando favorito para propiedad ${propertyId}:`, error);
                });
        });
    }
    
        // Función para mostrar vista rápida
    window.showQuickView = function(propertyId) {
        console.log('showQuickView llamado con:', propertyId);
        
        // Crear modal de vista rápida
        const modal = document.createElement('div');
        modal.className = 'quick-view-modal';
        modal.innerHTML = `
            <div class="modal-overlay">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5><i class="fas fa-search me-2"></i>Vista Rápida</h5>
                        <button class="close-btn" onclick="this.closest('.quick-view-modal').remove()">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p class="mt-2">Cargando información de la propiedad...</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Cargar información de la propiedad usando la API
        fetch(`/api/properties/${propertyId}/quick-view`)
            .then(response => response.json())
            .then(data => {
                console.log('Datos de vista rápida:', data);
                
                // Crear contenido del modal con la información real
                let amenitiesHtml = '';
                if (data.amenities && data.amenities.length > 0) {
                    amenitiesHtml = `
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Amenities destacados:</h6>
                            <div class="d-flex flex-wrap gap-1">
                                ${data.amenities.map(amenity => 
                                    `<span class="badge bg-light text-dark">${amenity.replace('_', ' ')}</span>`
                                ).join('')}
                            </div>
                        </div>
                    `;
                }
                
                let ratingHtml = '';
                if (data.rating > 0) {
                    ratingHtml = `
                        <div class="text-center mb-3">
                            <div class="stars mb-2">
                                ${Array.from({length: 5}, (_, i) => 
                                    i < Math.floor(data.rating) ? 
                                        '<i class="fas fa-star text-warning"></i>' : 
                                        '<i class="far fa-star text-warning"></i>'
                                ).join('')}
                            </div>
                            <span class="badge bg-warning text-dark">${data.rating}/5</span>
                            ${data.review_count > 0 ? `<small class="text-muted ms-2">(${data.review_count} reseñas)</small>` : ''}
                        </div>
                    `;
                }
                
                modal.querySelector('.modal-body').innerHTML = `
                    <div class="quick-view-content">
                        ${data.image_url ? `
                            <div class="text-center mb-3">
                                <img src="${data.image_url}" alt="${data.name}" class="img-fluid rounded" style="max-height: 200px; object-fit: cover;">
                            </div>
                        ` : ''}
                        
                        <h5 class="mb-3">${data.name}</h5>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    <i class="fas fa-map-marker-alt text-primary mb-1"></i>
                                    <div class="small text-muted">Ubicación</div>
                                    <div class="fw-bold">${data.location}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded">
                                    <i class="fas fa-building text-primary mb-1"></i>
                                    <div class="small text-muted">Tipo</div>
                                    <div class="fw-bold">${data.type}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-4">
                                <div class="text-center p-2 bg-light rounded">
                                    <i class="fas fa-users text-primary mb-1"></i>
                                    <div class="small text-muted">Capacidad</div>
                                    <div class="fw-bold">${data.capacity}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-2 bg-light rounded">
                                    <i class="fas fa-bed text-primary mb-1"></i>
                                    <div class="small text-muted">Habitaciones</div>
                                    <div class="fw-bold">${data.bedrooms || 'N/A'}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="text-center p-2 bg-light rounded">
                                    <i class="fas fa-bath text-primary mb-1"></i>
                                    <div class="small text-muted">Baños</div>
                                    <div class="fw-bold">${data.bathrooms || 'N/A'}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="text-muted mb-2">Descripción:</h6>
                            <p class="mb-0">${data.description}</p>
                        </div>
                        
                        ${amenitiesHtml}
                        
                        ${ratingHtml}
                        
                        <div class="text-center mb-3">
                            <span class="badge bg-primary fs-5 p-2">${data.price}</span>
                            <div class="small text-muted">por noche</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <a href="/propiedades/${data.id}" class="btn btn-primary">
                                <i class="fas fa-eye me-2"></i>Ver Detalles Completos
                            </a>
                            <button class="btn btn-outline-secondary" onclick="this.closest('.quick-view-modal').remove()">
                                Cerrar
                            </button>
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                console.error('Error en showQuickView:', error);
                modal.querySelector('.modal-body').innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                        <p class="mt-2">Error al cargar la información</p>
                        <p class="text-muted small">${error.message}</p>
                        <button class="btn btn-secondary" onclick="this.closest('.quick-view-modal').remove()">Cerrar</button>
                    </div>
                `;
            });
        
        // Cerrar al hacer clic fuera del modal
        modal.querySelector('.modal-overlay').addEventListener('click', (e) => {
            if (e.target === modal.querySelector('.modal-overlay')) {
                modal.remove();
            }
        });
    };
    
    // Función para mostrar notificaciones
    function showNotification(message, type = 'info') {
        console.log('Mostrando notificación:', message, type);
        const notification = document.createElement('div');
        let alertClass = 'alert-info';
        
        switch(type) {
            case 'success':
                alertClass = 'alert-success';
                break;
            case 'error':
                alertClass = 'alert-danger';
                break;
            case 'warning':
                alertClass = 'alert-warning';
                break;
            default:
                alertClass = 'alert-info';
        }
        
        notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
    
    // Cargar estado inicial de favoritos
    loadFavoritesState();
    
    console.log('Script inicializado completamente');
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
    max-width: 600px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-header {
    padding: 1.5rem 1.5rem 1rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 16px 16px 0 0;
}

.modal-header h5 {
    margin: 0;
    color: white;
}

.close-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    font-size: 1.5rem;
    color: white;
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
    background: rgba(255, 255, 255, 0.3);
    color: white;
}

.modal-body {
    padding: 1.5rem;
}

.quick-view-content h5 {
    color: #2c3e50;
    font-weight: 700;
    text-align: center;
}

.quick-view-content .bg-light {
    background-color: #f8f9fa !important;
    border: 1px solid #e9ecef;
}

.quick-view-content .text-primary {
    color: #667eea !important;
}

.quick-view-content .stars i {
    font-size: 1.2rem;
    margin: 0 0.1rem;
}

.quick-view-content .badge {
    font-size: 0.9rem;
    padding: 0.5rem 1rem;
}

.quick-view-content .badge.bg-primary {
    background-color: #667eea !important;
    font-size: 1.1rem;
    padding: 0.75rem 1.5rem;
}

.quick-view-content .img-fluid {
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.quick-view-content .row > .col-4,
.quick-view-content .row > .col-6 {
    margin-bottom: 0.5rem;
}

.quick-view-content .fw-bold {
    font-weight: 600 !important;
}

.quick-view-content .text-muted {
    color: #6c757d !important;
}
</style>
@endpush
