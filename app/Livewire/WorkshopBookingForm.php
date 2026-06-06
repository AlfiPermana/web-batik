<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Workshop;
use App\Models\WorkshopSlotSchedule;
use App\Models\WorkshopBooking;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class WorkshopBookingForm extends Component
{
    public $workshopId;
    public $workshop;
    public $selectedDate;
    public $selectedScheduleId;
    public $availableDates = [];
    public $availableSlots = [];

    // Form fields
    public $customer_name = '';
    public $customer_email = '';
    public $customer_phone = '';
    public $num_participants = 1;
    public $special_requests = '';

    // UI State
    public $step = 1; // 1: Date selection, 2: Slot & Participants, 3: Review & Confirm
    public $bookingCreated = false;
    public $bookingNumber = '';

    public function mount($workshopId)
    {
        $this->workshopId = $workshopId;
        $this->workshop = Workshop::findOrFail($workshopId);
        $this->loadAvailableDates();
        
        // Pre-fill user data if authenticated
        if (Auth::check()) {
            $user = Auth::user();
            $this->customer_name = $user->name ?? '';
            $this->customer_email = $user->email ?? '';
            if ($user->customer_profile) {
                $this->customer_phone = $user->customer_profile->phone ?? '';
            }
        }
    }

    public function loadAvailableDates()
    {
        // Get all available dates with schedules (including fully_booked to show all options)
        $availableDates = $this->workshop->availableDates()
            ->orderBy('date')
            ->with(['schedules' => function($q) {
                $q->orderBy('date');
            }])
            ->get();

        $dates = [];
        foreach ($availableDates as $availableDate) {
            if ($availableDate->schedules->count() > 0) {
                // Count available and on_book slots
                $availableSlots = $availableDate->schedules->where('status', 'available')->count();
                $onBookSlots = $availableDate->schedules->where('status', 'on_book')->count();
                $totalSlots = $availableDate->schedules->count();
                
                $dates[] = [
                    'date' => $availableDate->date->format('Y-m-d'),
                    'display' => $availableDate->date->translatedFormat('l, d F Y'),
                    'slots' => $totalSlots,
                    'available_slots' => $availableSlots,
                    'on_book_slots' => $onBookSlots,
                ];
            }
        }
        $this->availableDates = $dates;
    }

    public function selectDate($date)
    {
        $this->selectedDate = $date;
        $this->loadAvailableSlots();
        $this->step = 2;
        $this->dispatch('scroll-to-slots');
    }

    public function loadAvailableSlots()
    {
        if (!$this->selectedDate) {
            $this->availableSlots = [];
            return;
        }

        // Load ALL slots (including fully_booked) to show status to users
        $schedules = WorkshopSlotSchedule::where('workshop_id', $this->workshopId)
            ->where('date', $this->selectedDate)
            ->with('timeSlot')
            ->orderBy('timeSlot.start_time')
            ->get();

        $slots = $schedules->map(function ($schedule) {
            $isFullyBooked = $schedule->status === 'fully_booked';
            $isOnBook = $schedule->status === 'on_book';
            
            return [
                'id' => $schedule->id,
                'slot_name' => $schedule->timeSlot->name,
                'start_time' => substr($schedule->timeSlot->start_time, 0, 5),
                'end_time' => substr($schedule->timeSlot->end_time, 0, 5),
                'booked_count' => $schedule->booked_count,
                'status' => $schedule->status,
                'is_fully_booked' => $isFullyBooked,
                'is_on_book' => $isOnBook,
                'is_available' => !$isFullyBooked,
            ];
        });

        $this->availableSlots = $slots->toArray();
    }

    public function selectSlot($scheduleId)
    {
        // Verify slot is still available
        $schedule = WorkshopSlotSchedule::findOrFail($scheduleId);
        
        if ($schedule->status === 'fully_booked') {
            $this->dispatch('notify-error', 'Slot ini sudah penuh. Silakan pilih slot lain.');
            return;
        }

        $this->selectedScheduleId = $scheduleId;
        $this->step = 3;
        $this->dispatch('scroll-to-form');
    }

    public function incrementParticipants(): void
    {
        $this->num_participants = max(1, (int) $this->num_participants + 1);
    }

    public function decrementParticipants(): void
    {
        $this->num_participants = max(1, (int) $this->num_participants - 1);
    }


    public function submitBooking()
    {
        $this->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'num_participants' => 'required|integer|min:1',
            'special_requests' => 'nullable|string|max:1000',
        ]);

        try {
            // Verify schedule is still available
            $schedule = WorkshopSlotSchedule::findOrFail($this->selectedScheduleId);
            
            if (!$schedule->canBook($this->num_participants)) {
                $this->dispatch('notify-error', 'Schedule is no longer available for this number of participants');
                return;
            }

            // Calculate price (harga PER PESERTA)
            $unitPrice = (float) $this->workshop->amount;
            $totalPrice = $unitPrice * (int) $this->num_participants;
            $depositAmount = (int) ceil($totalPrice / 2);


            // Create booking
            $bookingNumber = 'WS-' . now()->format('Ym') . '-' . Str::random(6);
            
            $booking = WorkshopBooking::create([
                'user_id' => Auth::id(),
                'workshop_slot_schedule_id' => $schedule->id,
                'booking_number' => $bookingNumber,
                'customer_name' => $this->customer_name,
                'customer_email' => $this->customer_email,
                'customer_phone' => $this->customer_phone,
                'num_participants' => $this->num_participants,
                'total_price' => $totalPrice,
                'deposit_amount' => $depositAmount,
                'remaining_amount' => $totalPrice - $depositAmount,
                'status' => 'pending',
                'payment_status' => 'pending',
                'special_requests' => $this->special_requests,
            ]);

            // Update schedule booked count (tanpa kapasitas maksimal)
            $schedule->booked_count += $this->num_participants;
            if ($schedule->booked_count > 0) {
                $schedule->status = 'on_book';
            }
            $schedule->save();

            $this->bookingNumber = $booking->booking_number;
            $this->bookingCreated = true;

            $this->dispatch('booking-success', [
                'booking_number' => $booking->booking_number,
                'booking_id' => $booking->id,
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify-error', 'Error creating booking: ' . $e->getMessage());
        }
    }

    public function goBack()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function resetForm()
    {
        $this->selectedDate = null;
        $this->selectedScheduleId = null;
        $this->num_participants = 1;
        $this->special_requests = '';
        $this->step = 1;
        $this->bookingCreated = false;
    }

    public function render()
    {
        return view('livewire.workshop-booking-form');
    }
}
