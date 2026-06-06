<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User;
use App\Models\Order;

class CustomerController extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the customer dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this area.');
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'This area is for customers only.');
        }

        if ($user->role !== 'customer') {
            return redirect()->route('landing.home')
                ->with('error', 'You do not have permission to access this area.');
        }

        return view('customer.dashboard', [
            'user' => $user
        ]);
    }

    /**
     * Show the customer orders.
     */
    public function orders()
    {
        /** @var User $user */
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Please login to access this area.');
        }

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard')
                ->with('error', 'This area is for customers only.');
        }

        if ($user->role !== 'customer') {
            return redirect()->route('landing.home')
                ->with('error', 'You do not have permission to access this area.');
        }

        $orders = Order::where('user_id', $user->id)
            ->with('user')
            ->latest()
            ->paginate(10);
            
        return view('customer.orders', [
            'orders' => $orders,
            'user' => $user
        ]);
    }
}