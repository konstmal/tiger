<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SmsProxyTest extends TestCase
{
    private string $apiBaseUrl;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiBaseUrl = rtrim(env('SMS_API_BASE_URL'), '/');
    }

    public function test_proxies_get_number_request_successfully(): void
    {
        Http::fake([
                       $this->apiBaseUrl . '*' => Http::response([
                           'code' => 'ok',
                           'number' => '18181817177',
                           'activation' => '10869836',
                           'cost' => 0.01,
                       ]),
        ]);

        $response = $this->getJson('/api/v1/sms/getNumber?country=se&service=wa');

        $response
            ->assertStatus(200)
            ->assertJson([
                             'code' => 'ok',
                             'number' => '18181817177',
                             'activation' => '10869836',
                         ]);
    }

    public function test_proxies_get_sms_request_successfully(): void
    {
        Http::fake([
                       $this->apiBaseUrl . '*' => Http::response([
                                                                     'code' => 'ok',
                                                                     'sms' => '12345',
                                                                 ]),
        ]);

        $response = $this->getJson('/api/v1/sms/getSms?activation=10869836');

        $response
            ->assertStatus(200)
            ->assertJson([
                             'code' => 'ok',
                             'sms' => '12345',
                         ]);
    }

    public function test_proxies_cancel_number_request_successfully(): void
    {
        Http::fake([
                       $this->apiBaseUrl . '*' => Http::response([
                                                                     'code' => 'ok',
                                                                     'activation' => '10869836',
                                                                     'status' => 'canceled',
                                                                 ]),
        ]);

        $response = $this->getJson('/api/v1/sms/cancelNumber?activation=10869836');

        $response
            ->assertStatus(200)
            ->assertJson([
                             'status' => 'canceled'
                         ]);
    }

    public function test_proxies_error_response_correctly(): void
    {
        Http::fake([
                       $this->apiBaseUrl . '*' => Http::response([
                                                                     'code' => 'error',
                                                                     'message' => 'Number not found. Try again',
                                                                 ]),
        ]);

        $response = $this->getJson('/api/v1/sms/getNumber?country=xx&service=invalid');

        $response
            ->assertStatus(200)
            ->assertJson([
                             'code' => 'error',
                             'message' => 'Number not found. Try again',
                         ]);
    }
}
