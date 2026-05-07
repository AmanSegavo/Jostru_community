<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware proteksi Developer API.
 * Wajib kirim header: Authorization: Bearer <DEV_API_SECRET>
 * Secret disimpan di .env sebagai DEV_API_SECRET.
 */
class DevApiAuth
{
    public function handle(Request $request, Closure $next)
    {
        $secret = env('DEV_API_SECRET');

        if (empty($secret)) {
            return response()->json([
                'error' => 'Developer API belum dikonfigurasi. Set DEV_API_SECRET di .env server.'
            ], 503);
        }

        // Coba berbagai cara ambil token (shared hosting sering strip Authorization header)
        $token = null;

        // 1. Standard Bearer Authorization header
        $authHeader = $request->header('Authorization', '');
        if (str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);
        }

        // 2. Custom header X-Dev-Token (fallback untuk shared hosting)
        if (empty($token)) {
            $token = $request->header('X-Dev-Token');
        }

        // 3. Query param ?token= (fallback terakhir, hanya untuk development)
        if (empty($token)) {
            $token = $request->query('token');
        }

        if (empty($token) || trim($token) !== $secret) {
            return response()->json([
                'error'   => 'Unauthorized.',
                'hint'    => 'Gunakan salah satu: Header "Authorization: Bearer <token>", Header "X-Dev-Token: <token>", atau query param ?token=<token>',
            ], 401);
        }

        return $next($request);
    }
}
