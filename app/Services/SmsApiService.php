<?php

namespace App\Services;

use App\Exceptions\SmsApiException;
use Illuminate\Support\Facades\Http;

class SmsApiService
{
    private string $apiBaseUrl;
    private string $apiToken;

    public function __construct()
    {
        $this->apiBaseUrl = config('app.sms_api_base_url', env('SMS_API_BASE_URL'));
        $this->apiToken = config('app.sms_api_token', env('SMS_API_TOKEN'));
    }

    /**
     * @param string $action
     * @param array $queryParams
     * @return array
     * @throws SmsApiException
     */
    public function proxyRequest(string $action, array $queryParams): array
    {
        $queryParams['action'] = $action;
        $queryParams['token'] = $this->apiToken;

        try {
            return Http::get($this->apiBaseUrl, $queryParams)->json();
        } catch (\Throwable $e) {
            throw new SmsApiException('Failed to connect to the SMS service provider.', 503, $e);
        }
    }
}
