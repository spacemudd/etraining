<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\NoonService;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class NoonServiceTest extends TestCase
{
    public function test_checkout_url_from_successful_response(): void
    {
        $response = (object) [
            'resultCode' => 0,
            'result' => (object) [
                'checkoutData' => (object) [
                    'postUrl' => 'https://pay.example.test/checkout',
                ],
            ],
        ];

        $this->assertSame(
            'https://pay.example.test/checkout',
            (new NoonService())->checkoutUrlFromResponse($response)
        );
    }

    public function test_checkout_url_throws_on_null_response(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Noon payment returned an empty response');

        (new NoonService())->checkoutUrlFromResponse(null);
    }

    public function test_checkout_url_throws_on_missing_result_code(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Noon payment returned an empty response');

        (new NoonService())->checkoutUrlFromResponse((object) ['message' => 'timeout']);
    }

    public function test_checkout_url_throws_on_noon_error_code(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Noon payment fatal error: 19001 - bad request');

        (new NoonService())->checkoutUrlFromResponse((object) [
            'resultCode' => 19001,
            'message' => 'bad request',
        ]);
    }
}
