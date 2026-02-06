<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Exceptions that should not be reported (logged).
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        OAuthServerException::class,
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // default reporting
        });

        // Render OAuth server exceptions as clean JSON 401 responses
        $this->renderable(function (OAuthServerException $e, Request $request): JsonResponse {
            return response()->json([
                'message' => 'Unauthorized: invalid or revoked access token.'
            ], 401);
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson() || $request->is('api/*')
            ? response()->json(['message' => 'tidak bisa login.'], 401)
            : redirect()->guest(route('login'));
    }
}
