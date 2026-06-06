<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Models\WorkshopDate;
use App\Models\WorkshopBooking;
use App\Services\WorkshopPaymentSyncService;
use Illuminate\Http\Request;

class WorkshopController extends Controller
{
    /**
     * Display workshop list
     */
    public function index(Request $request)
    {
        $query = Workshop::query();

        // Filter by status
        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->get('search') . '%');
        }

        $workshops = $query
            ->with('dates', 'bookings')
            ->latest()
            ->paginate(15);

        return view('admin.workshops.index', compact('workshops'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.workshops.create');
    }

    /**
     * Show workshop details
     */
    public function show(Workshop $workshop)
    {
        $workshop->load([
            'schedules' => function ($q) {
                $q->orderBy('date')->with(['timeSlot', 'bookings']);
            },
            'bookings' => function ($q) {
                $q->latest()->with(['slotSchedule.timeSlot', 'workshopAvailableDate', 'payments', 'user']);
            },
        ]);

        return view('admin.workshops.show', compact('workshop'));
    }

    /**
     * Store new workshop WITH slots and dates in ONE submission
     */
    public function store(Request $request)
    {
        // Validate main workshop fields
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0|max:999999.99',
            'location' => 'required|string|max:255',
            'slots' => 'required|array|min:1',
            'slots.*.name' => 'required|string|max:100',
            'slots.*.start_time' => 'required|date_format:H:i',
            'slots.*.end_time' => 'required|date_format:H:i|after:slots.*.start_time',
            'dates' => 'required|array|min:1',
            'dates.*' => 'required|date|after_or_equal:today',
        ]);

        try {
            // NOTE: sistem tidak memakai kapasitas maksimal. Kolom tetap ada untuk kompatibilitas DB.
            $defaultMaxCapacity = 1000000;

            // Create workshop
            $workshop = Workshop::create([
                'title' => $request->title,
                'description' => $request->description,
                'amount' => $request->amount,
                'location' => $request->location,
                'capacity' => 0,
                'created_by' => auth('web')->user()?->id,
                'is_active' => true,
            ]);

            // Create time slots
            $timeSlotIds = [];
            foreach ($request->slots as $slotData) {
                $timeSlot = $workshop->timeSlots()->create([
                    'name' => $slotData['name'],
                    'start_time' => $slotData['start_time'],
                    'end_time' => $slotData['end_time'],
                    'max_capacity' => $defaultMaxCapacity,
                    'is_active' => true,
                ]);
                $timeSlotIds[] = $timeSlot->id;
            }

            // Create available dates and auto-generate schedules
            foreach ($request->dates as $date) {
                $availableDate = \App\Models\WorkshopAvailableDate::create([
                    'workshop_id' => $workshop->id,
                    'date' => $date,
                ]);

                // For each date, create a schedule for each time slot
                foreach ($timeSlotIds as $timeSlotId) {
                    \App\Models\WorkshopSlotSchedule::create([
                        'workshop_id' => $workshop->id,
                        'time_slot_id' => $timeSlotId,
                        'available_date_id' => $availableDate->id,
                        'date' => $date,
                        'max_capacity' => $defaultMaxCapacity,
                        'booked_count' => 0,
                        'status' => 'available',
                        'is_cancelled' => false,
                    ]);
                }
            }

            return redirect()->route('admin.workshop.index')
                ->with('success', 'Workshop berhasil dibuat dengan ' . count($timeSlotIds) . ' slot dan ' . count($request->dates) . ' tanggal!');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Gagal membuat workshop: ' . $e->getMessage());
        }
    }

    /**
     * Show edit form
     */
    public function edit(Workshop $workshop)
    {
        $workshop->load('dates');
        return view('admin.workshops.edit', compact('workshop'));
    }

    /**
     * Update workshop
     */
    public function update(Request $request, Workshop $workshop)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0|max:999999.99',
            'location' => 'required|string|max:255',
            'is_active' => 'sometimes|boolean',
        ]);

        $workshop->update($validated);

        return redirect()->route('admin.workshop.edit', $workshop->id)
            ->with('success', 'Workshop updated successfully.');
    }

    /**
     * Delete workshop
     */
    public function destroy(Workshop $workshop)
    {
        // Soft delete
        $workshop->delete();

        return redirect()->route('admin.workshop.index')
            ->with('success', 'Workshop deleted successfully.');
    }

    /**
     * Add workshop date
     */
    public function addDate(Request $request, Workshop $workshop)
    {
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'capacity' => 'nullable|integer|min:1|max:1000',
        ]);

        $validated['capacity'] = $validated['capacity'] ?? $workshop->capacity;

        $workshopDate = $workshop->dates()->create($validated);

        // Check if it's an AJAX request
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Workshop date added successfully.',
                'data' => $workshopDate,
            ]);
        }

        return redirect()->route('admin.workshop.edit', $workshop)
            ->with('success', 'Jadwal workshop berhasil ditambahkan.');
    }

    /**
     * Update workshop date
     */
    public function updateDate(Request $request, WorkshopDate $workshopDate)
    {
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'capacity' => 'nullable|integer|min:1|max:1000',
            'is_active' => 'sometimes|boolean',
            'is_cancelled' => 'sometimes|boolean',
            'cancellation_reason' => 'required_if:is_cancelled,true|nullable|string',
        ]);

        $workshopDate->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Workshop date updated successfully.',
            'data' => $workshopDate,
        ]);
    }

    /**
     * Delete workshop date
     */
    public function deleteDate(WorkshopDate $workshopDate)
    {
        $workshop = $workshopDate->workshop;
        $workshopDate->delete();

        // Check if it's an AJAX request
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Workshop date deleted successfully.',
            ]);
        }

        return redirect()->route('admin.workshop.edit', $workshop)
            ->with('success', 'Jadwal workshop berhasil dihapus.');
    }

    /**
     * View bookings for a workshop
     */
    public function bookings(Request $request, Workshop $workshop)
    {
        $query = $workshop->bookings()
            ->with(['slotSchedule.timeSlot', 'workshopAvailableDate', 'payments', 'user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->get('payment_status'));
        }

        // Search by booking number or customer email
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'like', "%$search%")
                    ->orWhere('customer_email', 'like', "%$search%")
                    ->orWhere('customer_name', 'like', "%$search%");
            });
        }

        $bookings = $query->latest()->paginate(20);
        $syncService = app(WorkshopPaymentSyncService::class);
        $bookings->setCollection(
            $bookings->getCollection()->map(fn ($booking) => $syncService->syncBooking($booking))
        );

        return view('admin.workshops.bookings.index', compact('workshop', 'bookings'));
    }

    /**
     * View single booking
     */
    public function viewBooking(Workshop $workshop, WorkshopBooking $booking)
    {
        $booking->load(['slotSchedule.timeSlot', 'workshopAvailableDate.workshop', 'user', 'payments', 'reminders']);

        // Verify booking belongs to workshop (NEW SYSTEM)
        $belongs = false;
        if ($booking->slotSchedule && (int) $booking->slotSchedule->workshop_id === (int) $workshop->id) {
            $belongs = true;
        }
        if ($booking->workshopAvailableDate && (int) $booking->workshopAvailableDate->workshop_id === (int) $workshop->id) {
            $belongs = true;
        }
        if (!$belongs) {
            abort(404);
        }

        $booking = app(WorkshopPaymentSyncService::class)->syncBooking($booking);

        return view('admin.workshops.bookings.show', compact('workshop', 'booking'));
    }

    /**
     * Dashboard stats
     */
    public function dashboardStats()
    {
        $stats = [
            'total_workshops' => Workshop::where('is_active', true)->count(),
            'upcoming_workshops' => WorkshopDate::where('date', '>=', now()->toDateString())
                ->where('is_cancelled', false)
                ->count(),
            'total_bookings' => WorkshopBooking::where('status', 'confirmed')->count(),
            'pending_payments' => WorkshopBooking::where('payment_status', 'pending')
                ->where('created_at', '>', now()->subDays(3))
                ->count(),
            'total_revenue' => WorkshopBooking::where('payment_status', 'deposit_paid')
                ->orWhere('payment_status', 'fully_paid')
                ->sum('deposit_amount'),
        ];

        return $stats;
    }

    /**
     * Remove available date (NEW SYSTEM)
     */
    public function removeAvailableDate($availableDateId)
    {
        $availableDate = \App\Models\WorkshopAvailableDate::findOrFail($availableDateId);
        
        // Check if date has any bookings
        $bookingsCount = \DB::table('workshop_bookings')
            ->join('workshop_slot_schedules', 'workshop_slot_schedules.id', '=', 'workshop_bookings.workshop_slot_schedule_id')
            ->where('workshop_slot_schedules.available_date_id', $availableDate->id)
            ->where('workshop_bookings.status', '!=', 'cancelled')
            ->count();
        
        if ($bookingsCount > 0) {
            return back()->with('error', 'Cannot delete date with active bookings (' . $bookingsCount . ' booking(s)).');
        }
        
        // Delete all schedules for this date
        \App\Models\WorkshopSlotSchedule::where('available_date_id', $availableDate->id)->delete();
        
        // Delete the available date
        $availableDate->delete();
        
        return back()->with('success', 'Available date removed successfully.');
    }

    /**
     * Add available date (NEW SYSTEM)
     */
    public function addAvailableDate(Request $request, Workshop $workshop)
    {
        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
        ]);

        try {
            // Check if date already exists
            $exists = \App\Models\WorkshopAvailableDate::where('workshop_id', $workshop->id)
                ->where('date', $validated['date'])
                ->exists();

            if ($exists) {
                return redirect()->route('admin.workshop.edit', $workshop)->with('error', 'Tanggal ini sudah ditambahkan sebelumnya.');
            }

            // Create available date
            $availableDate = \App\Models\WorkshopAvailableDate::create([
                'workshop_id' => $workshop->id,
                'date' => $validated['date'],
            ]);

            // Auto-create slot schedules for all time slots on this date
            $timeSlots = $workshop->timeSlots()->where('is_active', true)->get();
            
            foreach ($timeSlots as $slot) {
                \App\Models\WorkshopSlotSchedule::create([
                    'workshop_id' => $workshop->id,
                    'time_slot_id' => $slot->id,
                    'available_date_id' => $availableDate->id,
                    'date' => $validated['date'],
                    'max_capacity' => $slot->max_capacity,
                    'booked_count' => 0,
                    'status' => 'available',
                    'is_cancelled' => false,
                ]);
            }

            return redirect()->route('admin.workshop.edit', $workshop)->with('success', 'Tanggal berhasil ditambahkan. ' . $timeSlots->count() . ' jadwal otomatis dibuat.');
        } catch (\Exception $e) {
            return redirect()->route('admin.workshop.edit', $workshop)->with('error', 'Error: ' . $e->getMessage());
        }
    }
}

