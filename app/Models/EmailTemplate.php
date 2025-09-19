<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'subject',
        'body',
        'body_text',
        'variables',
        'type',
        'is_active',
        'description'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean'
    ];

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Obtener plantilla por nombre
     */
    public static function getByName(string $name): ?self
    {
        return static::where('name', $name)->active()->first();
    }

    /**
     * Procesar plantilla con variables
     */
    public function process(array $data = []): array
    {
        $subject = $this->subject;
        $body = $this->body;
        $bodyText = $this->body_text;

        // Función para reemplazar variables en un texto
        $replaceVariables = function($text, $data) {
            foreach ($data as $key => $value) {
                // Escapar caracteres especiales para regex
                $escapedKey = preg_quote($key, '/');
                
                // Patrón regex para capturar diferentes formatos de variables
                // {{ $variable }}, {{$variable}}, {{ $variable}}, {{$variable }}
                $pattern = '/\{\{\s*\$?' . $escapedKey . '\s*\}\}/';
                
                $text = preg_replace($pattern, $value, $text);
            }
            return $text;
        };

        // Reemplazar variables en el asunto
        $subject = $replaceVariables($subject, $data);

        // Reemplazar variables en el cuerpo HTML
        $body = $replaceVariables($body, $data);

        // Reemplazar variables en el cuerpo de texto plano
        if ($bodyText) {
            $bodyText = $replaceVariables($bodyText, $data);
        }

        return [
            'subject' => $subject,
            'body' => $body,
            'body_text' => $bodyText
        ];
    }

    /**
     * Crear plantillas por defecto
     */
    public static function createDefaultTemplates(): void
    {
        $templates = [
            [
                'name' => 'reservation_approved',
                'display_name' => 'Reserva Aprobada',
                'subject' => '¡Tu reserva ha sido aprobada! - {{property_title}}',
                'body' => '
                    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
                            <h1 style="margin: 0; font-size: 28px;">¡Reserva Aprobada!</h1>
                            <p style="margin: 10px 0 0 0; opacity: 0.9;">Tu solicitud de reserva ha sido confirmada</p>
                        </div>
                        <div style="background: white; padding: 30px; border: 1px solid #e2e8f0; border-top: none;">
                            <h2 style="color: #2d3748; margin-top: 0;">Hola {{user_name}},</h2>
                            <p style="color: #4a5568; line-height: 1.6;">¡Excelentes noticias! Tu reserva ha sido aprobada y confirmada.</p>
                            
                            <div style="background: #f7fafc; padding: 20px; border-radius: 8px; margin: 20px 0;">
                                <h3 style="color: #2d3748; margin-top: 0;">Detalles de tu reserva:</h3>
                                <p><strong>Propiedad:</strong> {{property_title}}</p>
                                <p><strong>Ubicación:</strong> {{property_location}}</p>
                                <p><strong>Check-in:</strong> {{check_in_date}} a las {{check_in_time}}</p>
                                <p><strong>Check-out:</strong> {{check_out_date}} a las {{check_out_time}}</p>
                                <p><strong>Huéspedes:</strong> {{guests}} personas</p>
                                <p><strong>Noches:</strong> {{nights}}</p>
                                <p><strong>Total:</strong> ${{total_amount}}</p>
                            </div>
                            
                            @if(admin_notes)
                            <div style="background: #e6fffa; padding: 15px; border-radius: 8px; border-left: 4px solid #38b2ac;">
                                <h4 style="color: #2c7a7b; margin-top: 0;">Notas del administrador:</h4>
                                <p style="color: #2c7a7b; margin: 0;">{{admin_notes}}</p>
                            </div>
                            @endif
                            
                            <p style="color: #4a5568; line-height: 1.6;">¡Esperamos que disfrutes tu estadía!</p>
                            <p style="color: #4a5568;">El equipo de Reservalo</p>
                        </div>
                        <div style="background: #f7fafc; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; border: 1px solid #e2e8f0; border-top: none;">
                            <p style="color: #718096; font-size: 14px; margin: 0;">© 2024 Reservalo. Todos los derechos reservados.</p>
                        </div>
                    </div>
                ',
                'body_text' => '
                    ¡Reserva Aprobada!
                    
                    Hola {{user_name}},
                    
                    ¡Excelentes noticias! Tu reserva ha sido aprobada y confirmada.
                    
                    Detalles de tu reserva:
                    - Propiedad: {{property_title}}
                    - Ubicación: {{property_location}}
                    - Check-in: {{check_in_date}} a las {{check_in_time}}
                    - Check-out: {{check_out_date}} a las {{check_out_time}}
                    - Huéspedes: {{guests}} personas
                    - Noches: {{nights}}
                    - Total: ${{total_amount}}
                    
                    @if(admin_notes)
                    Notas del administrador:
                    {{admin_notes}}
                    @endif
                    
                    ¡Esperamos que disfrutes tu estadía!
                    
                    El equipo de Reservalo
                ',
                'variables' => [
                    'user_name' => 'Nombre del usuario',
                    'property_title' => 'Título de la propiedad',
                    'property_location' => 'Ubicación de la propiedad',
                    'check_in_date' => 'Fecha de check-in',
                    'check_in_time' => 'Hora de check-in',
                    'check_out_date' => 'Fecha de check-out',
                    'check_out_time' => 'Hora de check-out',
                    'guests' => 'Número de huéspedes',
                    'nights' => 'Número de noches',
                    'total_amount' => 'Monto total',
                    'admin_notes' => 'Notas del administrador'
                ],
                'type' => 'reservation',
                'description' => 'Plantilla para notificar cuando una reserva es aprobada'
            ],
            [
                'name' => 'reservation_rejected',
                'display_name' => 'Reserva Rechazada',
                'subject' => 'Reserva rechazada - {{property_title}}',
                'body' => '
                    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                        <div style="background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
                            <h1 style="margin: 0; font-size: 28px;">Reserva Rechazada</h1>
                            <p style="margin: 10px 0 0 0; opacity: 0.9;">Tu solicitud de reserva no pudo ser procesada</p>
                        </div>
                        <div style="background: white; padding: 30px; border: 1px solid #e2e8f0; border-top: none;">
                            <h2 style="color: #2d3748; margin-top: 0;">Hola {{user_name}},</h2>
                            <p style="color: #4a5568; line-height: 1.6;">Lamentamos informarte que tu reserva no pudo ser aprobada en esta ocasión.</p>
                            
                            <div style="background: #f7fafc; padding: 20px; border-radius: 8px; margin: 20px 0;">
                                <h3 style="color: #2d3748; margin-top: 0;">Detalles de la reserva:</h3>
                                <p><strong>Propiedad:</strong> {{property_title}}</p>
                                <p><strong>Ubicación:</strong> {{property_location}}</p>
                                <p><strong>Check-in:</strong> {{check_in_date}}</p>
                                <p><strong>Check-out:</strong> {{check_out_date}}</p>
                                <p><strong>Huéspedes:</strong> {{guests}} personas</p>
                            </div>
                            
                            <div style="background: #fed7d7; padding: 15px; border-radius: 8px; border-left: 4px solid #e53e3e;">
                                <h4 style="color: #c53030; margin-top: 0;">Motivo del rechazo:</h4>
                                <p style="color: #c53030; margin: 0;">{{rejection_reason}}</p>
                            </div>
                            
                            <p style="color: #4a5568; line-height: 1.6;">Te invitamos a explorar otras opciones disponibles en nuestra plataforma.</p>
                            <p style="color: #4a5568;">El equipo de Reservalo</p>
                        </div>
                        <div style="background: #f7fafc; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; border: 1px solid #e2e8f0; border-top: none;">
                            <p style="color: #718096; font-size: 14px; margin: 0;">© 2024 Reservalo. Todos los derechos reservados.</p>
                        </div>
                    </div>
                ',
                'body_text' => '
                    Reserva Rechazada
                    
                    Hola {{user_name}},
                    
                    Lamentamos informarte que tu reserva no pudo ser aprobada en esta ocasión.
                    
                    Detalles de la reserva:
                    - Propiedad: {{property_title}}
                    - Ubicación: {{property_location}}
                    - Check-in: {{check_in_date}}
                    - Check-out: {{check_out_date}}
                    - Huéspedes: {{guests}} personas
                    
                    Motivo del rechazo:
                    {{rejection_reason}}
                    
                    Te invitamos a explorar otras opciones disponibles en nuestra plataforma.
                    
                    El equipo de Reservalo
                ',
                'variables' => [
                    'user_name' => 'Nombre del usuario',
                    'property_title' => 'Título de la propiedad',
                    'property_location' => 'Ubicación de la propiedad',
                    'check_in_date' => 'Fecha de check-in',
                    'check_out_date' => 'Fecha de check-out',
                    'guests' => 'Número de huéspedes',
                    'rejection_reason' => 'Motivo del rechazo'
                ],
                'type' => 'reservation',
                'description' => 'Plantilla para notificar cuando una reserva es rechazada'
            ]
        ];

        foreach ($templates as $template) {
            static::updateOrCreate(
                ['name' => $template['name']],
                $template
            );
        }
    }
}