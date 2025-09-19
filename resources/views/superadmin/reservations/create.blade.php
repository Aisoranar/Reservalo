@extends('layouts.app')

@section('title', 'Crear Reserva Manual - Super Admin')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0 text-gray-800">
                        <i class="fas fa-plus-circle text-primary me-2"></i>
                        Crear Reserva Manual
                    </h1>
                    <p class="text-muted mb-0">Crear una reserva en nombre de un cliente</p>
                </div>
                <div>
                    <a href="{{ route('superadmin.reservations') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Volver a Reservas
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
                    <form id="manualReservationForm" method="POST" action="{{ route('superadmin.reservations.store-manual') }}">
                        @csrf
                        
                        <div class="row">
                            <!-- Tipo de Cliente -->
                            <div class="col-12 mb-3">
                                <label class="form-label">Tipo de Cliente <span class="text-danger">*</span></label>
                                <div class="btn-group w-100" role="group" aria-label="Tipo de cliente">
                                    <input type="radio" class="btn-check" name="client_type" id="registered_client" value="registered" {{ old('client_type', 'registered') == 'registered' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="registered_client">
                                        <i class="fas fa-user me-1"></i>Cliente Registrado
                                    </label>
                                    
                                    <input type="radio" class="btn-check" name="client_type" id="guest_client" value="guest" {{ old('client_type') == 'guest' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-success" for="guest_client">
                                        <i class="fas fa-user-plus me-1"></i>Cliente Nuevo (Huésped)
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Cliente Registrado -->
                        <div id="registered_client_section" class="client-section">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="user_id" class="form-label">Cliente Registrado <span class="text-danger">*</span></label>
                                    <select class="form-select @error('user_id') is-invalid @enderror" id="user_id" name="user_id">
                                        <option value="">Seleccionar cliente...</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Cliente Huésped -->
                        <div id="guest_client_section" class="client-section" style="display: none;">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="guest_name" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('guest_name') is-invalid @enderror" 
                                           id="guest_name" name="guest_name" value="{{ old('guest_name') }}" 
                                           placeholder="Nombre completo del cliente">
                                    @error('guest_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="guest_email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('guest_email') is-invalid @enderror" 
                                           id="guest_email" name="guest_email" value="{{ old('guest_email') }}" 
                                           placeholder="email@ejemplo.com">
                                    @error('guest_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="guest_phone" class="form-label">Teléfono</label>
                                    <input type="tel" class="form-control @error('guest_phone') is-invalid @enderror" 
                                           id="guest_phone" name="guest_phone" value="{{ old('guest_phone') }}" 
                                           placeholder="+57 300 123 4567">
                                    @error('guest_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Selección de Propiedad -->
                            <div class="col-md-6 mb-3">
                                <label for="property_id" class="form-label">Propiedad <span class="text-danger">*</span></label>
                                <select class="form-select @error('property_id') is-invalid @enderror" id="property_id" name="property_id" required>
                                    <option value="">Seleccionar propiedad...</option>
                                    @foreach($properties as $property)
                                        <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                            {{ $property->name }} - {{ $property->location }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('property_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Calendario Dinámico Compacto -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Seleccionar Fechas <span class="text-danger">*</span></label>
                                <div class="compact-calendar-container">
                                    <div class="calendar-header">
                                        <button type="button" class="calendar-nav-btn" id="prevMonth">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <h6 class="calendar-month-year" id="currentMonthYear">Septiembre 2025</h6>
                                        <button type="button" class="calendar-nav-btn" id="nextMonth">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                    <div class="calendar-weekdays">
                                        <div class="weekday">L</div>
                                        <div class="weekday">M</div>
                                        <div class="weekday">X</div>
                                        <div class="weekday">J</div>
                                        <div class="weekday">V</div>
                                        <div class="weekday">S</div>
                                        <div class="weekday">D</div>
                                    </div>
                                    <div class="calendar-grid" id="calendarGrid">
                                        <!-- Los días se generarán dinámicamente -->
                                    </div>
                                    
                                    <!-- Indicador de selección de salida -->
                                    <div class="end-date-indicator mt-2" id="endDateIndicator" style="display: none;">
                                        <div class="alert alert-warning py-2 mb-0">
                                            <i class="fas fa-arrow-down me-2"></i>
                                            <strong>Selecciona la fecha de salida</strong> - Haz click en cualquier fecha posterior a la entrada
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Inputs ocultos para el formulario -->
                                <input type="hidden" id="start_date" name="start_date" value="{{ old('start_date') }}">
                                <input type="hidden" id="end_date" name="end_date" value="{{ old('end_date') }}">
                            </div>
                            
                            <!-- Fechas Seleccionadas y Controles -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Fechas Seleccionadas</label>
                                <div class="selected-dates-compact">
                                    <div class="date-display" id="startDateDisplay">
                                        <i class="fas fa-calendar-check text-primary me-2"></i>
                                        <span class="fw-bold">Entrada:</span>
                                        <span id="startDateText">Selecciona fecha</span>
                                    </div>
                                    <div class="date-display" id="endDateDisplay">
                                        <i class="fas fa-calendar-times text-success me-2"></i>
                                        <span class="fw-bold">Salida:</span>
                                        <span id="endDateText">Selecciona fecha</span>
                                    </div>
                                </div>
                                
                                <!-- Controles de selección múltiple -->
                                <div class="multi-selection-controls mt-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="clearSelection">
                                        <i class="fas fa-times me-1"></i>Limpiar
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success" id="selectToday">
                                        <i class="fas fa-calendar-day me-1"></i>Hoy
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info" id="selectWeekend">
                                        <i class="fas fa-calendar-week me-1"></i>Fin de Semana
                                    </button>
                                </div>
                                
                                <!-- Instrucciones de selección múltiple -->
                                <div class="selection-instructions mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <strong>Ctrl + Click</strong> para selección múltiple
                                    </small>
                                </div>
                                
                                <!-- Estado de selección -->
                                <div class="selection-status mt-2" id="selectionStatus" style="display: none;">
                                    <div class="alert alert-info py-2">
                                        <i class="fas fa-mouse-pointer me-2"></i>
                                        <span id="statusText">Selecciona la fecha de salida</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Número de Huéspedes -->
                            <div class="col-md-3 mb-3">
                                <label for="guests" class="form-label">Huéspedes <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('guests') is-invalid @enderror" 
                                       id="guests" name="guests" value="{{ old('guests', 1) }}" min="1" required>
                                @error('guests')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Precio Total -->
                            <div class="col-md-3 mb-3">
                                <label for="pricing_method" class="form-label">Método de Precio <span class="text-danger">*</span></label>
                                <select class="form-select @error('pricing_method') is-invalid @enderror" 
                                        id="pricing_method" name="pricing_method" required>
                                    <option value="">Seleccionar método</option>
                                    <option value="global" {{ old('pricing_method') === 'global' ? 'selected' : '' }}>
                                        Usar Precio Global
                                    </option>
                                    <option value="manual" {{ old('pricing_method') === 'manual' ? 'selected' : '' }}>
                                        Precio Manual
                                    </option>
                                </select>
                                @error('pricing_method')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Selector de Precio Global -->
                        <div class="row mb-3" id="global_pricing_section" style="display: none;">
                            <div class="col-md-6">
                                <label for="global_pricing_id" class="form-label">Precio Global</label>
                                <select class="form-select @error('global_pricing_id') is-invalid @enderror" 
                                        id="global_pricing_id" name="global_pricing_id">
                                    <option value="">Cargando precios...</option>
                                </select>
                                @error('global_pricing_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Precio por {{ old('price_type', 'día') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" id="global_price_per_unit" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Precio Manual -->
                        <div class="row mb-3" id="manual_pricing_section" style="display: none;">
                            <div class="col-md-6">
                                <label for="total_price" class="form-label">Precio Total <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control @error('total_price') is-invalid @enderror" 
                                           id="total_price" name="total_price" value="{{ old('total_price') }}" 
                                           step="0.01" min="0">
                                </div>
                                @error('total_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Noches</label>
                                <input type="text" class="form-control" id="manual_nights" readonly>
                            </div>
                        </div>

                        <!-- Precio Total Final -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title mb-2">
                                            <i class="fas fa-calculator me-2"></i>Resumen del Precio
                                        </h6>
                                        <div id="price_summary">
                                            <p class="text-muted mb-0">Selecciona un método de precio para ver el resumen</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del Precio -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div id="price-info"></div>
                            </div>
                        </div>

                        <!-- Indicador de Disponibilidad -->
                        <div class="row">
                            <div class="col-12">
                                <div class="availability-container"></div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Solicitudes Especiales -->
                            <div class="col-md-6 mb-3">
                                <label for="special_requests" class="form-label">Solicitudes Especiales</label>
                                <textarea class="form-control @error('special_requests') is-invalid @enderror" 
                                          id="special_requests" name="special_requests" rows="3" 
                                          placeholder="Solicitudes especiales del cliente...">{{ old('special_requests') }}</textarea>
                                @error('special_requests')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Notas del Administrador -->
                            <div class="col-md-6 mb-3">
                                <label for="admin_notes" class="form-label">Notas del Administrador</label>
                                <textarea class="form-control @error('admin_notes') is-invalid @enderror" 
                                          id="admin_notes" name="admin_notes" rows="3" 
                                          placeholder="Notas internas sobre esta reserva...">{{ old('admin_notes') }}</textarea>
                                @error('admin_notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Estado de la Reserva -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Estado de la Reserva</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pendiente</option>
                                    <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Aprobada</option>
                                    <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rechazada</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Enviar Correo -->
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="send_email" name="send_email" value="1" checked>
                                    <label class="form-check-label" for="send_email">
                                        <i class="fas fa-envelope me-1"></i>Enviar correo de notificación al cliente
                                    </label>
                                </div>
                                
                                <!-- Indicador de estado del correo -->
                                <div id="email-status" class="mt-2" style="display: none;">
                                    <div class="alert alert-info d-flex align-items-center" role="alert" id="email-status-alert">
                                        <i class="fas fa-info-circle me-2" id="email-status-icon"></i>
                                        <div>
                                            <strong>Estado del correo:</strong>
                                            <span id="email-status-text">Preparando envío...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('superadmin.reservations') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success btn-lg fw-bold text-white shadow-lg">
                                        <i class="fas fa-save me-2"></i>Crear Reserva
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

@push('styles')
<style>
/* Calendario Dinámico Compacto */
.compact-calendar-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 15px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    color: white;
    margin-bottom: 15px;
    max-width: 280px;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    padding: 0 5px;
}

.calendar-nav-btn {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    font-size: 0.8rem;
}

.calendar-nav-btn:hover {
    background: rgba(255,255,255,0.3);
    transform: scale(1.1);
}

.calendar-month-year {
    margin: 0;
    font-weight: 600;
    font-size: 1rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
    margin-bottom: 8px;
}

.weekday {
    text-align: center;
    font-weight: 600;
    font-size: 0.75rem;
    padding: 6px 2px;
    opacity: 0.8;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 500;
    position: relative;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    font-size: 0.8rem;
    min-height: 28px;
}

.calendar-day:hover {
    background: rgba(255,255,255,0.2);
    transform: scale(1.05);
}

.calendar-day.other-month {
    opacity: 0.3;
    cursor: not-allowed;
}

.calendar-day.today {
    background: linear-gradient(45deg, #ff6b6b, #ffa726);
    box-shadow: 0 4px 15px rgba(255,107,107,0.4);
    font-weight: 700;
}

.calendar-day.selected-start {
    background: linear-gradient(45deg, #4ecdc4, #44a08d);
    box-shadow: 0 4px 15px rgba(78,205,196,0.4);
    font-weight: 700;
}

.calendar-day.selected-end {
    background: linear-gradient(45deg, #667eea, #764ba2);
    box-shadow: 0 4px 15px rgba(102,126,234,0.4);
    font-weight: 700;
}

.calendar-day.in-range {
    background: linear-gradient(45deg, #a8edea, #fed6e3);
    color: #333;
    font-weight: 600;
}

.calendar-day.multi-selected {
    background: linear-gradient(45deg, #ffd700, #ffed4e);
    color: #333;
    font-weight: 700;
    box-shadow: 0 0 0 2px #ff6b6b;
}

.calendar-day.selectable-end {
    background: rgba(78, 205, 196, 0.3);
    border: 2px dashed #4ecdc4;
    animation: pulse-glow 2s infinite;
}

.calendar-day.blocked-range {
    background: linear-gradient(45deg, #ffa726, #ffb74d) !important;
    color: #ffffff !important;
    cursor: not-allowed !important;
    opacity: 0.7;
    font-weight: 600;
    border: 2px dashed #ff9800 !important;
    position: relative;
}

.calendar-day.blocked-range:hover {
    transform: none !important;
    background: linear-gradient(45deg, #ffa726, #ffb74d) !important;
    box-shadow: 0 2px 8px rgba(255, 152, 0, 0.3);
}

.calendar-day.blocked-range::before {
    content: '⚠';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 10px;
    font-weight: bold;
    color: #ffffff;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

@keyframes pulse-glow {
    0% { 
        box-shadow: 0 0 0 0 rgba(78, 205, 196, 0.7);
        transform: scale(1);
    }
    50% { 
        box-shadow: 0 0 0 4px rgba(78, 205, 196, 0.3);
        transform: scale(1.02);
    }
    100% { 
        box-shadow: 0 0 0 0 rgba(78, 205, 196, 0.7);
        transform: scale(1);
    }
}

.calendar-day.occupied {
    background: linear-gradient(45deg, #ff6b6b, #ff8e8e) !important;
    color: #ffffff !important;
    cursor: not-allowed !important;
    opacity: 0.8;
    font-weight: 700;
    border: 2px solid #ff4757 !important;
    box-shadow: 0 2px 8px rgba(255, 71, 87, 0.3);
    position: relative;
}

.calendar-day.occupied:hover {
    transform: none !important;
    background: linear-gradient(45deg, #ff6b6b, #ff8e8e) !important;
    box-shadow: 0 4px 12px rgba(255, 71, 87, 0.5);
}

.calendar-day.occupied::before {
    content: '✕';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 12px;
    font-weight: bold;
    color: #ffffff;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.calendar-day.disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

.calendar-day.disabled:hover {
    transform: none;
}

/* Indicador de ocupado */
.occupied-indicator {
    position: absolute;
    top: 1px;
    right: 1px;
    width: 8px;
    height: 8px;
    background: #ffffff;
    border: 1px solid #ff4757;
    border-radius: 50%;
    animation: pulse-occupied 2s infinite;
    z-index: 2;
}

@keyframes pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.2); opacity: 0.7; }
    100% { transform: scale(1); opacity: 1; }
}

@keyframes pulse-occupied {
    0% { 
        transform: scale(1); 
        opacity: 1; 
        background: #ffffff;
    }
    50% { 
        transform: scale(1.3); 
        opacity: 0.8; 
        background: #ff4757;
    }
    100% { 
        transform: scale(1); 
        opacity: 1; 
        background: #ffffff;
    }
}

/* Fechas seleccionadas compactas */
.selected-dates-compact {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 12px;
    border: 2px solid #e9ecef;
    margin-bottom: 10px;
}

.date-display {
    display: flex;
    align-items: center;
    margin-bottom: 6px;
    padding: 6px 10px;
    background: white;
    border-radius: 6px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

.date-display:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}

.date-display:last-child {
    margin-bottom: 0;
}

/* Controles de selección múltiple */
.multi-selection-controls {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.multi-selection-controls .btn {
    font-size: 0.8rem;
    padding: 4px 8px;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.multi-selection-controls .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

/* Indicadores de disponibilidad */
.date-warning {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

.availability-indicator {
    margin-top: 0.5rem;
    padding: 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
}

.availability-indicator.available {
    background-color: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.availability-indicator.occupied {
    background-color: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

.availability-indicator.warning {
    background-color: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
}

/* Indicador de fecha de salida */
.end-date-indicator {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.end-date-indicator .alert {
    border-radius: 8px;
    font-size: 0.9rem;
    border-left: 4px solid #ffc107;
}

/* Botón de guardar mejorado */
.btn-success.btn-lg {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
    border-radius: 12px;
    padding: 12px 24px;
    font-size: 1.1rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-success.btn-lg:hover {
    background: linear-gradient(135deg, #218838 0%, #1ea085 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.6);
    color: white !important;
}

.btn-success.btn-lg:active {
    transform: translateY(0);
    box-shadow: 0 2px 10px rgba(40, 167, 69, 0.4);
}

.btn-success.btn-lg:focus {
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.5);
    color: white !important;
}

.btn-success.btn-lg::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-success.btn-lg:hover::before {
    left: 100%;
}

/* Botón de cancelar mejorado */
.btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: linear-gradient(135deg, #5a6268 0%, #3d4449 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4);
    color: white !important;
}

/* Responsive */
@media (max-width: 768px) {
    .compact-calendar-container {
        padding: 15px;
    }
    
    .calendar-day {
        font-size: 0.9rem;
    }
    
    .selected-dates-compact {
        padding: 10px;
    }
    
    .btn-success.btn-lg {
        font-size: 1rem;
        padding: 10px 20px;
    }

    /* Indicadores de estado del correo */
    #email-status {
        transition: all 0.3s ease;
    }

    #email-status .alert {
        border-radius: 8px;
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 0;
    }

    #email-status .alert-info {
        background-color: #d1ecf1;
        color: #0c5460;
        border-left: 4px solid #17a2b8;
    }

    #email-status .alert-warning {
        background-color: #fff3cd;
        color: #856404;
        border-left: 4px solid #ffc107;
    }

    #email-status .alert-success {
        background-color: #d4edda;
        color: #155724;
        border-left: 4px solid #28a745;
    }

    #email-status .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
        border-left: 4px solid #dc3545;
    }

    #email-status .fa-spinner {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cargar datos de reserva pendiente desde localStorage
    loadPendingReservationData();
    
    // Elementos del formulario
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const propertySelect = document.getElementById('property_id');
    const totalPriceInput = document.getElementById('total_price');
    const guestsInput = document.getElementById('guests');
    
    // Variables para fechas ocupadas
    let occupiedDates = [];
    
    // Variables del calendario
    let currentDate = new Date();
    let selectedStartDate = null;
    let selectedEndDate = null;
    let isSelectingEndDate = false;
    let selectedDates = []; // Para selección múltiple
    let multiSelectMode = false;
    
    // Elementos para el cambio de tipo de cliente
    const registeredClientRadio = document.getElementById('registered_client');
    const guestClientRadio = document.getElementById('guest_client');
    const registeredSection = document.getElementById('registered_client_section');
    const guestSection = document.getElementById('guest_client_section');
    const userIdSelect = document.getElementById('user_id');
    const guestNameInput = document.getElementById('guest_name');
    const guestEmailInput = document.getElementById('guest_email');
    const guestPhoneInput = document.getElementById('guest_phone');

    // Manejar cambio de tipo de cliente
    function toggleClientSections() {
        if (registeredClientRadio.checked) {
            registeredSection.style.display = 'block';
            guestSection.style.display = 'none';
            userIdSelect.required = true;
            userIdSelect.disabled = false;
            guestNameInput.required = false;
            guestEmailInput.required = false;
            guestPhoneInput.required = false;
            // Limpiar campos de huésped
            guestNameInput.value = '';
            guestEmailInput.value = '';
            guestPhoneInput.value = '';
        } else {
            registeredSection.style.display = 'none';
            guestSection.style.display = 'block';
            userIdSelect.required = false;
            userIdSelect.disabled = true;
            guestNameInput.required = true;
            guestEmailInput.required = true;
            guestPhoneInput.required = false;
            // Limpiar campo de usuario
            userIdSelect.value = '';
        }
    }

    registeredClientRadio.addEventListener('change', toggleClientSections);
    guestClientRadio.addEventListener('change', toggleClientSections);
    
    // Inicializar secciones
    toggleClientSections();
    
    // Inicializar calendario
    initializeCalendar();
    
    // Inicializar controles de selección múltiple
    initializeMultiSelectControls();
    
    // Cargar fechas ocupadas si ya hay una propiedad seleccionada
    if (propertySelect.value) {
        fetchOccupiedDates(propertySelect.value);
    }

    // Función para obtener fechas ocupadas
    async function fetchOccupiedDates(propertyId) {
        if (!propertyId) {
            occupiedDates = [];
            return;
        }
        
        try {
            // Mostrar indicador de carga
            showLoadingIndicator();
            
            const response = await fetch(`/superadmin/reservations/occupied-dates?property_id=${propertyId}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            occupiedDates = data.occupied_dates || [];
            console.log('Fechas ocupadas cargadas:', occupiedDates);
            
            // Mostrar mensaje de éxito
            showOccupiedDatesInfo(occupiedDates.length);
            
        } catch (error) {
            console.error('Error obteniendo fechas ocupadas:', error);
            occupiedDates = [];
            showErrorIndicator('Error cargando fechas ocupadas');
        }
    }
    
    // Función para mostrar indicador de carga
    function showLoadingIndicator() {
        const container = document.querySelector('.availability-container');
        if (!container) return;
        
        container.innerHTML = `
            <div class="alert alert-info d-flex align-items-center">
                <i class="fas fa-spinner fa-spin me-2"></i>
                <span>Cargando fechas ocupadas...</span>
            </div>
        `;
    }
    
    // Función para mostrar información de fechas ocupadas
    function showOccupiedDatesInfo(count) {
        const container = document.querySelector('.availability-container');
        if (!container) return;
        
        if (count > 0) {
            container.innerHTML = `
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="fas fa-calendar-times me-2"></i>
                    <span>Se encontraron ${count} fecha(s) ocupada(s) para esta propiedad</span>
                </div>
            `;
        } else {
            container.innerHTML = `
                <div class="alert alert-success d-flex align-items-center">
                    <i class="fas fa-calendar-check me-2"></i>
                    <span>No hay fechas ocupadas para esta propiedad</span>
                </div>
            `;
        }
    }
    
    // Función para mostrar error
    function showErrorIndicator(message) {
        const container = document.querySelector('.availability-container');
        if (!container) return;
        
        container.innerHTML = `
            <div class="alert alert-danger d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <span>${message}</span>
            </div>
        `;
    }
    
    // Función para mostrar advertencia de fecha ocupada
    function showOccupiedDateWarning(dayDate) {
        const container = document.querySelector('.availability-container');
        if (!container) return;
        
        const dateFormatted = dayDate.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        
        container.innerHTML = `
            <div class="alert alert-danger d-flex align-items-center">
                <i class="fas fa-calendar-times me-2"></i>
                <div>
                    <strong>Fecha ocupada:</strong> ${dateFormatted}<br>
                    <small>Esta fecha no está disponible para reservas</small>
                </div>
            </div>
        `;
        
        // Auto-ocultar después de 3 segundos
        setTimeout(() => {
            if (occupiedDates.length > 0) {
                showOccupiedDatesInfo(occupiedDates.length);
            }
        }, 3000);
    }
    
    // Función para validar que no haya fechas ocupadas en un rango
    function validateDateRange(startDate, endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        
        // Verificar cada día del rango
        while (start <= end) {
            const dateString = start.toISOString().split('T')[0];
            if (isDateOccupied(dateString)) {
                return false; // Hay al menos una fecha ocupada
            }
            start.setDate(start.getDate() + 1);
        }
        
        return true; // El rango está completamente disponible
    }
    
    // Función para mostrar advertencia de rango ocupado
    function showRangeOccupiedWarning(startDate, endDate) {
        const container = document.querySelector('.availability-container');
        if (!container) return;
        
        const startFormatted = startDate.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        const endFormatted = endDate.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        
        // Encontrar fechas ocupadas en el rango
        const occupiedInRange = findOccupiedDatesInRange(startDate, endDate);
        const occupiedDatesFormatted = occupiedInRange.map(date => 
            new Date(date).toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit'
            })
        ).join(', ');
        
        container.innerHTML = `
            <div class="alert alert-danger d-flex align-items-center">
                <i class="fas fa-calendar-times me-2"></i>
                <div>
                    <strong>Rango no disponible:</strong> ${startFormatted} - ${endFormatted}<br>
                    <small>Fechas ocupadas en el rango: ${occupiedDatesFormatted}</small><br>
                    <small class="text-muted">Selecciona un rango sin fechas ocupadas</small>
                </div>
            </div>
        `;
        
        // Auto-ocultar después de 5 segundos
        setTimeout(() => {
            if (occupiedDates.length > 0) {
                showOccupiedDatesInfo(occupiedDates.length);
            }
        }, 5000);
    }
    
    // Función para encontrar fechas ocupadas en un rango
    function findOccupiedDatesInRange(startDate, endDate) {
        const start = new Date(startDate);
        const end = new Date(endDate);
        const occupiedInRange = [];
        
        while (start <= end) {
            const dateString = start.toISOString().split('T')[0];
            if (isDateOccupied(dateString)) {
                occupiedInRange.push(dateString);
            }
            start.setDate(start.getDate() + 1);
        }
        
        return occupiedInRange;
    }
    
    // Función para mostrar advertencia de fecha bloqueada para rango
    function showBlockedRangeWarning(dayDate) {
        const container = document.querySelector('.availability-container');
        if (!container) return;
        
        const dateFormatted = dayDate.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        
        const startFormatted = selectedStartDate.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        
        // Encontrar fechas ocupadas en el rango
        const occupiedInRange = findOccupiedDatesInRange(selectedStartDate, dayDate);
        const occupiedDatesFormatted = occupiedInRange.map(date => 
            new Date(date).toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit'
            })
        ).join(', ');
        
        container.innerHTML = `
            <div class="alert alert-warning d-flex align-items-center">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>
                    <strong>Rango bloqueado:</strong> ${startFormatted} - ${dateFormatted}<br>
                    <small>Fechas ocupadas en el rango: ${occupiedDatesFormatted}</small><br>
                    <small class="text-muted">Selecciona una fecha anterior o después del bloque ocupado</small>
                </div>
            </div>
        `;
        
        // Auto-ocultar después de 4 segundos
        setTimeout(() => {
            if (occupiedDates.length > 0) {
                showOccupiedDatesInfo(occupiedDates.length);
            }
        }, 4000);
    }
    
    // Función para sugerir fechas alternativas
    function suggestAlternativeDates(startDate, endDate) {
        const container = document.querySelector('.availability-container');
        if (!container) return;
        
        const startFormatted = startDate.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        const endFormatted = endDate.toLocaleDateString('es-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
        
        // Encontrar fechas ocupadas en el rango
        const occupiedInRange = findOccupiedDatesInRange(startDate, endDate);
        const occupiedDatesFormatted = occupiedInRange.map(date => 
            new Date(date).toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit'
            })
        ).join(', ');
        
        // Buscar fechas alternativas
        const alternatives = findAlternativeRanges(startDate, endDate);
        
        let alternativesHtml = '';
        if (alternatives.length > 0) {
            alternativesHtml = `
                <div class="mt-2">
                    <strong>Fechas alternativas sugeridas:</strong><br>
                    ${alternatives.map(alt => 
                        `<small class="text-success">• ${alt.start} - ${alt.end}</small>`
                    ).join('<br>')}
                </div>
            `;
        }
        
        container.innerHTML = `
            <div class="alert alert-info d-flex align-items-start">
                <i class="fas fa-lightbulb me-2 mt-1"></i>
                <div>
                    <strong>Rango no disponible:</strong> ${startFormatted} - ${endFormatted}<br>
                    <small class="text-muted">Fechas ocupadas: ${occupiedDatesFormatted}</small>
                    ${alternativesHtml}
                </div>
            </div>
        `;
    }
    
    // Función para encontrar rangos alternativos
    function findAlternativeRanges(startDate, endDate) {
        const alternatives = [];
        const start = new Date(startDate);
        const end = new Date(endDate);
        const rangeLength = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
        
        // Buscar antes del rango original
        for (let i = 1; i <= 7; i++) {
            const altStart = new Date(start);
            altStart.setDate(start.getDate() - i);
            const altEnd = new Date(altStart);
            altEnd.setDate(altStart.getDate() + rangeLength - 1);
            
            if (validateDateRange(altStart, altEnd)) {
                alternatives.push({
                    start: altStart.toLocaleDateString('es-ES', {
                        day: '2-digit',
                        month: '2-digit'
                    }),
                    end: altEnd.toLocaleDateString('es-ES', {
                        day: '2-digit',
                        month: '2-digit'
                    })
                });
                if (alternatives.length >= 2) break;
            }
        }
        
        // Buscar después del rango original
        for (let i = 1; i <= 7; i++) {
            const altStart = new Date(start);
            altStart.setDate(start.getDate() + i);
            const altEnd = new Date(altStart);
            altEnd.setDate(altStart.getDate() + rangeLength - 1);
            
            if (validateDateRange(altStart, altEnd)) {
                alternatives.push({
                    start: altStart.toLocaleDateString('es-ES', {
                        day: '2-digit',
                        month: '2-digit'
                    }),
                    end: altEnd.toLocaleDateString('es-ES', {
                        day: '2-digit',
                        month: '2-digit'
                    })
                });
                if (alternatives.length >= 4) break;
            }
        }
        
        return alternatives.slice(0, 3); // Máximo 3 alternativas
    }

    // Función para verificar si una fecha está ocupada
    function isDateOccupied(dateString) {
        return occupiedDates.includes(dateString);
    }

    // Función para mostrar advertencia de fecha ocupada
    function showDateWarning(input, isOccupied) {
        const existingWarning = input.parentNode.querySelector('.date-warning');
        if (existingWarning) {
            existingWarning.remove();
        }
        
        if (isOccupied) {
            const warning = document.createElement('div');
            warning.className = 'date-warning text-danger small mt-1';
            warning.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Esta fecha está ocupada';
            input.parentNode.appendChild(warning);
        }
    }

    // Verificar fechas cuando cambien
    function checkDateAvailability() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        
        // Limpiar indicadores anteriores
        clearAvailabilityIndicators();
        
        if (startDate && endDate) {
            // Verificar rango completo
            const start = new Date(startDate);
            const end = new Date(endDate);
            const occupiedInRange = [];
            
            while (start <= end) {
                const dateString = start.toISOString().split('T')[0];
                if (isDateOccupied(dateString)) {
                    occupiedInRange.push(dateString);
                }
                start.setDate(start.getDate() + 1);
            }
            
            if (occupiedInRange.length > 0) {
                showAvailabilityIndicator('occupied', `⚠️ Fechas ocupadas en el rango: ${occupiedInRange.length} día(s)`);
            } else {
                showAvailabilityIndicator('available', '✅ Rango de fechas disponible');
            }
        } else if (startDate) {
            const isStartOccupied = isDateOccupied(startDate);
            showDateWarning(startDateInput, isStartOccupied);
        }
        
        if (endDate) {
            const isEndOccupied = isDateOccupied(endDate);
            showDateWarning(endDateInput, isEndOccupied);
        }
    }

    // Función para mostrar indicador de disponibilidad
    function showAvailabilityIndicator(type, message) {
        const container = document.querySelector('.availability-container');
        if (!container) return;
        
        const indicator = document.createElement('div');
        indicator.className = `availability-indicator ${type}`;
        indicator.innerHTML = `<i class="fas fa-${type === 'available' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>${message}`;
        
        container.appendChild(indicator);
    }

    // Función para limpiar indicadores
    function clearAvailabilityIndicators() {
        const container = document.querySelector('.availability-container');
        if (container) {
            container.innerHTML = '';
        }
    }

    // Cargar fechas ocupadas cuando cambie la propiedad
    propertySelect.addEventListener('change', async function() {
        await fetchOccupiedDates(this.value);
        // Actualizar el calendario para mostrar las fechas ocupadas
        renderCalendar();
        checkDateAvailability();
    });

    // Verificar fechas cuando cambien
    startDateInput.addEventListener('change', checkDateAvailability);
    endDateInput.addEventListener('change', checkDateAvailability);

    // Variables para precios globales
    let globalPricings = [];
    let selectedGlobalPricing = null;

    // Cargar precios globales al inicializar
    function loadGlobalPricings() {
        fetch('/superadmin/api/pricing/active')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    globalPricings = data.pricings || [data.pricing];
                    updateGlobalPricingSelect();
                }
            })
            .catch(error => {
                console.error('Error cargando precios globales:', error);
            });
    }

    // Actualizar selector de precios globales
    function updateGlobalPricingSelect() {
        const select = document.getElementById('global_pricing_id');
        if (!select) return;

        select.innerHTML = '<option value="">Seleccionar precio global</option>';
        
        globalPricings.forEach(pricing => {
            const option = document.createElement('option');
            option.value = pricing.id;
            option.textContent = `${pricing.name} - $${pricing.final_price.toLocaleString('es-CO')} / ${pricing.price_type === 'nightly' ? 'noche' : 'día'}`;
            select.appendChild(option);
        });
    }

    // Calcular precio basado en el método seleccionado
    function calculatePrice() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        const propertyId = propertySelect.value;
        const guests = parseInt(guestsInput.value) || 1;
        const pricingMethod = document.getElementById('pricing_method').value;

        if (startDate && endDate && propertyId && startDate < endDate) {
            const nights = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
            
            if (pricingMethod === 'global' && selectedGlobalPricing) {
                calculateGlobalPrice(nights, selectedGlobalPricing);
            } else if (pricingMethod === 'manual') {
                calculateManualPrice(nights);
            }
            
            updatePriceSummary(nights);
        }
    }

    // Calcular precio usando precio global
    function calculateGlobalPrice(nights, pricing) {
        const basePrice = pricing.final_price;
        const totalPrice = nights * basePrice;
        
        // Actualizar campos
        document.getElementById('global_price_per_unit').value = basePrice.toLocaleString('es-CO');
        document.getElementById('total_price').value = totalPrice;
        
        // Actualizar información del precio
        updatePriceInfo(pricing, nights, totalPrice);
    }

    // Calcular precio manual
    function calculateManualPrice(nights) {
        const manualNightsInput = document.getElementById('manual_nights');
        if (manualNightsInput) {
            manualNightsInput.value = nights;
        }
    }

    // Actualizar información del precio en la interfaz
    function updatePriceInfo(pricing, nights, totalPrice) {
        const priceInfo = document.getElementById('price-info');
        if (priceInfo) {
            priceInfo.innerHTML = `
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Información del Precio</h6>
                    <p class="mb-1"><strong>Precio Global:</strong> ${pricing.name}</p>
                    <p class="mb-1"><strong>Precio por ${pricing.price_type === 'nightly' ? 'noche' : 'día'}:</strong> $${pricing.final_price.toLocaleString('es-CO')}</p>
                    <p class="mb-1"><strong>Noches:</strong> ${nights}</p>
                    <p class="mb-0"><strong>Total:</strong> $${totalPrice.toLocaleString('es-CO')}</p>
                </div>
            `;
        }
    }

    // Actualizar resumen del precio
    function updatePriceSummary(nights) {
        const summary = document.getElementById('price_summary');
        const pricingMethod = document.getElementById('pricing_method').value;
        const totalPrice = document.getElementById('total_price').value;
        
        if (!summary) return;
        
        if (pricingMethod === 'global' && selectedGlobalPricing) {
            const totalPriceNum = parseFloat(totalPrice) || 0;
            summary.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Método:</strong> Precio Global</p>
                        <p class="mb-1"><strong>Precio seleccionado:</strong> ${selectedGlobalPricing.name}</p>
                        <p class="mb-1"><strong>Precio por ${selectedGlobalPricing.price_type === 'nightly' ? 'noche' : 'día'}:</strong> $${selectedGlobalPricing.final_price.toLocaleString('es-CO')}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Noches:</strong> ${nights}</p>
                        <p class="mb-1"><strong>Total calculado:</strong> $${totalPriceNum.toLocaleString('es-CO')}</p>
                        <p class="mb-0 text-success"><strong>Precio final:</strong> $${totalPriceNum.toLocaleString('es-CO')}</p>
                    </div>
                </div>
            `;
        } else if (pricingMethod === 'manual') {
            const totalPriceNum = parseFloat(totalPrice) || 0;
            summary.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Método:</strong> Precio Manual</p>
                        <p class="mb-1"><strong>Noches:</strong> ${nights}</p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Precio ingresado:</strong> $${totalPriceNum.toLocaleString('es-CO')}</p>
                        <p class="mb-0 text-success"><strong>Precio final:</strong> $${totalPriceNum.toLocaleString('es-CO')}</p>
                    </div>
                </div>
            `;
        } else {
            summary.innerHTML = '<p class="text-muted mb-0">Selecciona un método de precio para ver el resumen</p>';
        }
    }

    // Event listeners para el método de precio
    document.getElementById('pricing_method').addEventListener('change', function() {
        const method = this.value;
        const globalSection = document.getElementById('global_pricing_section');
        const manualSection = document.getElementById('manual_pricing_section');
        
        if (method === 'global') {
            globalSection.style.display = 'block';
            manualSection.style.display = 'none';
            loadGlobalPricings();
        } else if (method === 'manual') {
            globalSection.style.display = 'none';
            manualSection.style.display = 'block';
        } else {
            globalSection.style.display = 'none';
            manualSection.style.display = 'none';
        }
        
        calculatePrice();
    });

    // Event listener para selección de precio global
    document.getElementById('global_pricing_id').addEventListener('change', function() {
        const pricingId = this.value;
        selectedGlobalPricing = globalPricings.find(p => p.id == pricingId);
        calculatePrice();
    });

    // Event listeners para cálculo automático
    startDateInput.addEventListener('change', calculatePrice);
    endDateInput.addEventListener('change', calculatePrice);
    propertySelect.addEventListener('change', calculatePrice);
    guestsInput.addEventListener('input', calculatePrice);

    // Validar que la fecha de salida sea posterior a la de entrada
    function validateDates() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        
        if (startDate && endDate && startDate > endDate) {
            endDateInput.setCustomValidity('La fecha de salida no puede ser anterior a la fecha de entrada');
        } else {
            endDateInput.setCustomValidity('');
        }
    }

    startDateInput.addEventListener('change', validateDates);
    endDateInput.addEventListener('change', validateDates);

    // Establecer fecha mínima como hoy
    const today = new Date();
    const todayString = today.toISOString().split('T')[0];
    
    // Establecer fecha mínima para entrada como hoy (permitir hoy)
    startDateInput.min = todayString;
    
    // Establecer fecha mínima para salida (puede ser la misma fecha de entrada)
    function updateEndDateMin() {
        if (startDateInput.value) {
            const startDate = new Date(startDateInput.value);
            // Permitir la misma fecha de entrada y salida
            endDateInput.min = startDate.toISOString().split('T')[0];
        } else {
            // Si no hay fecha de entrada, permitir desde hoy
            endDateInput.min = todayString;
        }
    }
    
    // Actualizar fecha mínima de salida cuando cambie la entrada
    startDateInput.addEventListener('change', updateEndDateMin);
    
    // Inicializar fecha mínima de salida
    updateEndDateMin();
    
    // ==================== FUNCIONES DEL CALENDARIO DINÁMICO ====================
    
    function initializeCalendar() {
        renderCalendar();
        setupCalendarEventListeners();
    }
    
    function setupCalendarEventListeners() {
        document.getElementById('prevMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });
        
        document.getElementById('nextMonth').addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });
    }
    
    function renderCalendar() {
        const calendarGrid = document.getElementById('calendarGrid');
        const monthYear = document.getElementById('currentMonthYear');
        
        // Actualizar título del mes
        const monthNames = [
            'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
        ];
        monthYear.textContent = `${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}`;
        
        // Limpiar calendario
        calendarGrid.innerHTML = '';
        
        // Obtener primer día del mes y cuántos días tiene
        const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
        const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
        const daysInMonth = lastDay.getDate();
        
        // Obtener el día de la semana del primer día (0 = domingo, 1 = lunes, etc.)
        let firstDayOfWeek = firstDay.getDay();
        // Convertir a lunes = 0
        firstDayOfWeek = firstDayOfWeek === 0 ? 6 : firstDayOfWeek - 1;
        
        // Agregar días del mes anterior
        const prevMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() - 1, 0);
        for (let i = firstDayOfWeek - 1; i >= 0; i--) {
            const day = prevMonth.getDate() - i;
            const dayElement = createDayElement(day, true);
            calendarGrid.appendChild(dayElement);
        }
        
        // Agregar días del mes actual
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = createDayElement(day, false);
            calendarGrid.appendChild(dayElement);
        }
        
        // Agregar días del mes siguiente para completar la grilla
        const remainingDays = 42 - (firstDayOfWeek + daysInMonth);
        for (let day = 1; day <= remainingDays; day++) {
            const dayElement = createDayElement(day, true);
            calendarGrid.appendChild(dayElement);
        }
    }
    
    function createDayElement(day, isOtherMonth) {
        const dayElement = document.createElement('div');
        dayElement.className = 'calendar-day';
        dayElement.textContent = day;
        
        if (isOtherMonth) {
            dayElement.classList.add('other-month');
            return dayElement;
        }
        
        // Crear fecha para este día
        const dayDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), day);
        const today = new Date();
        const dateString = dayDate.toISOString().split('T')[0];
        
        // Verificar si es hoy
        if (dayDate.toDateString() === today.toDateString()) {
            dayElement.classList.add('today');
        }
        
        // Verificar si está ocupado
        if (isDateOccupied(dateString)) {
            dayElement.classList.add('occupied');
            const indicator = document.createElement('div');
            indicator.className = 'occupied-indicator';
            dayElement.appendChild(indicator);
            
            // Agregar tooltip para fechas ocupadas
            dayElement.title = 'Esta fecha está ocupada - No disponible';
        }
        
        // Verificar si está deshabilitado (fechas pasadas)
        // Comparar solo las fechas sin la hora
        const dayDateOnly = new Date(dayDate.getFullYear(), dayDate.getMonth(), dayDate.getDate());
        const todayOnly = new Date(today.getFullYear(), today.getMonth(), today.getDate());
        
        if (dayDateOnly < todayOnly) {
            dayElement.classList.add('disabled');
        }
        
        // Verificar selección
        if (selectedStartDate && dayDate.getTime() === selectedStartDate.getTime()) {
            dayElement.classList.add('selected-start');
        }
        if (selectedEndDate && dayDate.getTime() === selectedEndDate.getTime()) {
            dayElement.classList.add('selected-end');
        }
        
        // Verificar si está en el rango seleccionado
        if (selectedStartDate && selectedEndDate && 
            dayDate > selectedStartDate && dayDate < selectedEndDate) {
            dayElement.classList.add('in-range');
        }
        
        // Verificar selección múltiple
        if (isDateInMultiSelect(dayDate)) {
            dayElement.classList.add('multi-selected');
        }
        
        // Agregar indicador visual de estado de selección
        if (isSelectingEndDate && selectedStartDate && dayDate > selectedStartDate) {
            // Verificar si el rango sería válido antes de marcar como seleccionable
            if (validateDateRange(selectedStartDate, dayDate)) {
            dayElement.classList.add('selectable-end');
            dayElement.title = 'Click para seleccionar fecha de salida';
            } else {
                // Marcar como bloqueado para selección de rango
                dayElement.classList.add('blocked-range');
                dayElement.title = 'Esta fecha no se puede seleccionar - Hay fechas ocupadas en el rango';
            }
        }
        
        // Event listener para selección
        dayElement.addEventListener('click', () => handleDayClick(dayDate, dayElement));
        
        return dayElement;
    }
    
    function handleDayClick(dayDate, dayElement) {
        // No permitir selección de días ocupados o deshabilitados
        if (dayElement.classList.contains('occupied')) {
            showOccupiedDateWarning(dayDate);
            return;
        }
        
        if (dayElement.classList.contains('disabled') || 
            dayElement.classList.contains('other-month')) {
            return;
        }
        
        // No permitir selección de fechas bloqueadas para rango
        if (dayElement.classList.contains('blocked-range')) {
            showBlockedRangeWarning(dayDate);
            return;
        }
        
        const today = new Date();
        // Comparar solo las fechas sin la hora
        const dayDateOnly = new Date(dayDate.getFullYear(), dayDate.getMonth(), dayDate.getDate());
        const todayOnly = new Date(today.getFullYear(), today.getMonth(), today.getDate());
        
        if (dayDateOnly < todayOnly) {
            return;
        }
        
        // Verificar si se mantiene presionada la tecla Ctrl para selección múltiple
        if (event.ctrlKey || event.metaKey) {
            addToMultiSelect(dayDate);
            return;
        }
        
        // Lógica de selección mejorada
        if (!selectedStartDate) {
            // Primera selección: fecha de inicio
            selectedStartDate = dayDate;
            selectedEndDate = null;
            isSelectingEndDate = true;
            console.log('Seleccionada fecha de inicio:', dayDate.toLocaleDateString());
        } else if (selectedStartDate && !selectedEndDate) {
            // Segunda selección: fecha de fin - VALIDAR RANGO COMPLETO
            const startDate = selectedStartDate;
            const endDate = dayDate;
            
            // Determinar el rango correcto (inicio y fin)
            let actualStartDate, actualEndDate;
            if (dayDate.getTime() === startDate.getTime()) {
                // Misma fecha: reserva de un día
                actualStartDate = startDate;
                actualEndDate = dayDate;
            } else if (dayDate < startDate) {
                // Fecha anterior: intercambiar
                actualStartDate = dayDate;
                actualEndDate = startDate;
            } else {
                // Fecha posterior: rango normal
                actualStartDate = startDate;
                actualEndDate = dayDate;
            }
            
            // VALIDAR QUE NO HAYA FECHAS OCUPADAS EN EL RANGO
            if (validateDateRange(actualStartDate, actualEndDate)) {
                selectedStartDate = actualStartDate;
                selectedEndDate = actualEndDate;
                isSelectingEndDate = false;
                console.log('Rango válido seleccionado - Inicio:', selectedStartDate.toLocaleDateString(), 'Fin:', selectedEndDate.toLocaleDateString());
            } else {
                // Mostrar error y sugerir fechas alternativas
                suggestAlternativeDates(actualStartDate, actualEndDate);
                return;
            }
        } else {
            // Ya hay ambas fechas seleccionadas: reiniciar con nueva fecha de inicio
            selectedStartDate = dayDate;
            selectedEndDate = null;
            isSelectingEndDate = true;
            console.log('Reiniciando selección con nueva fecha de inicio:', dayDate.toLocaleDateString());
        }
        
        updateDateDisplay();
        renderCalendar();
        
        // Verificar disponibilidad solo si tenemos ambas fechas
        if (selectedStartDate && selectedEndDate) {
            checkDateAvailability();
        }
    }
    
    function updateDateDisplay() {
        const startDateText = document.getElementById('startDateText');
        const endDateText = document.getElementById('endDateText');
        const selectionStatus = document.getElementById('selectionStatus');
        const statusText = document.getElementById('statusText');
        const endDateIndicator = document.getElementById('endDateIndicator');
        
        if (selectedStartDate) {
            startDateInput.value = selectedStartDate.toISOString().split('T')[0];
            startDateText.textContent = selectedStartDate.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        } else {
            startDateInput.value = '';
            startDateText.textContent = 'Selecciona fecha';
        }
        
        if (selectedEndDate) {
            endDateInput.value = selectedEndDate.toISOString().split('T')[0];
            endDateText.textContent = selectedEndDate.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        } else {
            endDateInput.value = '';
            endDateText.textContent = 'Selecciona fecha';
        }
        
        // Mostrar estado de selección
        if (selectedStartDate && !selectedEndDate) {
            selectionStatus.style.display = 'block';
            endDateIndicator.style.display = 'block';
            const startDateFormatted = selectedStartDate.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            statusText.innerHTML = `
                <i class="fas fa-check-circle text-success me-2"></i>
                <strong>Fecha de entrada seleccionada:</strong> ${startDateFormatted}<br>
                <small class="text-muted">Ahora haz click en la fecha de salida</small>
            `;
            selectionStatus.querySelector('.alert').className = 'alert alert-info py-3';
        } else if (selectedStartDate && selectedEndDate) {
            selectionStatus.style.display = 'block';
            endDateIndicator.style.display = 'none';
            const startDateFormatted = selectedStartDate.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            const endDateFormatted = selectedEndDate.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            const nights = Math.ceil((selectedEndDate - selectedStartDate) / (1000 * 60 * 60 * 24));
            statusText.innerHTML = `
                <i class="fas fa-calendar-check text-success me-2"></i>
                <strong>Rango seleccionado:</strong> ${startDateFormatted} - ${endDateFormatted}<br>
                <small class="text-success">${nights} noche(s) seleccionada(s)</small>
            `;
            selectionStatus.querySelector('.alert').className = 'alert alert-success py-3';
        } else {
            selectionStatus.style.display = 'none';
            endDateIndicator.style.display = 'none';
        }
    }
    
    // ==================== FUNCIONES DE SELECCIÓN MÚLTIPLE ====================
    
    function initializeMultiSelectControls() {
        document.getElementById('clearSelection').addEventListener('click', clearAllSelections);
        document.getElementById('selectToday').addEventListener('click', selectToday);
        document.getElementById('selectWeekend').addEventListener('click', selectWeekend);
    }
    
    function clearAllSelections() {
        selectedStartDate = null;
        selectedEndDate = null;
        selectedDates = [];
        isSelectingEndDate = false;
        multiSelectMode = false;
        updateDateDisplay();
        renderCalendar();
    }
    
    function selectToday() {
        const today = new Date();
        selectedStartDate = today;
        selectedEndDate = null;
        isSelectingEndDate = true;
        updateDateDisplay();
        renderCalendar();
    }
    
    function selectWeekend() {
        const today = new Date();
        const currentDay = today.getDay();
        
        // Encontrar el próximo sábado
        const daysUntilSaturday = (6 - currentDay) % 7;
        const saturday = new Date(today);
        saturday.setDate(today.getDate() + (daysUntilSaturday === 0 ? 7 : daysUntilSaturday));
        
        // Encontrar el próximo domingo
        const sunday = new Date(saturday);
        sunday.setDate(saturday.getDate() + 1);
        
        selectedStartDate = saturday;
        selectedEndDate = sunday;
        isSelectingEndDate = false;
        updateDateDisplay();
        renderCalendar();
        checkDateAvailability();
    }
    
    function toggleMultiSelectMode() {
        multiSelectMode = !multiSelectMode;
        if (multiSelectMode) {
            selectedDates = [];
            selectedStartDate = null;
            selectedEndDate = null;
        }
        renderCalendar();
    }
    
    function addToMultiSelect(date) {
        const dateString = date.toISOString().split('T')[0];
        const index = selectedDates.findIndex(d => d.toISOString().split('T')[0] === dateString);
        
        if (index > -1) {
            selectedDates.splice(index, 1);
        } else {
            selectedDates.push(date);
        }
        
        // Ordenar fechas
        selectedDates.sort((a, b) => a - b);
        
        // Actualizar fechas de inicio y fin
        if (selectedDates.length > 0) {
            selectedStartDate = selectedDates[0];
            selectedEndDate = selectedDates[selectedDates.length - 1];
        } else {
            selectedStartDate = null;
            selectedEndDate = null;
        }
        
        updateDateDisplay();
        renderCalendar();
    }
    
    function isDateInMultiSelect(date) {
        const dateString = date.toISOString().split('T')[0];
        return selectedDates.some(d => d.toISOString().split('T')[0] === dateString);
    }
    
    function isDateInRange(date) {
        if (!selectedStartDate || !selectedEndDate) return false;
        return date >= selectedStartDate && date <= selectedEndDate;
    }

    // Manejar el estado del envío de correo
    const emailCheckbox = document.getElementById('send_email');
    const emailStatus = document.getElementById('email-status');
    const emailStatusText = document.getElementById('email-status-text');
    const emailStatusAlert = document.getElementById('email-status-alert');
    const emailStatusIcon = document.getElementById('email-status-icon');

    function updateEmailStatus(message, type, icon) {
        emailStatusText.textContent = message;
        emailStatusAlert.className = `alert alert-${type} d-flex align-items-center`;
        emailStatusIcon.className = `fas fa-${icon} me-2`;
    }

    emailCheckbox.addEventListener('change', function() {
        if (this.checked) {
            emailStatus.style.display = 'block';
            updateEmailStatus('Correo se enviará al crear la reserva', 'info', 'info-circle');
        } else {
            emailStatus.style.display = 'none';
        }
    });

    // Mostrar estado inicial si está marcado
    if (emailCheckbox.checked) {
        emailStatus.style.display = 'block';
        updateEmailStatus('Correo se enviará al crear la reserva', 'info', 'info-circle');
    }

    // Manejar el envío del formulario
    document.getElementById('manualReservationForm').addEventListener('submit', function(e) {
        if (emailCheckbox.checked) {
            updateEmailStatus('Enviando correo...', 'warning', 'spinner fa-spin');
        }
    });
});

// Función para cargar datos de reserva pendiente desde localStorage
function loadPendingReservationData() {
    const pendingReservation = localStorage.getItem('pendingReservation');
    if (pendingReservation) {
        try {
            const data = JSON.parse(pendingReservation);
            console.log('Datos de reserva pendiente encontrados:', data);
            
            // Cargar datos en el formulario
            const propertySelect = document.getElementById('property_id');
            const startDateInput = document.getElementById('start_date');
            const endDateInput = document.getElementById('end_date');
            
            if (propertySelect && data.property_id) {
                propertySelect.value = data.property_id;
                // Disparar evento change para cargar fechas ocupadas
                propertySelect.dispatchEvent(new Event('change'));
            }
            
            if (startDateInput && data.start_date) {
                startDateInput.value = data.start_date;
            }
            
            if (endDateInput && data.end_date) {
                endDateInput.value = data.end_date;
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
</script>
@endpush
@endsection
