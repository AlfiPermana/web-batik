<?php

namespace App\Http\Controllers\Admin;

use App\Models\Order;
use App\Models\WorkshopBooking;
use App\Services\WorkshopPaymentSyncService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $workshopSyncService = app(WorkshopPaymentSyncService::class);

        $query = Order::with(['user', 'items']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Search by order number or customer email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('email', 'like', "%{$search}%");
                  });
            });
        }

        // Sort
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $orders = $query->paginate(15)->appends($request->query());

        $workshopQuery = WorkshopBooking::with([
            'user',
            'payments',
            'slotSchedule.timeSlot',
            'workshopAvailableDate.workshop',
        ]);

        if ($request->filled('status')) {
            $workshopQuery->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $workshopQuery->where('payment_status', $request->payment_status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $workshopQuery->where(function ($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('email', 'like', "%{$search}%");
                    });
            });
        }

        $workshopSortBy = $request->input('sort_by', 'created_at');
        $workshopSortOrder = $request->input('sort_order', 'desc');
        $workshopBookings = $workshopQuery
            ->orderBy($workshopSortBy, $workshopSortOrder)
            ->paginate(15, ['*'], 'workshop_page')
            ->appends($request->query());

        $workshopBookings->setCollection(
            $workshopBookings->getCollection()->map(function ($booking) use ($workshopSyncService) {
                return $workshopSyncService->syncBooking($booking);
            })
        );

        $customerWorkshopStats = collect();
        if ($workshopBookings->count() > 0) {
            $currentBookingIds = $workshopBookings->getCollection()->pluck('id')->all();
            $allRelevantBookings = WorkshopBooking::query()
                ->with(['workshopAvailableDate.workshop'])
                ->where(function ($query) use ($workshopBookings) {
                    foreach ($workshopBookings->getCollection() as $booking) {
                        $workshopAvailableDateId = $booking->workshop_available_date_id;

                        if ($booking->user_id) {
                            $query->orWhere(function ($subQuery) use ($booking, $workshopAvailableDateId) {
                                $subQuery->where('user_id', $booking->user_id);

                                if ($workshopAvailableDateId) {
                                    $subQuery->where('workshop_available_date_id', $workshopAvailableDateId);
                                }
                            });
                        } elseif ($booking->customer_email) {
                            $query->orWhere(function ($subQuery) use ($booking, $workshopAvailableDateId) {
                                $subQuery->where('customer_email', $booking->customer_email);

                                if ($workshopAvailableDateId) {
                                    $subQuery->where('workshop_available_date_id', $workshopAvailableDateId);
                                }
                            });
                        }
                    }
                })
                ->get();

            $statsByUserAndPackage = $allRelevantBookings
                ->filter(fn ($booking) => !empty($booking->user_id))
                ->groupBy(fn ($booking) => $booking->user_id . '|' . ($booking->workshop_available_date_id ?? 'none'))
                ->map(fn ($bookings) => [
                    'total_bookings' => (int) $bookings->count(),
                    'total_participants' => (int) $bookings->sum('num_participants'),
                ]);

            $statsByEmailAndPackage = $allRelevantBookings
                ->filter(fn ($booking) => empty($booking->user_id) && !empty($booking->customer_email))
                ->groupBy(fn ($booking) => $booking->customer_email . '|' . ($booking->workshop_available_date_id ?? 'none'))
                ->map(fn ($bookings) => [
                    'total_bookings' => (int) $bookings->count(),
                    'total_participants' => (int) $bookings->sum('num_participants'),
                ]);

            $customerWorkshopStats = $workshopBookings->getCollection()
                ->mapWithKeys(function ($booking) use ($statsByUserAndPackage, $statsByEmailAndPackage) {
                    $packageId = $booking->workshop_available_date_id ?? 'none';
                    $statsKey = $booking->user_id
                        ? $booking->user_id . '|' . $packageId
                        : $booking->customer_email . '|' . $packageId;

                    $stats = $booking->user_id
                        ? $statsByUserAndPackage->get($statsKey)
                        : $statsByEmailAndPackage->get($statsKey);

                    return [
                        $booking->id => [
                            'total_bookings' => (int) ($stats['total_bookings'] ?? 1),
                            'total_participants' => (int) ($stats['total_participants'] ?? (int) $booking->num_participants),
                        ],
                    ];
                });
        }

        // Status counts (global)
        $pendingOrdersCount = Order::where('status', 'pending')->count();
        $processingOrdersCount = Order::where('status', 'processing')->count();
        $shippedOrdersCount = Order::where('status', 'shipped')->count();
        $deliveredOrdersCount = Order::where('status', 'delivered')->count();
        $cancelledOrdersCount = Order::where('status', 'cancelled')->count();

        $pendingWorkshopOrdersCount = WorkshopBooking::where('status', 'pending')->count();
        $confirmedWorkshopOrdersCount = WorkshopBooking::where('status', 'confirmed')->count();
        $completedWorkshopOrdersCount = WorkshopBooking::where('status', 'completed')->count();
        $cancelledWorkshopOrdersCount = WorkshopBooking::where('status', 'cancelled')->count();
        $pendingWorkshopPaymentsCount = WorkshopBooking::where('payment_status', 'pending')->count();

        return view('admin.orders.index', [
            'orders' => $orders,
            'workshopBookings' => $workshopBookings,
            'statuses' => ['pending', 'processing', 'confirmed', 'shipped', 'delivered', 'completed', 'cancelled'],
            'paymentStatuses' => ['unpaid', 'pending', 'deposit_paid', 'paid', 'fully_paid', 'confirmed', 'expired', 'failed', 'refunded'],
            'pendingOrdersCount' => $pendingOrdersCount,
            'processingOrdersCount' => $processingOrdersCount,
            'shippedOrdersCount' => $shippedOrdersCount,
            'deliveredOrdersCount' => $deliveredOrdersCount,
            'cancelledOrdersCount' => $cancelledOrdersCount,
            'pendingWorkshopOrdersCount' => $pendingWorkshopOrdersCount,
            'confirmedWorkshopOrdersCount' => $confirmedWorkshopOrdersCount,
            'completedWorkshopOrdersCount' => $completedWorkshopOrdersCount,
            'cancelledWorkshopOrdersCount' => $cancelledWorkshopOrdersCount,
            'pendingWorkshopPaymentsCount' => $pendingWorkshopPaymentsCount,
            'customerWorkshopStats' => $customerWorkshopStats,
        ]);

    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product.images', 'items.productSize', 'statusHistories.changedBy']);

        return view('admin.orders.show', [
            'order' => $order,
            'statuses' => ['pending', 'processing', 'shipped', 'delivered', 'cancelled'],
        ]);
    }

    /**
     * Display order detail for customer
     */
    public function showCustomer(Order $order)
    {
        // Authorize: customer can only view their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this order');
        }

        $order->load(['user', 'items.product.images', 'items.productSize']);

        return view('orders.show', [
            'order' => $order,
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validated['status'] === 'cancelled' && !$order->canBeCancelled()) {
            return back()->with('error', 'Pesanan yang sudah lunas tidak bisa dibatalkan. Proses refund terlebih dahulu jika memang harus dibatalkan.');
        }

        $order->updateOrderStatus(
            $validated['status'],
            $validated['notes'] ?? null,
            Auth::id()
        );

        return back()->with('success', 'Status pesanan berhasil diubah');
    }

    /**
     * Generate invoice PDF
     */
    public function generateInvoice(Order $order)
    {
        // TODO: Implement PDF generation with dompdf or similar
        return back()->with('info', 'Feature invoice masih dalam pengembangan');
    }

    /**
     * Send notification to customer
     */
    public function sendNotification(Request $request, Order $order)
    {
        $validated = $request->validate([
            'type' => 'required|in:status_update,payment_reminder,shipment',
            'message' => 'nullable|string|max:500',
        ]);

        // TODO: Implement notification system
        // Send email/SMS to customer based on type

        return back()->with('success', 'Notifikasi berhasil dikirim');
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, Order $order)
    {
        if (!$order->canBeCancelled()) {
            return back()->with('error', 'Pesanan yang sudah lunas tidak bisa dibatalkan. Proses refund terlebih dahulu jika memang harus dibatalkan.');
        }

        if (in_array($order->status, ['pending', 'processing'], true)) {
            $order->updateOrderStatus('cancelled', $request->input('reason'), Auth::id());
            return back()->with('success', 'Pesanan berhasil dibatalkan');
        }

        return back()->with('error', 'Pesanan tidak dapat dibatalkan pada status ini');
    }
}
