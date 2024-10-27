<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ExceptionParser
{
    public static function renderer(): \Closure
    {
        return function (Throwable $exception) {
            if ($exception instanceof ModelNotFoundException) {
                return self::handleModelNotFoundException($exception);
            }

            if ($exception instanceof AuthenticationException) {
                return self::handleAuthenticationException($exception);
            }

            if ($exception instanceof AuthorizationException) {
                return self::handleAuthorizationException($exception);
            }

            if ($exception instanceof HttpException) {
                return self::handleHttpException($exception);
            }

            if ($exception instanceof RouteNotFoundException) {
                if (str_contains($exception->getMessage(), 'Route [login] not defined')) {
                    return self::handleInvalidJWT($exception);
                }
            }

            if ($exception instanceof NotFoundHttpException) {
                return self::handleNotFoundHttpException($exception);
            }

            return self::handleGeneralException($exception);
        };
    }

    protected static function handleModelNotFoundException(ModelNotFoundException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,

            'message' => 'Resource not found',
        ], 404);
    }

    protected static function handleAuthenticationException(AuthenticationException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,

            'message' => 'Unauthorized',
        ], 401);
    }

    protected static function handleAuthorizationException(AuthorizationException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,

            'message' => 'Forbidden',
        ], 403);
    }

    protected static function handleHttpException(HttpException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,

            'message' => $exception->getMessage(),
        ], $exception->getStatusCode());
    }

    protected static function handleGeneralException(Throwable $exception): JsonResponse
    {
        return response()->json([
            'success' => false,

            'message' => 'An error occurred.',
        ], 500);
    }

    protected static function handleInvalidJWT(RouteNotFoundException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,

            'message' => 'Invalid JWT token',
        ], 401);
    }

    protected static function handleNotFoundHttpException(NotFoundHttpException $exception): JsonResponse
    {
        return response()->json([
            'success' => false,

            'message' => 'Endpoint not found',
        ], 404);
    }

    protected static function handleValidationException($exception): JsonResponse
    {
        return response()->json([
            'success' => false,

            'message' => 'Validation failed',
            'errors' => $exception->validator->errors()
        ], 422);
    }

    protected static function handleConfigException(Throwable $exception): JsonResponse
    {
        return response()->json([
            'success' => false,

            'message' => 'Configuration error: ' . $exception->getMessage(),
        ], 500);
    }
}
