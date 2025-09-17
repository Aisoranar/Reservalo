<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestLogin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:test {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test user authentication';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("❌ Usuario no encontrado: {$email}");
            return 1;
        }
        
        if (!$user->is_active) {
            $this->error("❌ Usuario inactivo: {$email}");
            return 1;
        }
        
        if (!Hash::check($password, $user->password)) {
            $this->error("❌ Contraseña incorrecta para: {$email}");
            return 1;
        }
        
        $this->info("✅ Login exitoso!");
        $this->line("👤 Usuario: {$user->name}");
        $this->line("📧 Email: {$user->email}");
        $this->line("🔑 Rol: {$user->role}");
        $this->line("📊 Estado: " . ($user->is_active ? 'Activo' : 'Inactivo'));
        
        if ($user->current_membership_id) {
            $this->line("💎 Membresía: Activa");
        } else {
            $this->line("💎 Membresía: Sin membresía activa");
        }
        
        return 0;
    }
}
