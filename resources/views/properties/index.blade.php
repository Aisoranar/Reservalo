@extends('layouts.app')

@section('title', 'Propiedades - Reservalo')

@section('content')
<div class="properties-page">
    <!-- Hero Section Ultra Moderno -->
    <div class="hero-section-modern">
        <div class="hero-background">
            <div class="hero-overlay"></div>
            <div class="hero-particles"></div>
        </div>
        
        <div class="container">
            <div class="hero-content">
                <!-- Badge Animado -->
                <div class="hero-badge-modern" data-aos="fade-down" data-aos-delay="100">
                    <span class="badge-content">
                        <i class="fas fa-star"></i>
                        <span>Destinos Premium</span>
                        <div class="badge-glow"></div>
                         </span>
                     </div>
                     
                <!-- Título Principal -->
                <h1 class="hero-title" data-aos="fade-up" data-aos-delay="200">
                    <span class="title-line-1">Encuentra tu</span>
                    <span class="title-line-2">lugar perfecto</span>
                    <div class="title-underline"></div>
                     </h1>
                
                <!-- Subtítulo -->
                <p class="hero-subtitle" data-aos="fade-up" data-aos-delay="300">
                    Descubre las mejores propiedades turísticas en Colombia
                </p>
                
                <!-- Búsqueda Avanzada -->
                <div class="search-container-modern" data-aos="fade-up" data-aos-delay="400">
                    <form method="GET" action="{{ route('properties.index') }}" class="search-form-modern">
                        <div class="search-input-group">
                            <div class="search-icon">
                                <i class="fas fa-search"></i>
                            </div>
                            <input type="text" 
                                   class="search-input" 
                                   name="search" 
                                            placeholder="¿Dónde quieres ir? Busca por ciudad, departamento o nombre..." 
                                   value="{{ request('search') }}"
                                   autocomplete="off">
                            <button type="submit" class="search-button">
                                <span>Buscar</span>
                                <i class="fas fa-arrow-right"></i>
                                     </button>
                                 </div>
                             </form>
                     </div>
                     
                <!-- Estadísticas Animadas -->
                <div class="hero-stats-modern" data-aos="fade-up" data-aos-delay="500">
                    <div class="stats-grid">
                        <div class="stat-card-modern">
                            <div class="stat-icon">
                                <i class="fas fa-home"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number" data-count="{{ $properties->total() }}">0</div>
                                     <div class="stat-label">Propiedades</div>
                                 </div>
                             </div>
                        
                        <div class="stat-card-modern">
                            <div class="stat-icon">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number" data-count="32">0</div>
                                     <div class="stat-label">Departamentos</div>
                                 </div>
                             </div>
                        
                        <div class="stat-card-modern">
                            <div class="stat-icon">
                                <i class="fas fa-city"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number" data-count="1000">0</div>
                                     <div class="stat-label">Ciudades</div>
                                 </div>
                             </div>
                        
                        <div class="stat-card-modern">
                            <div class="stat-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="stat-content">
                                     <div class="stat-number">24/7</div>
                                     <div class="stat-label">Soporte</div>
                                 </div>
                             </div>
                         </div>
                    
                    <!-- Precio Global -->
                    @if($stats['active_global_pricing'])
                    <div class="pricing-card-modern" data-aos="zoom-in" data-aos-delay="600">
                        <div class="pricing-content">
                            <div class="pricing-icon">
                                <i class="fas fa-tag"></i>
                     </div>
                            <div class="pricing-info">
                                <div class="pricing-label">Precio Estándar</div>
                                <div class="pricing-amount">
                                    ${{ number_format($stats['active_global_pricing']->final_price, 0, ',', '.') }}
                 </div>
                                <div class="pricing-name">{{ $stats['active_global_pricing']->name }}</div>
                            </div>
                        </div>
                        <div class="pricing-glow"></div>
                    </div>
                    @endif
             </div>
         </div>
     </div>

        <!-- Scroll Indicator -->
        <div class="scroll-indicator" data-aos="fade-up" data-aos-delay="700">
            <div class="scroll-arrow">
                <i class="fas fa-chevron-down"></i>
            </div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="main-content">
        <div class="container">
    <div class="row">
                <!-- Sidebar de Filtros Moderno -->
                <div class="col-lg-3 mb-5">
                    <div class="filters-sidebar-modern">
                        <div class="filters-card">
                            <div class="filters-header">
                                <div class="filters-icon">
                                    <i class="fas fa-sliders-h"></i>
                    </div>
                                <h4 class="filters-title">Filtros</h4>
                                <button class="filters-toggle d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                            
                            <div class="collapse show" id="filtersCollapse">
                                <div class="filters-body">
                                    <form method="GET" action="{{ route('properties.index') }}" id="filterForm" class="filters-form">
                                                         <!-- Búsqueda -->
                             <div class="mb-4">
                                 <label class="form-label fw-bold mb-3">
                                     <i class="fas fa-map-marker-alt me-2 text-primary"></i>Ubicación
                                 </label>
                                            <input type="text" class="form-control" name="search" 
                                                   placeholder="Buscar por ciudad, departamento..." 
                                                   value="{{ request('search') }}">
                                        </div>

                                        <!-- Departamento -->
                                        <div class="mb-4">
                                            <label for="department" class="form-label fw-bold mb-3">
                                                <i class="fas fa-map me-2 text-primary"></i>Departamento
                                            </label>
                                            <select name="department" id="department" class="form-select">
                                                <option value="">Todos los departamentos</option>
                                                @foreach($departments as $dept)
                                                    <option value="{{ $dept->id }}" 
                                                            {{ request('department') == $dept->id ? 'selected' : '' }}>
                                                     {{ $dept->name }}
                                                 </option>
                                             @endforeach
                                         </select>
                                     </div>

                                        <!-- Ciudad -->
                                        <div class="mb-4">
                                            <label for="city" class="form-label fw-bold mb-3">
                                                <i class="fas fa-city me-2 text-primary"></i>Ciudad
                                            </label>
                                            <select name="city" id="city" class="form-select">
                                                <option value="">Todas las ciudades</option>
                                                @if($cities)
                                                    @foreach($cities as $city)
                                                        <option value="{{ $city->id }}" 
                                                                {{ request('city') == $city->id ? 'selected' : '' }}>
                                                         {{ $city->name }}
                                                     </option>
                                                 @endforeach
                                             @endif
                                         </select>
                             </div>

                                                         <!-- Tipo de Propiedad -->
                             <div class="mb-4">
                                            <label for="type" class="form-label fw-bold mb-3">
                                                <i class="fas fa-home me-2 text-primary"></i>Tipo de Propiedad
                                 </label>
                                            <select name="type" id="type" class="form-select">
                                                <option value="">Todos los tipos</option>
                                                <option value="apartment" {{ request('type') == 'apartment' ? 'selected' : '' }}>Apartamento</option>
                                                <option value="house" {{ request('type') == 'house' ? 'selected' : '' }}>Casa</option>
                                                <option value="villa" {{ request('type') == 'villa' ? 'selected' : '' }}>Villa</option>
                                                <option value="condo" {{ request('type') == 'condo' ? 'selected' : '' }}>Condominio</option>
                                            </select>
                             </div>

                                        <!-- Rango de Precios -->
                             <div class="mb-4">
                                 <label class="form-label fw-bold mb-3">
                                                <i class="fas fa-dollar-sign me-2 text-primary"></i>Rango de Precios
                                 </label>
                                            <div class="row">
                                         <div class="col-6">
                                                    <input type="number" class="form-control" name="min_price" 
                                                           placeholder="Mínimo" value="{{ request('min_price') }}">
                                         </div>
                                         <div class="col-6">
                                                    <input type="number" class="form-control" name="max_price" 
                                                           placeholder="Máximo" value="{{ request('max_price') }}">
                                     </div>
                                 </div>
                             </div>

                                        <!-- Botones -->
                                        <div class="d-grid gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search me-2"></i>Filtrar
                                 </button>
                                            <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-times me-2"></i>Limpiar
                                 </a>
                             </div>
                        </form>
                                </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido Principal -->
        <div class="col-lg-9">
                    <!-- Header Moderno -->
                    <div class="content-header-modern">
                        <div class="header-content">
                            <div class="header-left">
                                <div class="header-icon-modern">
                                 <i class="fas fa-home"></i>
                             </div>
                                <div class="header-text">
                                    <h2 class="header-title">Propiedades Disponibles</h2>
                                    <p class="header-subtitle">
                                        <i class="fas fa-search"></i>
                                     {{ $properties->total() }} propiedades encontradas
                                 </p>
                         </div>
                     </div>
                     
                            <div class="header-right">
                                <form method="GET" action="{{ route('properties.index') }}" id="sortForm" class="sort-form-modern">
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
                             
                                    <div class="sort-container">
                                        <label for="sortSelect" class="sort-label">
                                            <i class="fas fa-sort-amount-down"></i>
                                            Ordenar por
                                 </label>
                                        <select name="sort" id="sortSelect" class="sort-select-modern" onchange="this.form.submit()">
                                     <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Más recientes</option>
                                     <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Precio: menor a mayor</option>
                                     <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Precio: mayor a menor</option>
                                 </select>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>

                    <!-- Lista de Propiedades Moderna -->
            @if($properties->count() > 0)
                        <div class="properties-grid-modern">
                            <div class="properties-container">
                        @foreach($properties as $property)
                                    <div class="property-card-modern" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                                    <!-- Imagen de la propiedad -->
                                        <div class="property-image-modern">
                                        @if($property->primaryImage)
                                            <img src="{{ $property->primaryImage->full_url }}" 
                                                     class="property-img" 
                                                 alt="{{ $property->primaryImage->alt_text ?? $property->name }}"
                                                 loading="lazy">
                                        @else
                                                <div class="property-placeholder-modern">
                                                <i class="fas fa-home"></i>
                                                <span>Sin imagen</span>
                                            </div>
                                        @endif
                                        
                                            <!-- Overlay de la imagen -->
                                            <div class="property-overlay">
                                        <!-- Botón de favorito -->
                                                <button class="favorite-btn-modern" 
                                                data-property-id="{{ $property->id }}"
                                                onclick="toggleFavorite(this, {{ $property->id }})"
                                                title="{{ auth()->check() ? 'Agregar a favoritos' : 'Inicia sesión para agregar a favoritos' }}">
                                            <i class="far fa-heart"></i>
                                            @if(!auth()->check())
                                                        <span class="login-indicator">🔒</span>
                                            @endif
                                        </button>
                                        
                                        <!-- Precio -->
                                                <div class="price-tag-modern">
                                                    <div class="price-amount">${{ number_format($property->effective_price, 0, ',', '.') }}</div>
                                            <div class="price-period">por noche</div>
                                                </div>
                                                
                                                <!-- Botón de vista rápida -->
                                                <button class="quick-view-btn-modern" onclick="showQuickView({{ $property->id }})">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                        </div>
                                    </div>

                                        <!-- Contenido de la propiedad -->
                                        <div class="property-content-modern">
                                        <!-- Título -->
                                            <h5 class="property-title-modern">{{ $property->name }}</h5>
                                        
                                        <!-- Ubicación -->
                                            <div class="property-location-modern">
                                            <i class="fas fa-map-marker-alt"></i>
                                            @if($property->city)
                                                    <span>{{ $property->city->name }}, {{ $property->city->department->name }}</span>
                                            @else
                                                    <span>{{ $property->location }}</span>
                                            @endif
                                        </div>

                                        <!-- Descripción -->
                                            <p class="property-description-modern">{{ Str::limit($property->description, 120) }}</p>
                                        
                                        <!-- Características -->
                                            <div class="property-features-modern">
                                                <div class="feature-item-modern">
                                                <i class="fas fa-users"></i>
                                                    <span>{{ $property->capacity }}</span>
                                            </div>
                                                <div class="feature-item-modern">
                                                <i class="fas fa-bed"></i>
                                                    <span>{{ $property->bedrooms ?? 'N/A' }}</span>
                                            </div>
                                                <div class="feature-item-modern">
                                                <i class="fas fa-bath"></i>
                                                    <span>{{ $property->bathrooms ?? 'N/A' }}</span>
                                            </div>
                                        </div>

                                            <!-- Botón de acción principal -->
                                            <div class="property-action-modern">
                                            <a href="{{ route('properties.show', $property) }}" 
                                                   class="btn-view-details-modern">
                                                    <span>Ver Detalles</span>
                                                    <i class="fas fa-arrow-right"></i>
                                            </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Paginación -->
                @if($properties->hasPages())
                            <div class="pagination-modern">
                                @if($properties->onFirstPage())
                                    <span class="page-link-modern disabled">«</span>
                                @else
                                    <a href="{{ $properties->previousPageUrl() }}" class="page-link-modern">«</a>
                                @endif

                                @foreach($properties->getUrlRange(1, $properties->lastPage()) as $page => $url)
                                    @if($page == $properties->currentPage())
                                        <span class="page-link-modern active">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="page-link-modern">{{ $page }}</a>
                                    @endif
                                @endforeach

                                @if($properties->hasMorePages())
                                    <a href="{{ $properties->nextPageUrl() }}" class="page-link-modern">»</a>
                                @else
                                    <span class="page-link-modern disabled">»</span>
                                @endif
                    </div>
                @endif
            @else
                        <!-- Estado Vacío -->
                        <div class="empty-state-modern">
                            <div class="empty-state-icon-modern">
                            <i class="fas fa-search"></i>
                        </div>
                            <h3 class="empty-state-title-modern">No se encontraron propiedades</h3>
                            <p class="empty-state-text-modern">
                                Intenta ajustar tus filtros de búsqueda o explora nuestras categorías.
                        </p>
                        <a href="{{ route('properties.index') }}" class="btn btn-primary">
                            <i class="fas fa-refresh me-2"></i>Ver todas las propiedades
                        </a>
                </div>
            @endif
        </div>
    </div>
</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script cargado correctamente');
    
    const departmentSelect = document.getElementById('department');
    const citySelect = document.getElementById('city');
    
    // Cargar ciudades cuando cambie el departamento
    if (departmentSelect) {
        departmentSelect.addEventListener('change', function() {
            const departmentId = this.value;
            
            // Limpiar ciudades
            citySelect.innerHTML = '<option value="">Todas las ciudades</option>';
            
            if (departmentId) {
                // Mostrar loading
                citySelect.innerHTML = '<option value="">Cargando ciudades...</option>';
                
                // Hacer petición AJAX
                fetch(`/api/cities/${departmentId}`)
                    .then(response => response.json())
                    .then(cities => {
                        citySelect.innerHTML = '<option value="">Todas las ciudades</option>';
                        cities.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.id;
                            option.textContent = city.name;
                            citySelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        citySelect.innerHTML = '<option value="">Error al cargar ciudades</option>';
                    });
            }
        });
    }
    
    // Función para toggle de favoritos
    window.toggleFavorite = function(button, propertyId) {
        if (!{{ auth()->check() ? 'true' : 'false' }}) {
            showToast('Debes iniciar sesión para agregar a favoritos', 'warning');
            return;
        }
        
        const icon = button.querySelector('i');
        const isActive = button.classList.contains('active');
        
        // Cambiar estado visual inmediatamente
        if (isActive) {
            button.classList.remove('active');
            icon.classList.remove('fas');
            icon.classList.add('far');
                } else {
                    button.classList.add('active');
                    icon.classList.remove('far');
                    icon.classList.add('fas');
        }
        
        // Aquí iría la lógica AJAX para guardar/eliminar favorito
        showToast(isActive ? 'Eliminado de favoritos' : 'Agregado a favoritos', 'success');
    };
    
    // Función para vista rápida
    window.showQuickView = function(propertyId) {
        showToast('Función de vista rápida en desarrollo', 'info');
    };
});
</script>
@endpush
