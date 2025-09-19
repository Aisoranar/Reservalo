@extends('layouts.app')

@section('title', 'Crear Plan de Membresía - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-plus text-primary me-2"></i>
                        Crear Nuevo Plan de Membresía
                    </h1>
                    <p class="text-muted mb-0">Agrega un nuevo plan de membresía al sistema</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('superadmin.membership-plans') }}" class="btn btn-outline-secondary">
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
                        <i class="fas fa-gem me-2"></i>Información del Plan
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.membership-plans.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">
                                        Nombre del Plan <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="ej: Plan Básico"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">
                                        Precio (COP) <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" 
                                               class="form-control @error('price') is-invalid @enderror" 
                                               id="price" 
                                               name="price" 
                                               value="{{ old('price') }}" 
                                               placeholder="0"
                                               min="0"
                                               step="0.01"
                                               required>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration_days" class="form-label">
                                        Duración (días) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('duration_days') is-invalid @enderror" 
                                           id="duration_days" 
                                           name="duration_days" 
                                           value="{{ old('duration_days') }}" 
                                           placeholder="30"
                                           min="1"
                                           required>
                                    @error('duration_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_properties" class="form-label">
                                        Máximo de Propiedades
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('max_properties') is-invalid @enderror" 
                                           id="max_properties" 
                                           name="max_properties" 
                                           value="{{ old('max_properties') }}" 
                                           placeholder="Sin límite"
                                           min="0">
                                    @error('max_properties')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Dejar vacío para sin límite</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_reservations" class="form-label">
                                        Máximo de Reservas
                                    </label>
                                    <input type="number" 
                                           class="form-control @error('max_reservations') is-invalid @enderror" 
                                           id="max_reservations" 
                                           name="max_reservations" 
                                           value="{{ old('max_reservations') }}" 
                                           placeholder="Sin límite"
                                           min="0">
                                    @error('max_reservations')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Dejar vacío para sin límite</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Describe las características y beneficios del plan...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Características -->
                        <div class="mb-4">
                            <label class="form-label">Características del Plan</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="feature_properties" name="features[]" value="Gestión de propiedades" {{ in_array('Gestión de propiedades', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="feature_properties">
                                            Gestión de propiedades
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="feature_reservations" name="features[]" value="Sistema de reservas" {{ in_array('Sistema de reservas', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="feature_reservations">
                                            Sistema de reservas
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="feature_reports" name="features[]" value="Reportes avanzados" {{ in_array('Reportes avanzados', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="feature_reports">
                                            Reportes avanzados
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="feature_support" name="features[]" value="Soporte prioritario" {{ in_array('Soporte prioritario', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="feature_support">
                                            Soporte prioritario
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="feature_analytics" name="features[]" value="Analytics avanzado" {{ in_array('Analytics avanzado', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="feature_analytics">
                                            Analytics avanzado
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="feature_api" name="features[]" value="Acceso a API" {{ in_array('Acceso a API', old('features', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="feature_api">
                                            Acceso a API
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1" 
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Plan activo
                                        </label>
                                    </div>
                                    <div class="form-text">Los planes inactivos no están disponibles para nuevos usuarios</div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_default" 
                                               name="is_default" 
                                               value="1" 
                                               {{ old('is_default') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_default">
                                            Plan predeterminado
                                        </label>
                                    </div>
                                    <div class="form-text">Solo puede haber un plan predeterminado</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('superadmin.membership-plans') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i>Crear Plan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
