<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsProxyController extends Controller
{
    private string $apiBaseUrl;
    private string $apiToken;

    public function __construct()
    {
        $this->apiBaseUrl = config('app.sms_api_base_url', env('SMS_API_BASE_URL'));
        $this->apiToken = config('app.sms_api_token', env('SMS_API_TOKEN'));
    }

    /**
     * @param Request $request
     * @param string $action API Method (getNumber, getSms и т.д.)
     * @return JsonResponse
     */
    public function handle(Request $request, string $action): JsonResponse
    {
        $queryParams = $request->all();
        $queryParams['action'] = $action;
        $queryParams['token'] = $this->apiToken;

        try {
            $response = Http::get($this->apiBaseUrl, $queryParams);
            return response()->json($response->json(), $response->status());
        } catch (\Exception $e) {
            Log::error('SMS API Connection Error: ' . $e->getMessage());

            return response()->json([
                'code' => 'error',
                'message' => 'Failed to connect to the SMS service provider.'
            ], 503);
        }
    }
}
