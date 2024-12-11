<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class AdminCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {

        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['error' => 'Authorization token is required'], Response::HTTP_UNAUTHORIZED);
        }
        $token = str_replace('Bearer ', '', $token);

        $user = User::where('access_token', $token)->first();

        if (!$user || $user->role_id !== 1) {
            return response()->json(['error' => 'Unauthorized access'], Response::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}
