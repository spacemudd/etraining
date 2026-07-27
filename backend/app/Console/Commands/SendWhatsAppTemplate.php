<?php

namespace App\Console\Commands;

use App\Services\TelnyxWhatsAppService;
use Illuminate\Console\Command;

class SendWhatsAppTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:send-template {phone} {template_id} {--variable=*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a WhatsApp template message via Telnyx. Use --variable="1:value" for template variables.';

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

        $phone = $this->argument('phone');
        $templateId = $this->argument('template_id');
        $variables = $this->option('variable');

        $contentVariables = [];
        foreach ($variables as $variable) {
            $parts = explode(':', $variable, 2);
            if (count($parts) === 2) {
                $contentVariables[$parts[0]] = $parts[1];
            }
        }

        $this->info("Sending template '{$templateId}' to '{$phone}'...");
        if ($contentVariables) {
            $this->info('With variables: ' . json_encode($contentVariables));
        }

        try {
            $result = $telnyx->sendTemplate($phone, $templateId, $contentVariables);
            $this->info('API call successful. See result below:');
            $this->line(json_encode($result, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            $this->error('Failed to send message:');
            $this->error($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
