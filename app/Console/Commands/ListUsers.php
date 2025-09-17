<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users in the system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::all(['name', 'email', 'role', 'is_active']);
        
        $this->info('Usuarios en el sistema:');
        $this->line('');
        
        foreach ($users as $user) {
            $status = $user->is_active ? '✅ Activo' : '❌ Inactivo';
            $this->line("• {$user->name} ({$user->email}) - {$user->role} - {$status}");
        }
        
        $this->line('');
        $this->info("Total: {$users->count()} usuarios");
    }
}
