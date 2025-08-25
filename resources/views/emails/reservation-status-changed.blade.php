<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de tu reserva actualizado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .status-badge {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 25px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .status-approved {
            background-color: #28a745;
            color: white;
        }
        .status-rejected {
            background-color: #dc3545;
            color: white;
        }
        .property-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
            color: #6c757d;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 5px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏠 Reservalo</h1>
        <p>Tu plataforma de confianza para reservas turísticas</p>
    </div>

    <div class="content">
        <h2>Hola {{ $reservation->user->name }},</h2>
        
        @if($status === 'approved')
            <p>¡Excelentes noticias! Tu solicitud de reserva ha sido <strong>APROBADA</strong>.</p>
            
            <div class="status-badge status-approved">
                ✅ Reserva Aprobada
            </div>
            
            <p>Tu reserva está confirmada y lista para disfrutar. Aquí tienes los detalles:</p>
        @else
            <p>Lamentamos informarte que tu solicitud de reserva ha sido <strong>RECHAZADA</strong>.</p>
            
            <div class="status-badge status-rejected">
                ❌ Reserva Rechazada
            </div>
            
            <p>Aquí tienes los detalles de tu solicitud:</p>
        @endif

        <div class="property-card">
            <h3>{{ $reservation->property->name }}</h3>
            <p><strong>📍 Ubicación:</strong> {{ $reservation->property->location }}</p>
            <p><strong>📅 Fechas:</strong> {{ $reservation->start_date->format('d/m/Y') }} - {{ $reservation->end_date->format('d/m/Y') }}</p>
            <p><strong>🌙 Noches:</strong> {{ $reservation->nights }}</p>
            <p><strong>💰 Precio total:</strong> ${{ number_format($reservation->total_price, 2) }}</p>
            
            @if($reservation->special_requests)
                <p><strong>📝 Solicitudes especiales:</strong> {{ $reservation->special_requests }}</p>
            @endif
        </div>

        @if($status === 'approved')
            <h3>🎉 ¡Tu reserva está confirmada!</h3>
            <p>Ahora puedes:</p>
            <ul>
                <li>Preparar tu viaje con tranquilidad</li>
                <li>Contactar al anfitrión si tienes preguntas</li>
                <li>Revisar los detalles de llegada y salida</li>
            </ul>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('reservations.index') }}" class="btn">Ver mis reservas</a>
                <a href="{{ route('properties.show', $reservation->property) }}" class="btn">Ver detalles de la propiedad</a>
            </div>

            <div style="background: #e8f5e8; padding: 20px; border-radius: 10px; margin: 20px 0;">
                <h4>📋 Información importante:</h4>
                <ul>
                    <li><strong>Check-in:</strong> 15:00 horas</li>
                    <li><strong>Check-out:</strong> 11:00 horas</li>
                    <li>Lleva tu documento de identidad</li>
                    <li>Contacta al anfitrión antes de tu llegada</li>
                </ul>
            </div>
        @else
            <h3>📋 Motivo del rechazo:</h3>
            <div style="background: #ffeaea; padding: 20px; border-radius: 10px; margin: 20px 0;">
                <p><strong>{{ $reservation->rejection_reason }}</strong></p>
            </div>

            <p>No te desanimes, tenemos muchas otras opciones disponibles para ti:</p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ route('home') }}" class="btn">Explorar otras propiedades</a>
                <a href="{{ route('reservations.index') }}" class="btn">Ver mis reservas</a>
            </div>
        @endif

        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;">
            <h4>❓ ¿Necesitas ayuda?</h4>
            <p>Nuestro equipo de soporte está disponible 24/7 para ayudarte:</p>
            <ul>
                <li><strong>📧 Email:</strong> soporte@reservalo.com</li>
                <li><strong>📱 WhatsApp:</strong> +1234567890</li>
                <li><strong>🌐 Web:</strong> <a href="{{ url('/') }}">reservalo.com</a></li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} Reservalo. Todos los derechos reservados.</p>
        <p>Este es un correo automático, por favor no respondas a este mensaje.</p>
        <p>
            <a href="{{ route('home') }}">Inicio</a> | 
            <a href="{{ route('properties.index') }}">Propiedades</a> | 
            <a href="{{ route('reservations.index') }}">Mis Reservas</a>
        </p>
    </div>
</body>
</html>
