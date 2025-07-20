<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\SmsApiException;
use App\Http\Controllers\Controller;
use App\Services\SmsApiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SmsProxyController extends Controller
{
    public function __construct(private readonly SmsApiService $smsApiService)
    {
    }

    public function handle(Request $request, string $action): JsonResponse
    {
        try {
            $data = $this->smsApiService->proxyRequest(
                $action,
                $request->all()
            );

            return response()->json($data);
        } catch (SmsApiException $e) {
            Log::error('SmsApiException: ' . $e->getMessage());

            return response()->json([
                                        'code' => 'error',
                                        'message' => $e->getMessage()
                                    ], $e->getCode());
        }
    }
}
