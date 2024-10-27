<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TokenExpirationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->user()->currentAccessToken();

        if ($token) {
            $createdAt = $token->created_at;

            $expiresAt = $createdAt->addMinutes(intval(env('TOKEN_EXPIRED')));

            if (Carbon::now()->greaterThan($expiresAt)) {
                $token->delete();
                return response()->json(['message' => 'Token expired'], 401);
            }
        }

        return $next($request);
    }
}
