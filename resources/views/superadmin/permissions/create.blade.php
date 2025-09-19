@extends('layouts.app')

@section('title', 'Crear Permiso - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-plus text-primary me-2"></i>
                        Crear Nuevo Permiso
                    </h1>
                    <p class="text-muted mb-0">Agrega un nuevo permiso al sistema</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('superadmin.permissions') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-key me-2"></i>Información del Permiso
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.permissions.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        Nombre del Permiso <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="ej: manage_users"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Nombre técnico único del permiso (sin espacios, usar guiones bajos)</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="display_name" class="form-label">
                                        Nombre para Mostrar <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('display_name') is-invalid @enderror" 
                                           id="display_name" 
                                           name="display_name" 
                                           value="{{ old('display_name') }}" 
                                           placeholder="ej: Gestionar Usuarios"
                                           required>
                                    @error('display_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Nombre legible que se mostrará en la interfaz</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">
                                        Categoría <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" 
                                            name="category" 
                                            required>
                                        <option value="">Seleccionar categoría</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ old('category') === $category ? 'selected' : '' }}>
                                                {{ ucfirst($category) }}
                                            </option>
                                        @endforeach
                                        <option value="custom" {{ old('category') === 'custom' ? 'selected' : '' }}>
                                            Personalizada
                                        </option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="custom_category" class="form-label">Categoría Personalizada</label>
                                    <input type="text" 
                                           class="form-control @error('custom_category') is-invalid @enderror" 
                                           id="custom_category" 
                                           name="custom_category" 
                                           value="{{ old('custom_category') }}" 
                                           placeholder="ej: reportes"
                                           style="display: none;">
                                    @error('custom_category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text" id="custom_category_help" style="display: none;">Ingresa el nombre de la nueva categoría</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Describe qué permite hacer este permiso...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Permiso activo
                                </label>
                            </div>
                            <div class="form-text">Los permisos inactivos no se pueden asignar a roles</div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('superadmin.permissions') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Crear Permiso
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categorySelect = document.getElementById('category');
    const customCategoryInput = document.getElementById('custom_category');
    const customCategoryHelp = document.getElementById('custom_category_help');
    
    categorySelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customCategoryInput.style.display = 'block';
            customCategoryHelp.style.display = 'block';
            customCategoryInput.required = true;
        } else {
            customCategoryInput.style.display = 'none';
            customCategoryHelp.style.display = 'none';
            customCategoryInput.required = false;
            customCategoryInput.value = '';
        }
    });
    
    // Trigger change event on page load
    categorySelect.dispatchEvent(new Event('change'));
});
</script>
@endpush
@endsection
