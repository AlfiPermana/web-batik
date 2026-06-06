<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to access this page.');
        }

        /** @var User $user */
        $user = Auth::user();
        if (!$user->isAdmin()) {
            Log::warning('Unauthorized access attempt to admin area', [
                'user_id' => Auth::id(),
                'url' => $request->url(),
                'ip' => $request->ip()
            ]);
            
            return redirect()->route('customer.dashboard')
                ->with('error', 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}