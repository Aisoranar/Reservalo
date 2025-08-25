@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h2 mb-0">
        <i class="fas fa-plus me-2"></i>Nueva Propiedad
    </h1>
    <a href="{{ route('admin.properties') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Volver a Propiedades
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Información de la Propiedad</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.properties.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nombre de la propiedad *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label">Tipo de propiedad *</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Selecciona un tipo</option>
                                <option value="house" {{ old('type') == 'house' ? 'selected' : '' }}>Casa</option>
                                <option value="hotel" {{ old('type') == 'hotel' ? 'selected' : '' }}>Hotel</option>
                                <option value="farm" {{ old('type') == 'farm' ? 'selected' : '' }}>Finca</option>
                                <option value="apartment" {{ old('type') == 'apartment' ? 'selected' : '' }}>Apartamento</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">Ubicación *</label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                   id="location" name="location" value="{{ old('location') }}" 
                                   placeholder="Ciudad, región, dirección..." required>
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="capacity" class="form-label">Capacidad (personas) *</label>
                            <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                   id="capacity" name="capacity" value="{{ old('capacity', 1) }}" 
                                   min="1" max="50" required>
                            @error('capacity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Precio por noche *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                   id="price" name="price" value="{{ old('price') }}" 
                                   min="0" step="0.01" required>
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción *</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="4" 
                                  placeholder="Describe la propiedad, sus características, comodidades..." required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Servicios incluidos</label>
                        <div class="row">
                            @php
                                $services = [
                                    'WiFi', 'Aire acondicionado', 'Calefacción', 'Cocina', 'TV', 'Estacionamiento',
                                    'Piscina', 'Jardín', 'Terraza', 'Lavadora', 'Secadora', 'Desayuno incluido'
                                ];
                                $oldServices = old('services', []);
                            @endphp
                            @foreach($services as $service)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="services[]" value="{{ $service }}" 
                                               id="service_{{ $loop->index }}"
                                               {{ in_array($service, $oldServices) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="service_{{ $loop->index }}">
                                            {{ $service }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="images" class="form-label">Imágenes de la propiedad</label>
                        <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                               id="images" name="images[]" multiple accept="image/*">
                        <div class="form-text">
                            Puedes seleccionar múltiples imágenes. La primera será la imagen principal.
                        </div>
                        @error('images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('admin.properties') }}" class="btn btn-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Crear Propiedad
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Información de ayuda -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Consejos</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        <strong>Nombre:</strong> Usa un nombre descriptivo y atractivo
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        <strong>Descripción:</strong> Sé detallado sobre las características
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        <strong>Imágenes:</strong> Usa fotos de alta calidad
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        <strong>Precio:</strong> Establece un precio competitivo
                    </li>
                    <li>
                        <i class="fas fa-check text-success me-2"></i>
                        <strong>Servicios:</strong> Marca todos los servicios disponibles
                    </li>
                </ul>
            </div>
        </div>

        <!-- Vista previa de imágenes -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-images me-2"></i>Vista previa</h6>
            </div>
            <div class="card-body">
                <div id="imagePreview" class="text-center">
                    <i class="fas fa-image fa-3x text-muted mb-2"></i>
                    <p class="text-muted">Selecciona imágenes para ver la vista previa</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    const files = e.target.files;
    
    if (files.length > 0) {
        preview.innerHTML = '';
        
        for (let i = 0; i < Math.min(files.length, 6); i++) {
            const file = files[i];
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-thumbnail me-2 mb-2';
                    img.style.width = '80px';
                    img.style.height = '80px';
                    img.style.objectFit = 'cover';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        }
    } else {
        preview.innerHTML = `
            <i class="fas fa-image fa-3x text-muted mb-2"></i>
            <p class="text-muted">Selecciona imágenes para ver la vista previa</p>
        `;
    }
});
</script>
@endpush
