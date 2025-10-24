<?php

namespace App\Jobs;

use App\Services\Sms\SmsMessage;
use App\Utils\CheckEnvVariables;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendSms implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public SmsMessage $message)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $payload = $this->message->payLoad();
        try {
            $configs = [
                'base_url' => config('sms.base_url'),
                'url' => config('sms.url'),
                'api_key' => config('sms.api_key'),
                'template' => $payload['template'],
            ];
            CheckEnvVariables::checkEnvVariables($configs);

            $url = $configs['base_url'] . $configs['api_key'] . $configs['url'];
            Log::info($configs['template']);

            Http::asForm()->post($url, $payload);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
