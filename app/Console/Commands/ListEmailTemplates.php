<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailTemplate;

class ListEmailTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:templates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all available email templates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $templates = EmailTemplate::all(['name', 'display_name', 'is_active']);
        
        $this->info("Available Email Templates:");
        $this->line("");
        
        foreach ($templates as $template) {
            $status = $template->is_active ? '✅ Active' : '❌ Inactive';
            $this->line("- {$template->name} ({$template->display_name}) - {$status}");
        }
        
        return 0;
    }
}