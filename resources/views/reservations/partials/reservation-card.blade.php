<div class="col">
    <div class="reservation-card-modern h-100 {{ $highlight ? 'highlighted' : '' }}">
        <!-- Header de la tarjeta -->
        <div class="reservation-header">
            <div class="reservation-status">
                {!! $reservation->status_badge !!}
            </div>
            <div class="reservation-actions">
                <div class="dropdown">
                    <button class="btn btn-link btn-sm text-muted" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('properties.show', $reservation->property) }}">
                                <i class="fas fa-eye me-2"></i>Ver Propiedad
                            </a>
                        </li>
                        @if($reservation->canBeCancelled())
                            <li>
                                <button class="dropdown-item text-danger" 
                                        onclick="cancelReservation({{ $reservation->id }})">
                                    <i class="fas fa-times me-2"></i>Cancelar Reserva
                                </button>
                            </li>
                        @endif
                        <li>
                            <a class="dropdown-item" href="#" onclick="showReservationDetails({{ $reservation->id }})">
                                <i class="fas fa-info-circle me-2"></i>Ver Detalles
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Imagen de la propiedad -->
        <div class="property-image-section">
            @if($reservation->property->primaryImage)
                <img src="{{ $reservation->property->primaryImage->full_url }}" 
                     class="property-image" 
                     alt="{{ $reservation->property->name }}">
            @else
                <div class="property-placeholder">
                    <i class="fas fa-home"></i>
                </div>
            @endif
            
            <!-- Overlay con información rápida -->
            <div class="image-overlay">
                <div class="overlay-content">
                    <div class="property-type-badge">
                        {{ ucfirst($reservation->property->type) }}
                    </div>
                    <div class="price-badge">
                        <div class="price-amount">${{ number_format($reservation->total_price, 0) }}</div>
                        <div class="price-period">{{ $reservation->nights }} noche(s)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido de la reserva -->
        <div class="reservation-content">
            <!-- Información de la propiedad -->
            <div class="property-info mb-3">
                <h5 class="property-name">{{ $reservation->property->name }}</h5>
                <div class="property-location">
                    <i class="fas fa-map-marker-alt"></i>
                    @if($reservation->property->city)
                        <strong>{{ $reservation->property->city->name }}</strong>, {{ $reservation->property->city->department->name }}
                    @else
                        {{ $reservation->property->location }}
                    @endif
                </div>
            </div>

            <!-- Fechas de la reserva -->
            <div class="dates-section mb-3">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="date-item">
                            <div class="date-icon arrival">
                                <i class="fas fa-sign-in-alt"></i>
                            </div>
                            <div class="date-info">
                                <div class="date-label">Llegada</div>
                                <div class="date-value">{{ $reservation->start_date->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="date-item">
                            <div class="date-icon departure">
                                <i class="fas fa-sign-out-alt"></i>
                            </div>
                            <div class="date-info">
                                <div class="date-label">Salida</div>
                                <div class="date-value">{{ $reservation->end_date->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles adicionales -->
            @if($reservation->special_requests)
                <div class="special-requests mb-3">
                    <div class="requests-header">
                        <i class="fas fa-comment-dots me-2"></i>
                        <span class="fw-semibold">Solicitudes especiales:</span>
                    </div>
                    <div class="requests-content">
                        {{ $reservation->special_requests }}
                    </div>
                </div>
            @endif

            @if($reservation->status === 'rejected' && $reservation->rejection_reason)
                <div class="rejection-alert mb-3">
                    <div class="alert alert-danger border-0">
                        <div class="d-flex align-items-start">
                            <i class="fas fa-exclamation-triangle me-2 mt-1"></i>
                            <div>
                                <strong>Motivo del rechazo:</strong>
                                <p class="mb-0 mt-1">{{ $reservation->rejection_reason }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Footer de la tarjeta -->
            <div class="reservation-footer">
                <div class="reservation-meta">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Solicitado el {{ $reservation->created_at->format('d/m/Y H:i') }}
                    </small>
                </div>
                
                <div class="reservation-buttons">
                    <a href="{{ route('properties.show', $reservation->property) }}" 
                       class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-eye me-1"></i>Ver Propiedad
                    </a>
                    
                    @if($reservation->canBeCancelled())
                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                onclick="cancelReservation({{ $reservation->id }})">
                            <i class="fas fa-times me-1"></i>Cancelar
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
