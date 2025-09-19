<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailTemplate;

class TestEmailTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-template {template_name=reservation_created_temp_account}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email template processing with sample data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $templateName = $this->argument('template_name');
        
        $this->info("Testing template: {$templateName}");
        
        $template = EmailTemplate::where('name', $templateName)->first();
        
        if (!$template) {
            $this->error("Template '{$templateName}' not found!");
            return 1;
        }
        
        $this->info("Template found: {$template->display_name}");
        
        // Datos de prueba
        $testData = [
            'user_name' => 'Juan Pérez',
            'property_title' => 'Casa de Playa',
            'property_location' => 'Cartagena, Colombia',
            'check_in_date' => '25/09/2025',
            'check_in_time' => '15:00',
            'check_out_date' => '28/09/2025',
            'check_out_time' => '11:00',
            'guests' => '2',
            'nights' => '3',
            'total_amount' => '450.000',
            'temp_email' => 'juan@example.com',
            'temp_password' => 'temp123',
            'login_url' => 'http://127.0.0.1:8000/login',
            'special_requests' => 'Cama extra',
            'admin_notes' => 'Cliente VIP'
        ];
        
        $this->info("Processing template with test data...");
        
        $processed = $template->process($testData);
        
        $this->info("Subject: " . $processed['subject']);
        
        // Verificar si quedan variables sin procesar
        $hasUnprocessedVariables = strpos($processed['body'], '{{') !== false;
        
        if ($hasUnprocessedVariables) {
            $this->error("❌ Template still contains unprocessed variables!");
            
            // Encontrar variables no procesadas
            preg_match_all('/\{\{[^}]+\}\}/', $processed['body'], $matches);
            $unprocessed = array_unique($matches[0]);
            
            $this->warn("Unprocessed variables found:");
            foreach ($unprocessed as $var) {
                $this->line("  - {$var}");
            }
            
            // Mostrar las variables originales en la plantilla
            $this->info("\nOriginal template variables:");
            preg_match_all('/\{\{[^}]+\}\}/', $template->body, $originalMatches);
            $originalVars = array_unique($originalMatches[0]);
            foreach ($originalVars as $var) {
                $this->line("  - {$var}");
            }
            
            // Mostrar las claves de datos disponibles
            $this->info("\nAvailable data keys:");
            foreach (array_keys($testData) as $key) {
                $this->line("  - {$key}");
            }
        } else {
            $this->info("✅ Template processed successfully - no unprocessed variables found!");
        }
        
        // Mostrar una muestra del cuerpo procesado
        $this->info("\nSample of processed body (first 500 characters):");
        $this->line(substr(strip_tags($processed['body']), 0, 500) . "...");
        
        return 0;
    }
}