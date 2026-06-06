<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Workshop;
use App\Models\WorkshopBooking;
use App\Models\User;
use App\Models\Order;
use App\Services\WorkshopPaymentSyncService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalWorkshops = Workshop::count();
        $totalWorkshopBookings = WorkshopBooking::count();
        $pendingWorkshopPayments = WorkshopBooking::where('payment_status', 'pending')->count();
        $totalUsers = User::where('role', 'customer')->count();
        $totalOrders = Order::count();
        $totalRevenue = Order::sum('total') ?? 0;



        // Get recent orders
        $recentOrders = Order::with(['user'])
            ->latest()
            ->take(10)
            ->get();

        $recentWorkshopBookings = WorkshopBooking::with(['user', 'slotSchedule.timeSlot', 'workshopAvailableDate.workshop', 'payments'])
            ->latest()
            ->take(10)
            ->get();

        $syncService = app(WorkshopPaymentSyncService::class);
        $recentWorkshopBookings = $recentWorkshopBookings->map(
            fn ($booking) => $syncService->syncBooking($booking)
        );

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalWorkshops',
            'totalWorkshopBookings',
            'pendingWorkshopPayments',
            'totalUsers',
            'totalOrders',
            'totalRevenue',
            'recentOrders',
            'recentWorkshopBookings'
        ));
    }
}
