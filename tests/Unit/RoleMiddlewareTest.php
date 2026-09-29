<?php

namespace Tests\Unit;

use App\Http\Middleware\RoleMiddleware;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    public function test_guest_is_aborted_with_401(): void
    {
        try {
            $middleware = new RoleMiddleware();
            $middleware->handle(Request::create('/'), fn() => response('OK'), 'admin');
            $this->fail('Expected HttpException 401 was not thrown');
        } catch (HttpException $e) {
            $this->assertEquals(401, $e->getStatusCode());
        }
    }

    public function test_user_with_correct_role_can_pass(): void
    {
        $user = new User(['name' => 'Admin Test', 'email' => 'admin_test@test.com', 'role' => 'admin']);
        auth()->setUser($user);

        $middleware = new RoleMiddleware();
        $response = $middleware->handle(Request::create('/'), fn() => response('PASSED'), 'admin');

        $this->assertEquals('PASSED', $response->getContent());
    }

    public function test_user_with_wrong_role_is_forbidden_with_403(): void
    {
        $user = new User(['name' => 'Kasir Test', 'email' => 'kasir_test@test.com', 'role' => 'kasir']);
        auth()->setUser($user);

        try {
            $middleware = new RoleMiddleware();
            $middleware->handle(Request::create('/'), fn() => response('OK'), 'admin');
            $this->fail('Expected HttpException 403 was not thrown');
        } catch (HttpException $e) {
            $this->assertEquals(403, $e->getStatusCode());
        }
    }
}
