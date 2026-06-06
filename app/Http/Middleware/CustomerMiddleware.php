<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class CustomerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this page.');
        }

        /** @var User $user */
        $user = Auth::user();
        if (!$user->isCustomer()) {
            Log::warning('Unauthorized access attempt to customer area', [
                'user_id' => Auth::id(),
                'url' => $request->url(),
                'ip' => $request->ip()
            ]);
            
            if ($user->isAdmin()) {
                return redirect()->route('dashboard')
                    ->with('error', 'This area is for customers only.');
            }

            return redirect()->route('landing.home')
                ->with('error', 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}