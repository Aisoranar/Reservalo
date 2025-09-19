@extends('layouts.app')

@section('title', 'Crear Reserva')

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-calendar-plus text-primary me-2"></i>
                        Crear Reserva
                    </h1>
                    <p class="text-muted mb-0">Completa los datos para crear tu reserva</p>
                </div>
                <div>
                    <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver a Propiedades
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario de Reserva -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-plus me-2"></i>Datos de la Reserva
                    </h6>
                </div>
                <div class="card-body">
                    <form id="reservationForm" method="POST" action="{{ route('reservations.store.public') }}">
                        @csrf
                        
                        <div class="row">
                            <!-- Propiedad -->
                            <div class="col-md-6 mb-3">
                                <label for="property_id" class="form-label">Propiedad <span class="text-danger">*</span></label>
                                <select class="form-select @error('property_id') is-invalid @enderror" id="property_id" name="property_id" required>
                                    <option value="">Selecciona una propiedad</option>
                                    @foreach($properties as $property)
                                        <option value="{{ $property->id }}" 
                                                {{ old('property_id', $pendingReservation['property_id'] ?? '') == $property->id ? 'selected' : '' }}
                                                data-price="{{ $property->price }}">
                                            {{ $property->name }} - ${{ number_format($property->price, 0, ',', '.') }} COP/noche
                                        </option>
                                    @endforeach
                                </select>
                                @error('property_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Huéspedes -->
                            <div class="col-md-6 mb-3">
                                <label for="guests" class="form-label">Número de Huéspedes <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('guests') is-invalid @enderror" 
                                       id="guests" name="guests" min="1" max="20" 
                                       value="{{ old('guests', $pendingReservation['guests'] ?? '1') }}" required>
                                @error('guests')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Fecha de Inicio -->
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Fecha de Inicio <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                       id="start_date" name="start_date" 
                                       value="{{ old('start_date', $pendingReservation['start_date'] ?? '') }}" 
                                       min="{{ date('Y-m-d') }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Fecha de Fin -->
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">Fecha de Fin <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                       id="end_date" name="end_date" 
                                       value="{{ old('end_date', $pendingReservation['end_date'] ?? '') }}" 
                                       min="{{ date('Y-m-d') }}" required>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Solicitudes Especiales -->
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="special_requests" class="form-label">Solicitudes Especiales</label>
                                <textarea class="form-control @error('special_requests') is-invalid @enderror" 
                                          id="special_requests" name="special_requests" rows="3" 
                                          placeholder="Menciona cualquier solicitud especial o comentario adicional...">{{ old('special_requests') }}</textarea>
                                @error('special_requests')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Resumen de Precio -->
                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title text-primary">
                                            <i class="fas fa-calculator me-2"></i>Resumen de Precio
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <small class="text-muted">Precio por noche:</small>
                                                <div id="price-per-night" class="fw-bold">$0 COP</div>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Noches:</small>
                                                <div id="nights" class="fw-bold">0</div>
                                            </div>
                                            <div class="col-md-4">
                                                <small class="text-muted">Total:</small>
                                                <div id="total-price" class="fw-bold text-primary fs-5">$0 COP</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="{{ route('properties.index') }}" class="btn btn-outline-secondary me-md-2">
                                        <i class="fas fa-times me-1"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                        <i class="fas fa-calendar-check me-1"></i>Crear Reserva
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cargar datos de reserva pendiente desde localStorage
    loadPendingReservationData();
    
    // Elementos del formulario
    const propertySelect = document.getElementById('property_id');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const guestsInput = document.getElementById('guests');
    const submitBtn = document.getElementById('submitBtn');
    
    // Elementos de precio
    const pricePerNightEl = document.getElementById('price-per-night');
    const nightsEl = document.getElementById('nights');
    const totalPriceEl = document.getElementById('total-price');
    
    // Variables para fechas ocupadas
    let occupiedDates = [];
    
    // Cargar fechas ocupadas cuando se selecciona una propiedad
    propertySelect.addEventListener('change', function() {
        if (this.value) {
            loadOccupiedDates(this.value);
            calculateTotal();
        } else {
            clearPriceFields();
        }
    });
    
    // Calcular total cuando cambian las fechas
    startDateInput.addEventListener('change', calculateTotal);
    endDateInput.addEventListener('change', calculateTotal);
    guestsInput.addEventListener('change', calculateTotal);
    
    // Función para cargar fechas ocupadas
    function loadOccupiedDates(propertyId) {
        fetch(`/api/properties/${propertyId}/occupied-dates`)
            .then(response => response.json())
            .then(data => {
                occupiedDates = data.occupied_dates || [];
                console.log('Fechas ocupadas cargadas:', occupiedDates);
            })
            .catch(error => {
                console.error('Error cargando fechas ocupadas:', error);
                occupiedDates = [];
            });
    }
    
    // Función para calcular el total
    function calculateTotal() {
        const propertyId = propertySelect.value;
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        const guests = guestsInput.value;
        
        if (!propertyId || !startDate || !endDate || !guests) {
            clearPriceFields();
            return;
        }
        
        // Verificar que las fechas sean válidas
        const start = new Date(startDate);
        const end = new Date(endDate);
        
        if (start >= end) {
            clearPriceFields();
            return;
        }
        
        // Calcular noches
        const nights = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
        
        // Obtener precio por noche
        const selectedOption = propertySelect.options[propertySelect.selectedIndex];
        const pricePerNight = parseFloat(selectedOption.dataset.price) || 0;
        
        // Calcular total
        const totalPrice = nights * pricePerNight;
        
        // Actualizar elementos
        if (pricePerNightEl) pricePerNightEl.textContent = `$${pricePerNight.toLocaleString()} COP`;
        if (nightsEl) nightsEl.textContent = nights;
        if (totalPriceEl) totalPriceEl.textContent = `$${totalPrice.toLocaleString()} COP`;
        
        // Habilitar/deshabilitar botón
        const isValid = propertyId && startDate && endDate && guests && nights > 0 && !isDateOccupied(startDate, endDate);
        if (submitBtn) {
            submitBtn.disabled = !isValid;
        }
    }
    
    // Función para verificar si las fechas están ocupadas
    function isDateOccupied(startDate, endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        
        for (let d = new Date(start); d < end; d.setDate(d.getDate() + 1)) {
            const dateStr = d.toISOString().split('T')[0];
            if (occupiedDates.includes(dateStr)) {
                return true;
            }
        }
        return false;
    }
    
    // Función para limpiar campos de precio
    function clearPriceFields() {
        if (pricePerNightEl) pricePerNightEl.textContent = '$0 COP';
        if (nightsEl) nightsEl.textContent = '0';
        if (totalPriceEl) totalPriceEl.textContent = '$0 COP';
        if (submitBtn) submitBtn.disabled = true;
    }
    
    // Función para cargar datos de reserva pendiente desde localStorage
    function loadPendingReservationData() {
        const pendingReservation = localStorage.getItem('pendingReservation');
        if (pendingReservation) {
            try {
                const data = JSON.parse(pendingReservation);
                console.log('Datos de reserva pendiente encontrados:', data);
                
                // Cargar datos en el formulario
                if (data.property_id && propertySelect) {
                    propertySelect.value = data.property_id;
                    // Disparar evento change para cargar fechas ocupadas
                    propertySelect.dispatchEvent(new Event('change'));
                }
                
                if (data.start_date && startDateInput) {
                    startDateInput.value = data.start_date;
                }
                
                if (data.end_date && endDateInput) {
                    endDateInput.value = data.end_date;
                }
                
                if (data.guests && guestsInput) {
                    guestsInput.value = data.guests;
                }
                
                // Mostrar mensaje de información
                if (data.property_name) {
                    showNotification(`Datos cargados desde: ${data.property_name}`, 'info');
                }
                
                // Limpiar datos del localStorage
                localStorage.removeItem('pendingReservation');
                
            } catch (error) {
                console.error('Error al cargar datos de reserva pendiente:', error);
                localStorage.removeItem('pendingReservation');
            }
        }
    }
    
    // Función para mostrar notificaciones
    function showNotification(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            <i class="fas fa-info-circle me-2"></i>${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.parentNode.removeChild(alertDiv);
            }
        }, 5000);
    }
});
</script>
@endpush
