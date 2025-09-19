<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class AddReservationDeletedEmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $template = [
            'name' => 'reservation_deleted',
            'display_name' => 'Reserva Eliminada',
            'subject' => 'Reserva eliminada - {{property_title}}',
            'body' => '
                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                    <div style="background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;">
                        <h1 style="margin: 0; font-size: 28px;">Reserva Eliminada</h1>
                        <p style="margin: 10px 0 0 0; opacity: 0.9;">Tu reserva ha sido eliminada por un administrador</p>
                    </div>
                    <div style="background: white; padding: 30px; border: 1px solid #e2e8f0; border-top: none;">
                        <h2 style="color: #2d3748; margin-top: 0;">Hola {{user_name}},</h2>
                        <p style="color: #4a5568; line-height: 1.6;">Lamentamos informarte que tu reserva ha sido eliminada por un administrador del sistema.</p>
                        
                        <div style="background: #fed7d7; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #e53e3e;">
                            <h3 style="color: #c53030; margin-top: 0;">Motivo de la eliminación:</h3>
                            <p style="color: #742a2a; margin: 0;">{{deletion_reason}}</p>
                        </div>
                        
                        <div style="background: #f7fafc; padding: 20px; border-radius: 8px; margin: 20px 0;">
                            <h3 style="color: #2d3748; margin-top: 0;">Detalles de la reserva eliminada:</h3>
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="padding: 8px 0; color: #4a5568; font-weight: bold; width: 40%;">Propiedad:</td>
                                    <td style="padding: 8px 0; color: #2d3748;">{{property_title}}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #4a5568; font-weight: bold;">Ubicación:</td>
                                    <td style="padding: 8px 0; color: #2d3748;">{{property_location}}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #4a5568; font-weight: bold;">Check-in:</td>
                                    <td style="padding: 8px 0; color: #2d3748;">{{check_in_date}}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #4a5568; font-weight: bold;">Check-out:</td>
                                    <td style="padding: 8px 0; color: #2d3748;">{{check_out_date}}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; color: #4a5568; font-weight: bold;">Huéspedes:</td>
                                    <td style="padding: 8px 0; color: #2d3748;">{{guests}}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div style="background: #e6fffa; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #38b2ac;">
                            <h3 style="color: #2c7a7b; margin-top: 0;">¿Qué puedes hacer ahora?</h3>
                            <ul style="color: #2d3748; margin: 0; padding-left: 20px;">
                                <li>Crear una nueva reserva si lo deseas</li>
                                <li>Contactar con el soporte si tienes preguntas</li>
                                <li>Revisar nuestras políticas de reservas</li>
                            </ul>
                        </div>
                        
                        <p style="color: #4a5568; line-height: 1.6;">Si tienes alguna pregunta sobre esta eliminación, no dudes en contactarnos.</p>
                        
                        <div style="text-align: center; margin-top: 30px;">
                            <a href="{{app_url}}/reservations" style="background: #4299e1; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;">Ver Mis Reservas</a>
                        </div>
                    </div>
                    <div style="background: #f7fafc; padding: 20px; text-align: center; border-radius: 0 0 10px 10px; border: 1px solid #e2e8f0; border-top: none;">
                        <p style="color: #718096; font-size: 14px; margin: 0;">Este es un correo automático, por favor no respondas a este mensaje.</p>
                    </div>
                </div>
            ',
            'body_text' => '
                RESERVA ELIMINADA
                
                Hola {{user_name}},
                
                Lamentamos informarte que tu reserva ha sido eliminada por un administrador del sistema.
                
                MOTIVO DE LA ELIMINACIÓN:
                {{deletion_reason}}
                
                DETALLES DE LA RESERVA ELIMINADA:
                Propiedad: {{property_title}}
                Ubicación: {{property_location}}
                Check-in: {{check_in_date}}
                Check-out: {{check_out_date}}
                Huéspedes: {{guests}}
                
                ¿QUÉ PUEDES HACER AHORA?
                - Crear una nueva reserva si lo deseas
                - Contactar con el soporte si tienes preguntas
                - Revisar nuestras políticas de reservas
                
                Si tienes alguna pregunta sobre esta eliminación, no dudes en contactarnos.
                
                Ver Mis Reservas: {{app_url}}/reservations
                
                ---
                Este es un correo automático, por favor no respondas a este mensaje.
            ',
            'variables' => [
                'user_name' => 'Nombre del usuario',
                'property_title' => 'Título de la propiedad',
                'property_location' => 'Ubicación de la propiedad',
                'check_in_date' => 'Fecha de check-in',
                'check_out_date' => 'Fecha de check-out',
                'guests' => 'Número de huéspedes',
                'deletion_reason' => 'Motivo de la eliminación',
                'app_url' => 'URL de la aplicación'
            ],
            'type' => 'reservation',
            'description' => 'Plantilla para notificar cuando una reserva es eliminada por un administrador'
        ];

        EmailTemplate::updateOrCreate(
            ['name' => $template['name']],
            $template
        );

        $this->command->info('Template de correo para reserva eliminada creado correctamente');
    }
}