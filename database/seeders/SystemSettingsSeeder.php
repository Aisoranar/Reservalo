<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Inicializar configuraciones por defecto
        SystemSetting::initializeDefaults();

        // Configuraciones adicionales específicas
        $additionalSettings = [
            [
                'key' => 'membership_notification_days',
                'value' => '7',
                'type' => 'integer',
                'description' => 'Días antes de expirar para notificar membresías',
                'is_public' => false
            ],
            [
                'key' => 'auto_renewal_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Habilitar renovación automática de membresías',
                'is_public' => true
            ],
            [
                'key' => 'max_trial_periods',
                'value' => '1',
                'type' => 'integer',
                'description' => 'Máximo número de períodos de prueba por usuario',
                'is_public' => false
            ],
            [
                'key' => 'membership_grace_period',
                'value' => '3',
                'type' => 'integer',
                'description' => 'Días de gracia después de expirar membresía',
                'is_public' => false
            ],
            [
                'key' => 'email_notifications_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Habilitar notificaciones por email',
                'is_public' => true
            ],
            [
                'key' => 'whatsapp_notifications_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Habilitar notificaciones por WhatsApp',
                'is_public' => true
            ],
            [
                'key' => 'sms_notifications_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Habilitar notificaciones por SMS',
                'is_public' => true
            ],
            [
                'key' => 'backup_frequency',
                'value' => 'daily',
                'type' => 'string',
                'description' => 'Frecuencia de respaldos automáticos',
                'is_public' => false
            ],
            [
                'key' => 'session_timeout',
                'value' => '120',
                'type' => 'integer',
                'description' => 'Timeout de sesión en minutos',
                'is_public' => false
            ],
            [
                'key' => 'max_login_attempts',
                'value' => '5',
                'type' => 'integer',
                'description' => 'Máximo intentos de login antes de bloquear',
                'is_public' => false
            ],
            [
                'key' => 'password_min_length',
                'value' => '8',
                'type' => 'integer',
                'description' => 'Longitud mínima de contraseña',
                'is_public' => true
            ],
            [
                'key' => 'password_require_special_chars',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Requerir caracteres especiales en contraseña',
                'is_public' => true
            ],
            [
                'key' => 'two_factor_auth_enabled',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Habilitar autenticación de dos factores',
                'is_public' => true
            ],
            [
                'key' => 'api_rate_limit',
                'value' => '100',
                'type' => 'integer',
                'description' => 'Límite de requests por minuto para API',
                'is_public' => false
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'El sistema está en mantenimiento. Volveremos pronto.',
                'type' => 'string',
                'description' => 'Mensaje mostrado durante mantenimiento',
                'is_public' => true
            ]
        ];

        foreach ($additionalSettings as $setting) {
            SystemSetting::set(
                $setting['key'],
                $setting['value'],
                $setting['type'],
                $setting['description'],
                $setting['is_public']
            );
        }
    }
}
