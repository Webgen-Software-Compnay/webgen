<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required',
                'error_code' => 'AUTHENTICATION_REQUIRED'
            ], 401);
        }

        $user = Auth::user();

        // Check if user has admin role/permission
        // For now, we'll check if user email contains "admin" or has specific admin domains
        // In production, you would have a proper role/permission system
        
        $adminDomains = ['admin@', 'gov.lk', 'ministry.', 'department.'];
        $isAdmin = false;

        foreach ($adminDomains as $domain) {
            if (str_contains($user->email, $domain)) {
                $isAdmin = true;
                break;
            }
        }

        // Also check for specific admin user IDs (for development)
        $adminUserIds = [1]; // Add admin user IDs here
        if (in_array($user->id, $adminUserIds)) {
            $isAdmin = true;
        }

        if (!$isAdmin) {
            return response()->json([
                'success' => false,
                'message' => 'Admin access required',
                'error_code' => 'ADMIN_ACCESS_REQUIRED'
            ], 403);
        }

        return $next($request);
    }
}
