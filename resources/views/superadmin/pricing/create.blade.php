@extends('layouts.app')

@section('title', 'Crear Precio Global')

@section('styles')
<style>
.btn-success.text-white {
    background-color: #28a745 !important;
    border-color: #28a745 !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

.btn-success.text-white:hover {
    background-color: #218838 !important;
    border-color: #1e7e34 !important;
    color: #ffffff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
}

.btn-success.text-white:focus {
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.5) !important;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-plus me-2"></i>
                        Crear Precio Global
                    </h1>
                    <p class="text-muted mb-0">Configura un nuevo precio global para todas las propiedades</p>
                </div>
                <div>
                    <a href="{{ route('superadmin.pricing') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Volver
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Configuración del Precio
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('superadmin.pricing.store') }}" method="POST" id="pricingForm">
                        @csrf
                        
                        <!-- Información Básica -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="name" class="form-label">
                                    Nombre del Precio <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" 
                                       placeholder="Ej: Precio Estándar 2025" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="price_type" class="form-label">
                                    Tipo de Precio <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('price_type') is-invalid @enderror" 
                                        id="price_type" name="price_type" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="daily" {{ old('price_type') === 'daily' ? 'selected' : '' }}>
                                        Diario (por día)
                                    </option>
                                    <option value="nightly" {{ old('price_type') === 'nightly' ? 'selected' : '' }}>
                                        Nocturno (por noche)
                                    </option>
                                </select>
                                @error('price_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Precio Base -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="base_price" class="form-label">
                                    Precio Base <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('base_price') is-invalid @enderror" 
                                           id="base_price" name="base_price" value="{{ old('base_price') }}" 
                                           step="0.01" min="0" placeholder="0.00" required>
                                </div>
                                <div class="form-text">Precio base por {{ old('price_type') === 'nightly' ? 'noche' : 'día' }}</div>
                                @error('base_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="description" class="form-label">Descripción</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3" 
                                          placeholder="Descripción opcional del precio">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Configuración de Descuento -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="has_discount" name="has_discount" 
                                           value="1" {{ old('has_discount') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="has_discount">
                                        Aplicar Descuento
                                    </label>
                                </div>
                            </div>
                            <div class="card-body" id="discount_section" style="display: none;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="discount_type" class="form-label">Tipo de Descuento</label>
                                        <select class="form-select @error('discount_type') is-invalid @enderror" 
                                                id="discount_type" name="discount_type">
                                            <option value="">Seleccionar tipo</option>
                                            <option value="percentage" {{ old('discount_type') === 'percentage' ? 'selected' : '' }}>
                                                Porcentaje (%)
                                            </option>
                                            <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>
                                                Monto Fijo ($)
                                            </option>
                                        </select>
                                        @error('discount_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="discount_percentage" class="form-label" id="discount_percentage_label">
                                            Porcentaje de Descuento
                                        </label>
                                        <div class="input-group">
                                            <input type="number" class="form-control @error('discount_percentage') is-invalid @enderror" 
                                                   id="discount_percentage" name="discount_percentage" 
                                                   value="{{ old('discount_percentage') }}" 
                                                   step="0.01" min="0" max="100" placeholder="0.00">
                                            <span class="input-group-text">%</span>
                                        </div>
                                        @error('discount_percentage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="discount_amount" class="form-label" id="discount_amount_label">
                                            Monto de Descuento
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control @error('discount_amount') is-invalid @enderror" 
                                                   id="discount_amount" name="discount_amount" 
                                                   value="{{ old('discount_amount') }}" 
                                                   step="0.01" min="0" placeholder="0.00">
                                        </div>
                                        @error('discount_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           value="1" {{ old('is_active') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="is_active">
                                        Activar este precio global
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, este precio se activará automáticamente y desactivará cualquier otro precio activo.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-success text-white fw-bold">
                                        <i class="fas fa-save me-1"></i>
                                        Crear Precio Global
                                    </button>
                                    <a href="{{ route('superadmin.pricing') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>
                                        Cancelar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel de Vista Previa -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-eye me-2"></i>
                        Vista Previa
                    </h5>
                </div>
                <div class="card-body">
                    <div id="preview_content">
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p>Completa el formulario para ver la vista previa</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hasDiscountCheckbox = document.getElementById('has_discount');
    const discountSection = document.getElementById('discount_section');
    const discountTypeSelect = document.getElementById('discount_type');
    const discountPercentageLabel = document.getElementById('discount_percentage_label');
    const discountAmountLabel = document.getElementById('discount_amount_label');
    const discountPercentageInput = document.getElementById('discount_percentage');
    const discountAmountInput = document.getElementById('discount_amount');
    const basePriceInput = document.getElementById('base_price');
    const priceTypeSelect = document.getElementById('price_type');
    const previewContent = document.getElementById('preview_content');

    // Toggle discount section
    hasDiscountCheckbox.addEventListener('change', function() {
        discountSection.style.display = this.checked ? 'block' : 'none';
        updatePreview();
    });

    // Update discount labels based on type
    discountTypeSelect.addEventListener('change', function() {
        if (this.value === 'percentage') {
            discountPercentageLabel.style.display = 'block';
            discountAmountLabel.style.display = 'none';
            discountPercentageInput.required = true;
            discountAmountInput.required = false;
        } else if (this.value === 'fixed') {
            discountPercentageLabel.style.display = 'none';
            discountAmountLabel.style.display = 'block';
            discountPercentageInput.required = false;
            discountAmountInput.required = true;
        } else {
            discountPercentageLabel.style.display = 'block';
            discountAmountLabel.style.display = 'block';
            discountPercentageInput.required = false;
            discountAmountInput.required = false;
        }
        updatePreview();
    });

    // Update preview on input change
    [basePriceInput, discountPercentageInput, discountAmountInput, priceTypeSelect].forEach(input => {
        input.addEventListener('input', updatePreview);
    });

    function updatePreview() {
        const basePrice = parseFloat(basePriceInput.value) || 0;
        const priceType = priceTypeSelect.value;
        const hasDiscount = hasDiscountCheckbox.checked;
        const discountType = discountTypeSelect.value;
        const discountPercentage = parseFloat(discountPercentageInput.value) || 0;
        const discountAmount = parseFloat(discountAmountInput.value) || 0;

        let finalPrice = basePrice;

        if (hasDiscount && discountType === 'percentage') {
            finalPrice = basePrice - (basePrice * discountPercentage / 100);
        } else if (hasDiscount && discountType === 'fixed') {
            finalPrice = Math.max(0, basePrice - discountAmount);
        }

        if (basePrice > 0) {
            previewContent.innerHTML = `
                <div class="text-center">
                    <h6 class="text-primary mb-3">Vista Previa del Precio</h6>
                    <div class="mb-3">
                        <h4 class="text-success mb-0">
                            $${finalPrice.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0})}
                            <small class="text-muted">/${priceType === 'nightly' ? 'noche' : 'día'}</small>
                        </h4>
                        ${hasDiscount ? `
                            <small class="text-muted">
                                Precio base: $${basePrice.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0})}
                                ${discountType === 'percentage' ? `(${discountPercentage}% desc.)` : `(-$${discountAmount.toLocaleString('es-CO', {minimumFractionDigits: 0, maximumFractionDigits: 0})})`}
                            </small>
                        ` : ''}
                    </div>
                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            Este precio se aplicará a todas las propiedades cuando esté activo.
                        </small>
                    </div>
                </div>
            `;
        } else {
            previewContent.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="fas fa-info-circle fa-2x mb-2"></i>
                    <p>Completa el formulario para ver la vista previa</p>
                </div>
            `;
        }
    }

    // Initialize preview
    updatePreview();
});
</script>
@endsection
