<?php

namespace App\Console\Commands;

use App\Services\TelnyxWhatsAppService;
use Illuminate\Console\Command;

class ListWhatsAppTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:list-templates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lists available WhatsApp templates from Telnyx.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(TelnyxWhatsAppService $telnyx)
    {
        if (!$telnyx->isConfigured()) {
            $this->error('Telnyx is not configured. Please check your .env file.');
            return Command::FAILURE;
        }

        $this->info('Fetching WhatsApp templates from Telnyx...');

        try {
            $templates = $telnyx->listTemplates();
        } catch (\Exception $e) {
            $this->error('Failed to fetch templates:');
            $this->error($e->getMessage());
            return Command::FAILURE;
        }

        if (empty($templates)) {
            $this->info('No templates found.');
            return Command::SUCCESS;
        }

        $this->table(
            ['ID (sid)', 'Name', 'Language', 'Category', 'Status', 'Body', 'Variables'],
            array_map(function ($template) {
                return [
                    $template['sid'],
                    $template['friendly_name'],
                    $template['language'],
                    $template['category'],
                    $template['approval_status'],
                    $template['body'],
                    implode(', ', $template['variables']),
                ];
            }, $templates)
        );

        return Command::SUCCESS;
    }
}
