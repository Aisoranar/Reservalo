@extends('layouts.app')

@section('title', 'Mis Favoritos - Reservalo')

@section('meta')
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header del Perfil -->
    <div class="profile-header mb-5">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-3">
                    <div class="profile-avatar me-4">
                        <div class="avatar-circle">
                            <i class="fas fa-heart"></i>
                        </div>
                    </div>
                    <div>
                        <h1 class="h2 mb-1">Mis Favoritos</h1>
                        <p class="text-muted mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Tus propiedades preferidas en un solo lugar
                        </p>
                    </div>
                </div>
                
                <!-- Información rápida -->
                <div class="user-info-cards">
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-heart"></i>
                                </div>
                                <div class="info-content">
                                    <div class="info-number">{{ $favorites->total() }}</div>
                                    <div class="info-label">Favoritos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4 text-md-end">
                <div class="header-actions">
                    <a href="{{ route('properties.index') }}" class="btn btn-primary me-2">
                        <i class="fas fa-search me-2"></i>Explorar Propiedades
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-home me-2"></i>Inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
            
    @if($favorites->count() > 0)
        <!-- Lista de Favoritos -->
        <div class="favorites-grid">
            <div class="row row-cols-1 row-cols-lg-2 g-4">
                    @foreach($favorites as $favorite)
                        @php $property = $favorite->property @endphp
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
                                            <div class="placeholder-content">
                                                <i class="fas fa-home"></i>
                                                <span>Sin imagen</span>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <!-- Botón de favorito (ya está en favoritos) -->
                                    <button class="favorite-btn active" 
                                            data-property-id="{{ $property->id }}"
                                            onclick="toggleFavorite(this, {{ $property->id }})"
                                            title="Quitar de favoritos">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                    
                                    <!-- Precio flotante -->
                                    <div class="price-tag">
                                        <div class="price-amount">{{ $property->formatted_price }}</div>
                                        <div class="price-period">por noche</div>
                                    </div>
                                    
                                    <!-- Badge de tipo -->
                                    <div class="type-badge">
                                        <span class="badge-type">{{ ucfirst($property->type) }}</span>
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
                                        <div class="feature-item">
                                            <div class="feature-icon">
                                                <i class="fas fa-users"></i>
                                            </div>
                                            <div class="feature-info">
                                                <span class="feature-value">{{ $property->capacity }}</span>
                                                <span class="feature-label">Personas</span>
                                            </div>
                                        </div>
                                        <div class="feature-item">
                                            <div class="feature-icon">
                                                <i class="fas fa-bed"></i>
                                            </div>
                                            <div class="feature-info">
                                                <span class="feature-value">{{ $property->bedrooms ?? 'N/A' }}</span>
                                                <span class="feature-label">Habitaciones</span>
                                            </div>
                                        </div>
                                        <div class="feature-item">
                                            <div class="feature-icon">
                                                <i class="fas fa-bath"></i>
                                            </div>
                                            <div class="feature-info">
                                                <span class="feature-value">{{ $property->bathrooms ?? 'N/A' }}</span>
                                                <span class="feature-label">Baños</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Botones de acción -->
                                    <div class="property-actions">
                                        <a href="{{ route('properties.show', $property) }}" 
                                           class="btn btn-primary btn-view-details">
                                            <i class="fas fa-eye me-2"></i>Ver Detalles
                                        </a>
                                        <button class="btn btn-outline-danger btn-remove-favorite" 
                                                onclick="removeFavorite({{ $property->id }}, this)">
                                            <i class="fas fa-heart-broken me-2"></i>Quitar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>

        <!-- Paginación -->
        @if($favorites->hasPages())
            <div class="mt-4">
                <nav aria-label="Navegación de favoritos" class="d-flex justify-content-center">
                    {{ $favorites->links() }}
                </nav>
            </div>
        @endif
    @else
        <!-- Estado vacío -->
        <div class="text-center py-5">
            <div class="empty-state">
                <div class="empty-state-icon mb-4">
                    <i class="fas fa-heart-broken"></i>
                </div>
                <h4 class="mb-3">No tienes favoritos aún</h4>
                <p class="text-muted mb-4">
                    Explora nuestras propiedades y marca como favoritas las que más te gusten.
                </p>
                <div class="empty-state-actions">
                    <a href="{{ route('properties.index') }}" class="btn btn-primary btn-explore">
                        <i class="fas fa-search me-2"></i>Explorar Propiedades
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-home">
                        <i class="fas fa-home me-2"></i>Volver al Inicio
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Botón de Scroll to Top -->
<button class="scroll-to-top" id="scrollToTop" title="Volver arriba">
    <i class="fas fa-arrow-up"></i>
</button>

<!-- Estilos CSS para Favoritos -->
<style>
/* Header del Perfil */
.profile-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 2rem;
    border: 1px solid #dee2e6;
}

.profile-avatar .avatar-circle {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
}

.user-info-cards .info-card {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
    border: 1px solid #e9ecef;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.user-info-cards .info-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.user-info-cards .info-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem;
    color: white;
    font-size: 1rem;
}

.user-info-cards .info-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 0.25rem;
}

.user-info-cards .info-label {
    font-size: 0.8rem;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.header-actions .btn {
    border-radius: 8px;
    font-weight: 600;
    padding: 0.5rem 1rem;
    font-size: 0.9rem;
}

/* Tarjetas de Propiedades */
.property-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    height: 100%;
}

.property-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.property-image-wrapper {
    position: relative;
    height: 200px;
    overflow: hidden;
    background: #f8f9fa;
}

.property-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.property-card:hover .property-image {
    transform: scale(1.05);
}

.property-placeholder {
    width: 100%;
    height: 100%;
    background: #f8f9fa;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #6c757d;
}

.property-placeholder i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    opacity: 0.4;
    color: #dc3545;
}

.placeholder-content {
    text-align: center;
}

.placeholder-content span {
    font-size: 0.8rem;
    font-weight: 600;
}

/* Botón de Favorito */
.favorite-btn {
    position: absolute !important;
    top: 0.75rem;
    right: 0.75rem;
    background: #dc3545;
    border: none;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    transition: all 0.3s ease;
    z-index: 10;
    font-size: 0.9rem;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
}

.favorite-btn:hover {
    background: #c82333;
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
}

/* Badge de Tipo */
.type-badge {
    position: absolute;
    top: 0.75rem;
    left: 0.75rem;
}

.badge-type {
    background: rgba(255, 255, 255, 0.95);
    color: #495057;
    padding: 0.4rem 0.8rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: 1px solid rgba(255, 255, 255, 0.8);
}

/* Precio */
.price-tag {
    position: absolute;
    bottom: 0.75rem;
    left: 0.75rem;
    background: #dc3545;
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
}

.price-amount {
    font-size: 1rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.1rem;
}

.price-period {
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
}

/* Contenido de la Tarjeta */
.property-content {
    padding: 1rem;
    background: #fff;
}

.property-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
}

.property-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2c3e50;
    margin: 0;
    line-height: 1.3;
    flex: 1;
    margin-right: 0.75rem;
}

.property-rating {
    text-align: right;
    min-width: 80px;
}

.stars {
    margin-bottom: 0.25rem;
}

.stars i {
    color: #ffc107;
    font-size: 0.8rem;
    margin: 0 0.1rem;
}

.rating-text {
    font-weight: 600;
    color: #495057;
    font-size: 0.8rem;
}

.review-count {
    color: #6c757d;
    font-size: 0.7rem;
}

/* Ubicación y Descripción */
.property-location {
    color: #6c757d;
    margin-bottom: 0.75rem;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.property-location i {
    color: #dc3545;
    font-size: 0.9rem;
}

.property-description {
    color: #6c757d;
    margin-bottom: 1rem;
    line-height: 1.5;
    font-size: 0.85rem;
    font-weight: 400;
}

/* Características */
.property-features {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.feature-item {
    text-align: center;
    padding: 0.75rem 0.5rem;
    background: #f8f9fa;
    border-radius: 8px;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.feature-item:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.feature-icon {
    margin-bottom: 0.5rem;
}

.feature-icon i {
    font-size: 1.2rem;
    color: #dc3545;
}

.feature-value {
    font-size: 0.9rem;
    font-weight: 700;
    color: #2c3e50;
    display: block;
    margin-bottom: 0.1rem;
}

.feature-label {
    font-size: 0.7rem;
    color: #6c757d;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Botones de Acción */
.property-actions {
    display: flex;
    gap: 0.5rem;
}

.btn-view-details {
    flex: 2;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-weight: 600;
    background: #dc3545;
    border: none;
    color: white;
    transition: all 0.3s ease;
    font-size: 0.8rem;
}

.btn-view-details:hover {
    background: #c82333;
    color: white;
}

.btn-remove-favorite {
    flex: 1;
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    font-weight: 600;
    border: 1px solid #dc3545;
    color: #dc3545;
    background: transparent;
    transition: all 0.3s ease;
    font-size: 0.8rem;
}

.btn-remove-favorite:hover {
    background: #dc3545;
    color: white;
}

/* Estado Vacío */
.empty-state {
    max-width: 400px;
    margin: 0 auto;
    text-align: center;
    padding: 2rem;
    background: #f8f9fa;
    border-radius: 12px;
    border: 1px solid #e9ecef;
}

.empty-state-icon {
    font-size: 3rem;
    color: #dc3545;
    margin-bottom: 1rem;
    opacity: 0.6;
}

.empty-state h4 {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 0.75rem;
}

.empty-state p {
    color: #6c757d;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 1.5rem;
}

.empty-state-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-explore {
    background: #dc3545;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-explore:hover {
    background: #c82333;
}

.btn-home {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    border: 1px solid #6c757d;
    color: #6c757d;
    background: transparent;
    transition: all 0.3s ease;
}

.btn-home:hover {
    background: #6c757d;
    color: white;
}

/* Paginación */
.pagination {
    gap: 0.25rem;
}

.page-link {
    border-radius: 6px;
    border: 1px solid #e9ecef;
    color: #dc3545;
    font-weight: 600;
    transition: all 0.3s ease;
    margin: 0 0.1rem;
    min-width: 35px;
    text-align: center;
    padding: 0.5rem 0.75rem;
}

.page-link:hover {
    background: #dc3545;
    border-color: #dc3545;
    color: white;
}

.page-item.active .page-link {
    background: #dc3545;
    border-color: #dc3545;
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .property-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .property-rating {
        text-align: left;
        min-width: auto;
    }
    
    .property-features {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .property-actions {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .empty-state-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-explore,
    .btn-home {
        width: 100%;
        max-width: 250px;
    }
}



/* Botón de Scroll to Top */
.scroll-to-top {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 45px;
    height: 45px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    font-size: 1rem;
    cursor: pointer;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    z-index: 1000;
}

.scroll-to-top.show {
    opacity: 1;
    visibility: visible;
}

.scroll-to-top:hover {
    background: #c82333;
    transform: translateY(-2px);
}
</style>

@push('scripts')
<script>
// Función para remover favoritos
function removeFavorite(propertyId, button) {
    if (confirm('¿Estás seguro de que quieres quitar esta propiedad de tus favoritos?')) {
        // Mostrar indicador de carga
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Quitando...';
        button.disabled = true;
        
        // Llamada AJAX para quitar de favoritos
        fetch(`/favoritos/${propertyId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remover la tarjeta
                const card = button.closest('.col');
                card.remove();
                
                // Actualizar contador
                updateFavoritesCount(-1);
                
                // Verificar si no hay más favoritos
                if (document.querySelectorAll('.property-card').length === 0) {
                    location.reload();
                }
            } else {
                alert(data.message || 'Error al quitar de favoritos');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        })
        .finally(() => {
            // Restaurar botón
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}

// Función para actualizar el contador de favoritos
function updateFavoritesCount(change) {
    const infoNumber = document.querySelector('.info-number');
    if (infoNumber) {
        const currentCount = parseInt(infoNumber.textContent);
        const newCount = Math.max(0, currentCount + change);
        infoNumber.textContent = newCount;
    }
}

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Funcionalidad del botón scroll to top
    const scrollToTopBtn = document.getElementById('scrollToTop');
    
    window.addEventListener('scroll', () => {
        if (window.pageYOffset > 200) {
            scrollToTopBtn.classList.add('show');
        } else {
            scrollToTopBtn.classList.remove('show');
        }
    });
    
    scrollToTopBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
});
</script>
@endpush
@endsection
