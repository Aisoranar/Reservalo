@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <!-- Galería de imágenes -->
        @if($property->images->count() > 0)
            <div id="propertyCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @foreach($property->images as $index => $image)
                        <button type="button" data-bs-target="#propertyCarousel" 
                                data-bs-slide-to="{{ $index }}" 
                                class="{{ $index === 0 ? 'active' : '' }}"></button>
                    @endforeach
                </div>
                <div class="carousel-inner">
                    @foreach($property->images as $index => $image)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <img src="{{ $image->full_url }}" class="d-block w-100" 
                                 alt="{{ $image->alt_text }}" style="height: 400px; object-fit: cover;">
                        </div>
                    @endforeach
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#propertyCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#propertyCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>
        @endif

        <!-- Información de la propiedad -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h1 class="h2 mb-0">{{ $property->name }}</h1>
                    <div class="text-end">
                        <div class="h3 text-primary mb-0">${{ number_format($property->price, 2) }}</div>
                        <small class="text-muted">por noche</small>
                    </div>
                </div>

                <p class="text-muted mb-3">
                    <i class="fas fa-map-marker-alt me-2"></i>{{ $property->location }}
                </p>

                <div class="row mb-4">
                    <div class="col-md-3 text-center">
                        <i class="fas fa-home fa-2x text-primary mb-2"></i>
                        <div class="fw-bold">{{ ucfirst($property->type) }}</div>
                        <small class="text-muted">Tipo</small>
                    </div>
                    <div class="col-md-3 text-center">
                        <i class="fas fa-users fa-2x text-primary mb-2"></i>
                        <div class="fw-bold">{{ $property->capacity }}</div>
                        <small class="text-muted">Personas</small>
                    </div>
                    <div class="col-md-3 text-center">
                        <i class="fas fa-star fa-2x text-primary mb-2"></i>
                        <div class="fw-bold">4.8</div>
                        <small class="text-muted">Calificación</small>
                    </div>
                    <div class="col-md-3 text-center">
                        <i class="fas fa-calendar-check fa-2x text-primary mb-2"></i>
                        <div class="fw-bold">{{ $property->reservations->count() }}</div>
                        <small class="text-muted">Reservas</small>
                    </div>
                </div>

                <h5>Descripción</h5>
                <p class="text-muted">{{ $property->description }}</p>

                @if($property->services)
                    <h5 class="mt-4">Servicios incluidos</h5>
                    <div class="row">
                        @foreach($property->services as $service)
                            <div class="col-md-6 mb-2">
                                <i class="fas fa-check text-success me-2"></i>{{ $service }}
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Calendario de disponibilidad -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-calendar me-2"></i>Calendario de disponibilidad</h5>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Formulario de reserva -->
        @auth
            <div class="card sticky-top" style="top: 2rem;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Reservar ahora</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('reservations.store', $property) }}" method="POST" id="reservationForm">
                        @csrf
                        <div class="mb-3">
                            <label for="start_date" class="form-label">Fecha de llegada</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="end_date" class="form-label">Fecha de salida</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   min="{{ date('Y-m-d', strtotime('+2 days')) }}" required>
                        </div>

                        <div class="mb-3">
                            <label for="nights" class="form-label">Noches</label>
                            <input type="text" class="form-control" id="nights" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="total_price" class="form-label">Precio total</label>
                            <input type="text" class="form-control" id="total_price" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="special_requests" class="form-label">Solicitudes especiales</label>
                            <textarea class="form-control" id="special_requests" name="special_requests" 
                                      rows="3" placeholder="Algún requerimiento especial..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="submitBtn" disabled>
                            <i class="fas fa-calendar-check me-2"></i>Enviar solicitud
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                    <h5>Inicia sesión para reservar</h5>
                    <p class="text-muted">Necesitas una cuenta para hacer reservas</p>
                    <a href="{{ route('login') }}" class="btn btn-primary me-2">Iniciar Sesión</a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary">Registrarse</a>
                </div>
            </div>
        @endauth

        <!-- Información de contacto -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información importante</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-clock text-primary me-2"></i>
                        Check-in: 15:00 - Check-out: 11:00
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-credit-card text-primary me-2"></i>
                        Pago al confirmar la reserva
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-undo text-primary me-2"></i>
                        Cancelación gratuita hasta 24h antes
                    </li>
                    <li>
                        <i class="fas fa-shield-alt text-primary me-2"></i>
                        Reserva segura y confirmada
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Calendario FullCalendar
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek'
        },
        events: [
            // Aquí se cargarían las reservas existentes
            @foreach($property->reservations->where('status', 'approved') as $reservation)
            {
                title: 'Reservado',
                start: '{{ $reservation->start_date }}',
                end: '{{ $reservation->end_date }}',
                backgroundColor: '#dc3545',
                borderColor: '#dc3545'
            },
            @endforeach
        ],
        selectable: true,
        select: function(info) {
            // Aquí se podría implementar la selección de fechas
        }
    });
    calendar.render();

    // Cálculo de noches y precio total
    function calculateTotal() {
        const startDate = new Date(document.getElementById('start_date').value);
        const endDate = new Date(document.getElementById('end_date').value);
        
        if (startDate && endDate && startDate < endDate) {
            const nights = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
            const pricePerNight = {{ $property->price }};
            const totalPrice = nights * pricePerNight;
            
            document.getElementById('nights').value = nights + ' noche(s)';
            document.getElementById('total_price').value = '$' + totalPrice.toFixed(2);
            document.getElementById('submitBtn').disabled = false;
        } else {
            document.getElementById('nights').value = '';
            document.getElementById('total_price').value = '';
            document.getElementById('submitBtn').disabled = true;
        }
    }

    document.getElementById('start_date').addEventListener('change', calculateTotal);
    document.getElementById('end_date').addEventListener('change', calculateTotal);
});
</script>
@endpush
