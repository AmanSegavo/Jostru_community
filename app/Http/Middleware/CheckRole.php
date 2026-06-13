<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized action.');
        }

        $userRole = auth()->user()->role;

        // Superadmin bypass semua
        if ($userRole === 'superadmin') {
            return $next($request);
        }

        // Cek admin pendamping (punya hak kelola apapun)
        $user = auth()->user();
        $isAssistantAdmin = $user->can_manage_members || 
                            $user->can_manage_finances || 
                            $user->can_manage_waste || 
                            $user->can_manage_posts;

        // Gabungkan semua parameter role yang dikirim
        $allowedRoles = [];
        foreach ($roles as $role) {
            // Pecah kalau pakai koma atau pipe
            $allowedRoles = array_merge($allowedRoles, explode(',', $role));
            $allowedRoles = array_merge($allowedRoles, explode('|', $role));
        }

        // Bersihkan spasi
        $allowedRoles = array_map('trim', $allowedRoles);

        if (in_array('member', $allowedRoles) && !in_array($userRole, ['admin', 'superadmin'])) {
            return $next($request);
        }

        // Jika rute butuh admin, dan user punya hak kelola (assistant admin)
        if (in_array('admin', $allowedRoles) && $isAssistantAdmin) {
            return $next($request);
        }

        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}