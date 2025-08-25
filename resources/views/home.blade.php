@extends('layouts.app')

@section('title', 'Inicio - Reservalo')

@section('content')
<!-- Hero Section -->
<div class="hero-section text-white text-center py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="display-3 fw-bold mb-4">
                    Descubre tu lugar perfecto en Colombia
                </h1>
                <p class="lead mb-5">
                    Reservalo te conecta con las mejores propiedades turísticas del país. 
                    Desde casas de campo hasta apartamentos de lujo, encuentra tu hogar temporal ideal.
                </p>
                
                <!-- Búsqueda principal -->
                <div class="search-container mb-5">
                    <form method="GET" action="{{ route('properties.index') }}" class="search-form">
                        <div class="row g-3 justify-content-center">
                            <div class="col-md-4">
                                <input type="text" class="form-control form-control-lg" name="search" 
                                       placeholder="¿Dónde quieres ir?" value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="type" class="form-select form-select-lg">
                                    <option value="">Tipo de propiedad</option>
                                    <option value="casa">Casa</option>
                                    <option value="apartamento">Apartamento</option>
                                    <option value="cabaña">Cabaña</option>
                                    <option value="hotel">Hotel</option>
                                    <option value="finca">Finca</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-search me-2"></i>Buscar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Estadísticas rápidas -->
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="stat-item">
                            <div class="stat-number">{{ \App\Models\Property::active()->count() }}+</div>
                            <div class="stat-label">Propiedades</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-item">
                            <div class="stat-number">{{ \App\Models\Department::active()->count() }}+</div>
                            <div class="stat-label">Departamentos</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-item">
                            <div class="stat-number">{{ \App\Models\City::active()->count() }}+</div>
                            <div class="stat-label">Ciudades</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sección de Destinos Populares -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="display-5 fw-bold mb-3">Destinos Populares</h2>
                <p class="lead text-muted">Explora las regiones más visitadas de Colombia</p>
            </div>
        </div>
        
        <div class="row g-4">
            @php
                $popularDepartments = \App\Models\Department::active()->take(6)->get();
            @endphp
            
            @foreach($popularDepartments as $dept)
                <div class="col-md-4 col-lg-2">
                    <div class="destination-card text-center">
                        <div class="destination-icon mb-3">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h5 class="destination-name">{{ $dept->name }}</h5>
                        <a href="{{ route('properties.index', ['department' => $dept->id]) }}" 
                           class="btn btn-outline-primary btn-sm mt-2">
                            Explorar
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Sección de Propiedades Destacadas -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="display-5 fw-bold mb-3">Propiedades Destacadas</h2>
                <p class="lead text-muted">Descubre nuestras mejores opciones</p>
            </div>
        </div>
        
        <div class="row g-4">
            @php
                $featuredProperties = \App\Models\Property::active()
                    ->where('featured_until', '>', now())
                    ->with(['primaryImage', 'city.department'])
                    ->take(6)
                    ->get();
            @endphp
            
            @forelse($featuredProperties as $property)
                <div class="col-md-6 col-lg-4">
                    <div class="featured-property-card">
                        <div class="property-image-container">
                            @if($property->primaryImage)
                                <img src="{{ $property->primaryImage->full_url }}" 
                                     class="property-image" 
                                     alt="{{ $property->primaryImage->alt_text ?? $property->name }}">
                            @else
                                <div class="property-placeholder">
                                    <div class="placeholder-icon">
                                        <i class="fas fa-home"></i>
                                    </div>
                                </div>
                            @endif
                            <div class="featured-badge">
                                <i class="fas fa-star"></i> Destacada
                            </div>
                        </div>
                        
                        <div class="property-info">
                            <h5 class="property-title">{{ $property->name }}</h5>
                            <p class="property-location">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                @if($property->city)
                                    {{ $property->city->name }}, {{ $property->city->department->name }}
                                @else
                                    {{ $property->location }}
                                @endif
                            </p>
                            <div class="property-features">
                                <span class="feature">
                                    <i class="fas fa-users me-1"></i>{{ $property->capacity }} personas
                                </span>
                                <span class="feature">
                                    <i class="fas fa-bed me-1"></i>{{ $property->bedrooms ?? 'N/A' }} hab.
                                </span>
                                <span class="feature">
                                    <i class="fas fa-bath me-1"></i>{{ $property->bathrooms ?? 'N/A' }} baños
                                </span>
                            </div>
                            <div class="property-price">
                                <span class="price">{{ $property->formatted_price }}</span>
                                <span class="period">por noche</span>
                            </div>
                            <a href="{{ route('properties.show', $property) }}" 
                               class="btn btn-primary w-100 mt-3">
                                Ver Detalles
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">No hay propiedades destacadas en este momento.</p>
                </div>
            @endforelse
        </div>
        
        <div class="text-center mt-5">
            <a href="{{ route('properties.index') }}" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-search me-2"></i>Ver Todas las Propiedades
            </a>
        </div>
    </div>
</section>

<!-- Sección de Categorías -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="display-5 fw-bold mb-3">Tipos de Propiedades</h2>
                <p class="lead text-muted">Encuentra el tipo de alojamiento que mejor se adapte a ti</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <h4>Casas</h4>
                    <p>Espacios amplios y cómodos para familias grandes o grupos de amigos.</p>
                    <a href="{{ route('properties.index', ['type' => 'casa']) }}" class="btn btn-outline-primary">
                        Explorar Casas
                    </a>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <h4>Apartamentos</h4>
                    <p>Alojamientos modernos en ubicaciones céntricas y estratégicas.</p>
                    <a href="{{ route('properties.index', ['type' => 'apartamento']) }}" class="btn btn-outline-primary">
                        Explorar Apartamentos
                    </a>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-tree"></i>
                    </div>
                    <h4>Cabañas</h4>
                    <p>Refugios rústicos en medio de la naturaleza para una experiencia única.</p>
                    <a href="{{ route('properties.index', ['type' => 'cabaña']) }}" class="btn btn-outline-primary">
                        Explorar Cabañas
                    </a>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-hotel"></i>
                    </div>
                    <h4>Hoteles</h4>
                    <p>Servicios de lujo y comodidades premium para viajes de negocios o placer.</p>
                    <a href="{{ route('properties.index', ['type' => 'hotel']) }}" class="btn btn-outline-primary">
                        Explorar Hoteles
                    </a>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-seedling"></i>
                    </div>
                    <h4>Fincas</h4>
                    <p>Propiedades rurales con amplios terrenos y contacto directo con la naturaleza.</p>
                    <a href="{{ route('properties.index', ['type' => 'finca']) }}" class="btn btn-outline-primary">
                        Explorar Fincas
                    </a>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-4">
                <div class="category-card">
                    <div class="category-icon">
                        <i class="fas fa-compass"></i>
                    </div>
                    <h4>Otros</h4>
                    <p>Descubre opciones únicas y especiales para tu próxima aventura.</p>
                    <a href="{{ route('properties.index') }}" class="btn btn-outline-primary">
                        Explorar Todo
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sección de Por Qué Elegirnos -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="display-5 fw-bold mb-3">¿Por qué elegir Reservalo?</h2>
                <p class="lead text-muted">Descubre las ventajas de nuestra plataforma</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="feature-card text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5>Reservas Seguras</h5>
                    <p class="text-muted">Sistema de pagos seguro y verificación de propiedades.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="feature-card text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h5>Soporte 24/7</h5>
                    <p class="text-muted">Asistencia disponible en cualquier momento del día.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="feature-card text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-star"></i>
                    </div>
                    <h5>Calidad Garantizada</h5>
                    <p class="text-muted">Todas las propiedades son verificadas y calificadas.</p>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="feature-card text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h5>Acceso Móvil</h5>
                    <p class="text-muted">Plataforma optimizada para todos los dispositivos.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sección CTA -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="display-5 fw-bold mb-4">¿Listo para tu próxima aventura?</h2>
        <p class="lead mb-4">
            Únete a miles de viajeros que ya han descubierto Colombia con Reservalo
        </p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="{{ route('properties.index') }}" class="btn btn-light btn-lg">
                <i class="fas fa-search me-2"></i>Explorar Propiedades
            </a>
            @guest
                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-user-plus me-2"></i>Registrarse
                </a>
            @endguest
        </div>
    </div>
</section>

<!-- Estilos CSS personalizados -->
<style>
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 6rem 0;
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
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    opacity: 0.3;
}

.search-container {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.search-form .form-control,
.search-form .form-select {
    border: none;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.stat-item {
    padding: 1rem;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #fff;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.stat-label {
    font-size: 1rem;
    opacity: 0.9;
}

.destination-card {
    background: white;
    padding: 2rem 1rem;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.destination-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.destination-icon {
    font-size: 2.5rem;
    color: #667eea;
}

.destination-name {
    font-weight: 600;
    margin-bottom: 1rem;
}

.featured-property-card {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.featured-property-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.property-image-container {
    height: 200px;
    position: relative;
    overflow: hidden;
}

.property-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.featured-property-card:hover .property-image {
    transform: scale(1.05);
}

.property-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.placeholder-icon {
    font-size: 3rem;
    opacity: 0.6;
}

.featured-badge {
    position: absolute;
    top: 1rem;
    left: 1rem;
    background: #ffc107;
    color: #000;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.property-info {
    padding: 1.5rem;
}

.property-title {
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #333;
}

.property-location {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.property-features {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.feature {
    font-size: 0.8rem;
    color: #666;
}

.property-price {
    text-align: center;
    margin-bottom: 1rem;
}

.price {
    font-size: 1.5rem;
    font-weight: bold;
    color: #667eea;
}

.period {
    font-size: 0.9rem;
    color: #666;
}

.category-card {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    text-align: center;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    height: 100%;
}

.category-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.category-icon {
    font-size: 3rem;
    color: #667eea;
    margin-bottom: 1rem;
}

.category-card h4 {
    color: #333;
    margin-bottom: 1rem;
}

.category-card p {
    color: #666;
    margin-bottom: 1.5rem;
}

.feature-card {
    background: white;
    padding: 2rem;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    height: 100%;
}

.feature-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.feature-icon {
    font-size: 2.5rem;
    color: #667eea;
}

.feature-card h5 {
    color: #333;
    margin-bottom: 1rem;
}

.feature-card p {
    color: #666;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .hero-section {
        padding: 4rem 0;
    }
    
    .display-3 {
        font-size: 2.5rem;
    }
    
    .search-container {
        padding: 1.5rem;
    }
    
    .stat-number {
        font-size: 2rem;
    }
}
</style>
@endsection
