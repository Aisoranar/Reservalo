<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class TempAccountEmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        EmailTemplate::firstOrCreate(
            ['name' => 'reservation_created_temp_account'],
            [
                'display_name' => 'Reserva Creada con Cuenta Temporal',
                'subject' => 'Reserva Confirmada - Cuenta Temporal Creada',
                'body' => '
                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                    <div style="background-color: #28a745; color: white; padding: 20px; text-align: center;">
                        <h1>¡Reserva Confirmada!</h1>
                    </div>
                    
                    <div style="padding: 30px; background-color: #f8f9fa;">
                        <h2>Hola {{ $user_name }},</h2>
                        
                        <p>Tu reserva ha sido confirmada exitosamente. Se ha creado una cuenta temporal para ti en nuestro sistema.</p>
                        
                        <div style="background-color: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #28a745;">
                            <h3 style="color: #28a745; margin-top: 0;">Detalles de la Reserva</h3>
                            <p><strong>Propiedad:</strong> {{ $property_title }}</p>
                            <p><strong>Ubicación:</strong> {{ $property_location }}</p>
                            <p><strong>Fecha de Entrada:</strong> {{ $check_in_date }} a las {{ $check_in_time }}</p>
                            <p><strong>Fecha de Salida:</strong> {{ $check_out_date }} a las {{ $check_out_time }}</p>
                            <p><strong>Huéspedes:</strong> {{ $guests }}</p>
                            <p><strong>Noches:</strong> {{ $nights }}</p>
                            <p><strong>Total:</strong> ${{ $total_amount }}</p>
                        </div>
                        
                        <div style="background-color: #fff3cd; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #ffc107;">
                            <h3 style="color: #856404; margin-top: 0;">🔐 Cuenta Temporal Creada</h3>
                            <p>Se ha creado una cuenta temporal para ti con los siguientes datos:</p>
                            <ul style="list-style: none; padding: 0;">
                                <li style="margin: 10px 0;"><strong>Email:</strong> {{ $temp_email }}</li>
                                <li style="margin: 10px 0;"><strong>Contraseña Temporal:</strong> <code style="background-color: #e9ecef; padding: 4px 8px; border-radius: 4px;">{{ $temp_password }}</code></li>
                            </ul>
                            <p><strong>⚠️ Importante:</strong> Debes cambiar esta contraseña temporal en tu primer inicio de sesión por seguridad.</p>
                        </div>
                        
                        <div style="text-align: center; margin: 30px 0;">
                            <a href="{{ $login_url }}" style="background-color: #28a745; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; display: inline-block; font-weight: bold;">
                                Iniciar Sesión
                            </a>
                        </div>
                        
                        @if($special_requests)
                        <div style="background-color: #e7f3ff; padding: 15px; border-radius: 8px; margin: 20px 0;">
                            <h4 style="color: #004085; margin-top: 0;">Solicitudes Especiales</h4>
                            <p>{{ $special_requests }}</p>
                        </div>
                        @endif
                        
                        @if($admin_notes)
                        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 8px; margin: 20px 0; border: 1px solid #dee2e6;">
                            <h4 style="color: #495057; margin-top: 0;">Notas del Administrador</h4>
                            <p>{{ $admin_notes }}</p>
                        </div>
                        @endif
                        
                        <p>Si tienes alguna pregunta, no dudes en contactarnos.</p>
                        
                        <p>¡Esperamos verte pronto!</p>
                        
                        <p><strong>Equipo Reservalo</strong></p>
                    </div>
                </div>',
                'body_text' => '
¡Reserva Confirmada!

Hola {{ $user_name }},

Tu reserva ha sido confirmada exitosamente. Se ha creado una cuenta temporal para ti en nuestro sistema.

DETALLES DE LA RESERVA:
- Propiedad: {{ $property_title }}
- Ubicación: {{ $property_location }}
- Fecha de Entrada: {{ $check_in_date }} a las {{ $check_in_time }}
- Fecha de Salida: {{ $check_out_date }} a las {{ $check_out_time }}
- Huéspedes: {{ $guests }}
- Noches: {{ $nights }}
- Total: ${{ $total_amount }}

CUENTA TEMPORAL CREADA:
- Email: {{ $temp_email }}
- Contraseña Temporal: {{ $temp_password }}

IMPORTANTE: Debes cambiar esta contraseña temporal en tu primer inicio de sesión por seguridad.

Iniciar Sesión: {{ $login_url }}

@if($special_requests)
SOLICITUDES ESPECIALES:
{{ $special_requests }}
@endif

@if($admin_notes)
NOTAS DEL ADMINISTRADOR:
{{ $admin_notes }}
@endif

Si tienes alguna pregunta, no dudes en contactarnos.

¡Esperamos verte pronto!

Equipo Reservalo',
                'type' => 'reservation',
                'description' => 'Plantilla para reservas creadas con cuenta temporal',
                'variables' => [
                    'user_name', 'property_title', 'property_location', 'check_in_date', 'check_in_time',
                    'check_out_date', 'check_out_time', 'guests', 'nights', 'total_amount',
                    'special_requests', 'admin_notes', 'temp_email', 'temp_password', 'login_url'
                ]
            ]
        );
    }
}
