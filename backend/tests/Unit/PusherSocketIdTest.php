<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Support\PusherSocketId;
use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\CreatesApplication;

class PusherSocketIdTest extends BaseTestCase
{
    use CreatesApplication;

    public function test_normalizes_duplicated_socket_id_header(): void
    {
        $request = Request::create('/back/chat/send-message', 'POST');
        $request->headers->set('X-Socket-ID', '5982022853.2783707746, 5982022853.2783707746');

        PusherSocketId::normalizeRequest($request);

        $this->assertSame('5982022853.2783707746', $request->header('X-Socket-ID'));
    }

    public function test_removes_invalid_socket_id_header(): void
    {
        $request = Request::create('/back/chat/send-message', 'POST');
        $request->headers->set('X-Socket-ID', 'not-a-socket');

        PusherSocketId::normalizeRequest($request);

        $this->assertNull($request->header('X-Socket-ID'));
    }

    public function test_keeps_valid_socket_id_header(): void
    {
        $request = Request::create('/back/chat/send-message', 'POST');
        $request->headers->set('X-Socket-ID', '123.456');

        PusherSocketId::normalizeRequest($request);

        $this->assertSame('123.456', $request->header('X-Socket-ID'));
    }
}
