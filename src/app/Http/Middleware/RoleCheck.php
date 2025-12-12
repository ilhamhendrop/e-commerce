<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        $user = $request->user();

        try {
            $expectedRole = RoleEnum::from($role);
        } catch (\ValueError $e) {
            return response()->json([
                'message' => 'Role tidak dikenal.'
            ], 403);
        }

        if (!$user || !$user->hasRole($expectedRole)) {
            return response()->json([
                'message' => 'Akses tidak diizinkan'
            ], 403);
        }

        return $next($request);
    }
}
