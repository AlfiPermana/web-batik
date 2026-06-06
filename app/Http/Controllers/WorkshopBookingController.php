<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use App\Models\WorkshopDate;
use App\Models\WorkshopBooking;
use App\Models\WorkshopPayment;
use App\Models\WorkshopSlotSchedule;
use App\Services\WorkshopPaymentSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WorkshopBookingController extends Controller
{
    /**
     * Show booking form (display all packages)
     */
    public function index()
    {
        $workshops = Workshop::where('is_active', true)->get();
        return view('workshop.booking-form', compact('workshops'));
    }

    /**
     * Show payment page
     */
    public function showPayment(WorkshopBooking $booking)
    {
        $user = auth('web')->user();
        if (!$user || ($user->id !== $booking->user_id && $user->role !== 'admin')) {
            abort(403);
        }

        $booking->load(['workshopAvailableDate.workshop', 'slotSchedule.timeSlot', 'payments']);
        $booking = app(WorkshopPaymentSyncService::class)->syncBooking($booking);

        return view('workshop.booking.payment', compact('booking'));
    }

    public function getDates(Workshop $workshop)
    {
        $dates = $workshop->upcomingDates()
            ->get()
            ->map(function ($date) {
                return [
                    'id' => $date->id,
                    'formatted' => $date->getFormattedDateTime(),
                    'date' => $date->date->format('Y-m-d'),
                    'start_time' => $date->start_time->format('H:i'),
                    'end_time' => $date->end_time->format('H:i'),
                    'capacity' => $date->capacity,
                    'available' => $date->getAvailableCapacity(),
                    'is_full' => $date->isFull(),
                ];
            });

        return response()->json($dates);
    }

    /**
     * Get available schedules for a workshop (for dropdown selection)
     * UPDATED: Only show slots that are NOT PAID (status != 'PAID')
     * No capacity limit - admin only manages time slots and pricing
     */
    public function getAvailableSchedules(Workshop $workshop)
    {
        $schedules = $workshop->schedules()
            ->with('timeSlot')
            ->where('status', 'available')
            ->where('is_cancelled', false)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date')
            ->orderBy('time_slot_id')
            ->get()
            ->map(function ($schedule) {
                // Cek apakah timeSlot ada
                if (!$schedule->timeSlot) {
                    return null; // Skip jika tidak ada timeSlot
                }
                
                // Format date properly (remove timestamp)
                $dateStr = is_string($schedule->date) 
                    ? $schedule->date 
                    : $schedule->date->format('Y-m-d');
                
                // Format times properly (remove seconds)
                $startTime = is_string($schedule->timeSlot->start_time)
                    ? substr($schedule->timeSlot->start_time, 0, 5)
                    : $schedule->timeSlot->start_time->format('H:i');
                
                $endTime = is_string($schedule->timeSlot->end_time)
                    ? substr($schedule->timeSlot->end_time, 0, 5)
                    : $schedule->timeSlot->end_time->format('H:i');
                
                return [
                    'id' => $schedule->id,
                    'date' => $dateStr,
                    'date_formatted' => $dateStr . ' (' . $this->getDayName($dateStr) . ')',
                    'time_slot_name' => $schedule->timeSlot->name,
                    'slot_name' => $schedule->timeSlot->name,  // Keep for backward compatibility
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'booked_count' => $schedule->booked_count,
                    'status' => $schedule->status,
                    'is_available' => true,  // ✅ All non-PAID slots are available
                    'label' => sprintf(
                        '%s - %s %s-%s',
                        $dateStr,
                        $schedule->timeSlot->name,
                        $startTime,
                        $endTime
                    )
                ];
            })
            ->filter(null) // Hapus null entries
            ->values(); // Re-index array

        return response()->json($schedules);
    }

    private function getDayName($date)
    {
        $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return $days[date('w', strtotime($date))];
    }

    /**
     * Store booking (Step 1: Form submission)
     */
    public function store(Request $request)
    {
        \Log::info('✅ WorkshopBookingController::store() called!');
        try {
            \Log::info('Request data:', $request->all());
            
            $validated = $request->validate([
                'workshop_id' => 'required|integer',
                'workshop_slot_schedule_id' => 'required|integer', 
                'workshop_name' => 'required|string',
                'package_price' => 'required|numeric|min:0',
                'number_of_participants' => 'required|integer|min:1',
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
            ]);

            \Log::info('✅ Validation passed!', $validated);

            // Validate schedule exists and is available
            $schedule = WorkshopSlotSchedule::with('availableDate')->findOrFail($validated['workshop_slot_schedule_id']);
            \Log::info('✅ Schedule found:', ['schedule_id' => $schedule->id, 'status' => $schedule->status]);
        
            // NEW SYSTEM: hanya boleh booking kalau schedule benar-benar "available"
            if ($schedule->is_cancelled || $schedule->status !== 'available') {
                if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                    return response()->json(['success' => false, 'message' => 'Jadwal ini sudah tidak tersedia.'], 400);
                }
                return redirect()->back()->withErrors(['schedule' => 'Jadwal ini sudah tidak tersedia. Silahkan pilih jadwal lain.']);
            }

        // Calculate price (harga PER PESERTA)
        $workshop = Workshop::findOrFail((int) $validated['workshop_id']);
        $numParticipants = (int) $validated['number_of_participants'];
        $unitPrice = (float) $workshop->amount;

        $totalPrice = $unitPrice * $numParticipants;
        $depositAmount = (int) ceil($totalPrice / 2); // 50% deposit
        $remainingAmount = $totalPrice - $depositAmount;


        // Generate booking number (keep existing logic)
        $bookingNumber = 'WS-' . date('Ym') . '-' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Create or find user
        $user = auth('web')->user();
        
        // Require auth untuk lanjut ke payment
        if (!$user) {
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silahkan login terlebih dahulu untuk melanjutkan booking workshop.',
                    'redirect' => route('login')
                ], 401);
            }
            return redirect()->route('login')->with('warning', 'Silahkan login terlebih dahulu untuk melanjutkan booking workshop.');
        }
        
        // Create booking dengan schedule info
        $booking = DB::transaction(function () use ($validated, $user, $bookingNumber, $totalPrice, $depositAmount, $remainingAmount) {
            $lockedSchedule = WorkshopSlotSchedule::query()
                ->lockForUpdate()
                ->findOrFail($validated['workshop_slot_schedule_id']);

            if ($lockedSchedule->is_cancelled || $lockedSchedule->status !== 'available') {
                throw new \RuntimeException('Jadwal ini sudah tidak tersedia. Silahkan pilih jadwal lain.');
            }

            $booking = WorkshopBooking::create([
            'user_id' => $user->id,
            'workshop_available_date_id' => $lockedSchedule->available_date_id,
            'workshop_slot_schedule_id' => $validated['workshop_slot_schedule_id'],
            'booking_number' => $bookingNumber,
            'customer_name' => $validated['full_name'],
            'customer_email' => $validated['email'],
            'customer_phone' => $validated['phone'],
            'num_participants' => $validated['number_of_participants'],
            'total_price' => $totalPrice,
            'deposit_amount' => $depositAmount,
            'remaining_amount' => $remainingAmount,
            'status' => 'pending',
            'payment_status' => 'pending',
        ]);
        
        \Log::info('✅ Booking created!', ['booking_id' => $booking->id]);

        // Update schedule booked_count
        \Log::info('Before bookParticipants', ['num_to_book' => $validated['number_of_participants']]);
        $bookResult = $lockedSchedule->bookParticipants((int) $validated['number_of_participants']);
        \Log::info('After bookParticipants', ['result' => $bookResult]);

        if (!$bookResult) {
            throw new \RuntimeException('Gagal mengunci jadwal workshop. Silahkan coba lagi.');
        }

        return $booking;
        });

        // Log before returning response
        \Log::info('About to check if AJAX request', [
            'has_XMLHttpRequest_header' => $request->header('X-Requested-With'),
            'expectsJson' => $request->expectsJson(),
            'wantsJson' => $request->wantsJson(),
        ]);

        // If AJAX request (FormData POST with X-Requested-With header), return JSON
        if ($request->expectsJson() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            \Log::info('Returning JSON response for booking');
            return response()->json([
                'success' => true,
                'message' => 'Booking berhasil dibuat.',
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'total_price' => $booking->total_price,
                'redirect' => route('workshop.booking.payment', $booking->id)
            ]);
        }

        \Log::info('Returning redirect response for booking');

        return redirect()->route('workshop.booking.payment', $booking->id)
            ->with('success', 'Booking berhasil dibuat. Silahkan lanjut ke pembayaran.');
        
        } catch (\Exception $e) {
            \Log::error('Workshop Booking Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            if ($request->expectsJson() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage(),
                    'error' => $e->getMessage()
                ], 400);
            }
            
            return redirect()->back()->withErrors(['error' => 'Error: ' . $e->getMessage()]);
        }
    }

    // ✅ ✅ ✅ BARU: Proses pembayaran via Tripay (tidak menggantikan storePayment!)
    public function processPayment(Request $request, WorkshopBooking $booking)
    {
        $user = auth('web')->user();
        if (!$user || $user->id !== $booking->user_id) {
            abort(403, 'Unauthorized');
        }

        $booking = app(WorkshopPaymentSyncService::class)->syncBooking($booking);

        // Validate request parameters dengan pesan error yang jelas
        try {
            $validated = $request->validate([
                'payment_method' => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) {
                        $allowedMethods = array_keys(config('payment.methods', []));
                        if (!in_array($value, $allowedMethods)) {
                            $fail("Invalid payment method: {$value}. Allowed: " . implode(', ', $allowedMethods));
                        }
                    }
                ],
                'type' => 'required|string|in:deposit,remaining,full',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . implode(', ', $e->errors() ? array_map(fn($errs) => implode(', ', $errs), $e->errors()) : ['Unknown error']),
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        // 🔍 Hitung amount berdasarkan type
        $amount = match ($validated['type']) {
            'deposit' => $booking->deposit_amount,
            'remaining' => $booking->remaining_amount,
            'full' => $booking->total_price,
            default => throw new \InvalidArgumentException('Invalid payment type')
        };

        if ($booking->payment_status === 'fully_paid') {
            $message = 'Booking ini sudah lunas.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->withErrors(['payment' => $message]);
        }

        if ($validated['type'] === 'deposit' && in_array($booking->payment_status, ['deposit_paid', 'fully_paid'], true)) {
            $message = 'DP untuk booking ini sudah dibayar.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->withErrors(['payment' => $message]);
        }

        if ($validated['type'] === 'remaining' && $booking->payment_status !== 'deposit_paid') {
            $message = 'Pelunasan hanya bisa dilakukan setelah DP terkonfirmasi.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->withErrors(['payment' => $message]);
        }

        $activePendingPayment = $booking->payments()
            ->where('type', $validated['type'])
            ->where('payment_status', 'pending')
            ->latest()
            ->get()
            ->first(function ($payment) {
                if (!$payment->notes) {
                    return true;
                }

                $notes = json_decode($payment->notes, true);
                if (!is_array($notes)) {
                    return true;
                }

                $expiredTime = $notes['tripay']['expired_time'] ?? null;
                $expiredAt = $notes['tripay']['expired_at'] ?? null;

                if (is_numeric($expiredTime)) {
                    return \Carbon\Carbon::createFromTimestamp((int) $expiredTime)->isFuture();
                }

                if (is_string($expiredAt) && $expiredAt !== '') {
                    try {
                        return \Carbon\Carbon::parse($expiredAt)->isFuture();
                    } catch (\Throwable $e) {
                        return true;
                    }
                }

                return true;
            });

        if ($activePendingPayment) {
            $message = 'Masih ada pembayaran workshop yang aktif. Selesaikan atau tunggu hingga kadaluarsa terlebih dahulu.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->withErrors(['payment' => $message]);
        }

        if ($amount <= 0) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Jumlah pembayaran tidak valid.'], 422);
            }
            return back()->withErrors(['payment' => 'Jumlah pembayaran tidak valid.']);
        }

        // 🟢 Langkah 1: Buat WorkshopPayment dulu (status = pending)
        $payment = $booking->payments()->create([
            'amount' => $amount,
            'type' => $validated['type'],
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'pending',
            'reference_number' => 'TRIPAY_' . Str::random(12),
        ]);

        try {
            // 🟢 Langkah 2: Kirim ke Tripay
            $paymentService = new \App\Services\payment\PaymentService();
            $response = $paymentService->createPayment([
                'merchant_ref' => $payment->reference_number,
                'amount' => $amount,
                'method' => $validated['payment_method'],
                'customer_name' => $booking->customer_name ?? $booking->user->name ?? 'Customer',
                'customer_email' => $booking->customer_email ?? $booking->user->email ?? '',
                'customer_phone' => $booking->customer_phone ?? $booking->user->phone ?? '',
                'booking_id' => $booking->id, // Pass booking_id untuk return_url
                'items' => [
                    [
                        'name' => $booking->workshopAvailableDate?->workshop?->title ?? $booking->workshop_name ?? 'Workshop Booking',
                        'quantity' => 1,
                        'price' => $amount,
                    ]
                ]
            ]);

            // ❌ Gagal?
            if (!($response['success'] ?? false) || empty($response['data']['checkout_url'])) {
                Log::error('Tripay create failed', $response);
                $errorMsg = $response['message'] ?? 'Gagal membuat pembayaran.';

                // Simpan attempt sebagai failed (biar ada audit trail)
                $payment->update([
                    'payment_status' => 'failed',
                    'notes' => json_encode([
                        'error' => $errorMsg,
                        'tripay_response' => $response,
                    ]),
                ]);

                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $errorMsg], 422);
                }
                return back()->withErrors(['payment' => $errorMsg]);
            }

            // ✅ Sukses → update payment & return checkout URL
            $payment->update([
                'reference_number' => $response['data']['reference'] ?? $payment->reference_number,
                'payment_status' => 'pending',
            ]);
            
            $tripayData = $response['data'] ?? [];

            // Simpan data Tripay penting (termasuk batas waktu pembayaran) ke payment.notes
            $existingNotes = [];
            if (is_string($payment->notes) && $payment->notes) {
                $decoded = json_decode($payment->notes, true);
                if (is_array($decoded)) {
                    $existingNotes = $decoded;
                }
            }
            $existingNotes['tripay'] = [
                'reference' => $tripayData['reference'] ?? null,
                'method' => $validated['payment_method'],
                'expired_time' => $tripayData['expired_time'] ?? null,
                'expired_at' => $tripayData['expired_at'] ?? null,
            ];
            $payment->update([
                'notes' => json_encode($existingNotes),
            ]);

            // Extract payment details berdasarkan method untuk ditampilkan di modal
            $paymentDetails = $this->extractPaymentDetails($validated['payment_method'], $tripayData);

            // Admin fee (Tripay):
            // - Prefer fee charged to customer (`fee_customer`) if > 0
            // - Otherwise fallback to `total_fee`
            // - Otherwise fallback to `fee_merchant`
            $feeCustomer = $tripayData['fee_customer'] ?? null;
            $totalFee = $tripayData['total_fee'] ?? null;
            $feeMerchant = $tripayData['fee_merchant'] ?? null;

            $feeCustomerInt = is_numeric($feeCustomer) ? (int) $feeCustomer : null;
            $totalFeeInt = is_numeric($totalFee) ? (int) $totalFee : null;
            $feeMerchantInt = is_numeric($feeMerchant) ? (int) $feeMerchant : null;

            if ($feeCustomerInt !== null && $feeCustomerInt > 0) {
                $tripayFee = $feeCustomerInt;
            } elseif ($totalFeeInt !== null && $totalFeeInt > 0) {
                $tripayFee = $totalFeeInt;
            } elseif ($feeMerchantInt !== null && $feeMerchantInt > 0) {
                $tripayFee = $feeMerchantInt;
            } else {
                $tripayFee = 0;
            }

            $paymentDetails['fee'] = $tripayFee;

            // Log untuk debugging
            Log::info('Payment processed - returning to frontend', [
                'payment_id' => $payment->id,
                'method' => $validated['payment_method'],
                'payment_details' => $paymentDetails,
                'tripay_fee' => $tripayFee,
                'tripay_response' => $tripayData
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'checkout_url' => $tripayData['checkout_url'],
                    'reference' => $tripayData['reference'] ?? $payment->reference_number,
                    'payment_details' => $paymentDetails,
                    'method' => $validated['payment_method'],
                    'amount' => $amount,
                    'fee' => $tripayFee,
                    'expired_time' => $tripayData['expired_time'] ?? null,
                    'expired_at' => $tripayData['expired_at'] ?? null,
                ]);
            }
            return redirect($tripayData['checkout_url']);

        } catch (\Exception $e) {
            // simpan attempt sebagai failed (jangan delete) supaya bisa dilacak
            try {
                $payment->update([
                    'payment_status' => 'failed',
                    'notes' => json_encode([
                        'error' => $e->getMessage(),
                    ]),
                ]);
            } catch (\Throwable $_) {
                // ignore
            }

            Log::error('Tripay exception', [
                'error' => $e->getMessage(),
                'booking_id' => $booking->id,
                'user_id' => $user->id,
            ]);
            $errorMsg = 'Gagal terhubung ke gateway pembayaran. Silakan coba lagi.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 422);
            }
            return back()->withErrors(['payment' => $errorMsg]);
        }
    }

    /**
     * Get payment status (untuk polling dari frontend)
     */
    public function getPaymentStatus(WorkshopBooking $booking)
    {
        $user = auth('web')->user();
        if (!$user || $user->id !== $booking->user_id) {
            abort(403, 'Unauthorized');
        }

        $booking = app(WorkshopPaymentSyncService::class)->syncBooking($booking);

        // Get latest payment
        $payment = $booking->payments()->latest()->first();
        
        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'No payment found',
            ], 404);
        }

        // Auto-mark expired jika sudah melewati batas waktu Tripay
        $expiresAt = null;
        if ($payment->notes) {
            $notes = json_decode($payment->notes, true);
            if (is_array($notes)) {
                $t = $notes['tripay']['expired_time'] ?? null;
                $s = $notes['tripay']['expired_at'] ?? null;

                if (is_numeric($t)) {
                    $expiresAt = \Carbon\Carbon::createFromTimestamp((int) $t);
                } elseif (is_string($s) && $s) {
                    try { $expiresAt = \Carbon\Carbon::parse($s); } catch (\Throwable $_) { $expiresAt = null; }
                }
            }
        }

        if ($payment->payment_status === 'pending' && $expiresAt && $expiresAt->isPast()) {
            $payment->update(['payment_status' => 'expired']);
        }

        // Jika payment sudah confirmed, pastikan booking table ikut tersinkron
        // (UI "Workshop Saya" membaca booking.payment_status, bukan payment.payment_status)
        if ($payment->payment_status === 'confirmed') {
            $nextBookingPaymentStatus = match ($payment->type) {
                'deposit' => 'deposit_paid',
                'remaining', 'full' => 'fully_paid',
                default => $booking->payment_status ?? 'pending',
            };

            if (($booking->payment_status ?? null) !== 'fully_paid') {
                $booking->update([
                    'status' => 'confirmed',
                    'payment_status' => $nextBookingPaymentStatus,
                    'payment_date' => now(),
                ]);
            }
        }

        // Sinkron status booking untuk status gagal/kadaluarsa/refund (biar tampil di UI)
        if (in_array($payment->payment_status, ['failed', 'expired', 'refunded'], true)) {
            $booking->update([
                'payment_status' => $payment->payment_status,
            ]);
        }

        return response()->json([
            'success' => true,
            'payment_status' => $payment->payment_status,
            'booking_status' => $booking->status,
            'payment_id' => $payment->id,
            'reference_number' => $payment->reference_number,
            'expired_time' => $expiresAt?->timestamp,
            'expired_at' => $expiresAt?->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Store booking (Manual: bank transfer, upload bukti)
    {
        // ... (kode lama tetap utuh)
        // ✅ Tidak diubah sama sekali
        // Simpan untuk fallback jika Tripay tidak digunakan
        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This booking cannot be paid.',
            ], 422);
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:bank_transfer,ewallet,credit_card',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store('workshop-payments', 'public');
        }

        $payment = $booking->payments()->create([
            'amount' => $booking->deposit_amount,
            'type' => 'deposit',
            'payment_method' => $validated['payment_method'],
            'payment_status' => $request->has('proof_file') ? 'pending' : 'pending',
            'proof_file_path' => $proofPath,
        ]);

        if ($validated['payment_method'] === 'credit_card') {
            $booking->update([
                'status' => 'confirmed',
                'payment_status' => 'deposit_paid',
                'payment_date' => now(),
            ]);
            $payment->update(['payment_status' => 'confirmed']);
            $this->createBookingReminders($booking);

            return response()->json([
                'success' => true,
                'message' => 'Payment successful! Your booking is confirmed.',
                'redirect' => route('workshop.booking.confirmation', $booking->id),
            ]);
        } else {
            $booking->update([
                'status' => 'confirmed',
                'payment_status' => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment submitted for verification. We\'ll confirm within 1-2 hours.',
                'redirect' => route('workshop.booking.confirmation', $booking->id),
            ]);
        }
    }

    /**
     * Show booking confirmation
     */
    public function confirmation(WorkshopBooking $booking)
    {
        $user = auth('web')->user();
        if (!$user || ($user->id !== $booking->user_id && $user->role !== 'admin')) {
            abort(403);
        }

        $booking->load(['workshopAvailableDate.workshop', 'slotSchedule.timeSlot', 'payments']);
        $booking = app(WorkshopPaymentSyncService::class)->syncBooking($booking);

        return view('workshop.booking.confirmation', compact('booking'));
    }

    /**
     * Get customer's bookings
     */
    public function myBookings()
    {
        $user = auth('web')->user();
        if (!$user) {
            abort(403);
        }

        $bookings = WorkshopBooking::where('user_id', $user->id)
            ->with(['workshopAvailableDate.workshop', 'slotSchedule.timeSlot', 'payments'])
            ->latest()
            ->paginate(10);

        $syncService = app(WorkshopPaymentSyncService::class);
        $bookings->setCollection(
            $bookings->getCollection()->map(fn ($booking) => $syncService->syncBooking($booking))
        );

        return view('workshop.my-bookings', compact('bookings'));
    }

    /**
     * Get booking detail
     */
    public function detail(WorkshopBooking $booking)
    {
        $user = auth('web')->user();
        if (!$user || ($user->id !== $booking->user_id && $user->role !== 'admin')) {
            abort(403);
        }

        $booking->load(['workshopAvailableDate.workshop', 'slotSchedule.timeSlot', 'payments', 'reminders']);
        $booking = app(WorkshopPaymentSyncService::class)->syncBooking($booking);

        return view('workshop.booking.detail', compact('booking'));
    }

    /**
     * Cancel booking
     */
    public function cancel(WorkshopBooking $booking, Request $request)
    {
        $user = auth('web')->user();
        if (!$user || $user->id !== $booking->user_id) {
            abort(403);
        }

        if ($booking->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel a completed booking.',
            ], 422);
        }

        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'This booking is already cancelled.',
            ], 422);
        }

        if ($booking->status === 'confirmed' || in_array($booking->payment_status, ['deposit_paid', 'fully_paid'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Booking yang sudah dikonfirmasi atau sudah dibayar tidak bisa dibatalkan otomatis. Silahkan hubungi admin.',
            ], 422);
        }

        if ($booking->payments()->where('payment_status', 'confirmed')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Booking dengan pembayaran terkonfirmasi tidak bisa dibatalkan otomatis. Silahkan hubungi admin.',
            ], 422);
        }

        $reason = $request->validate([
            'reason' => 'required|string|max:500',
        ])['reason'];

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        // NEW SYSTEM: kembalikan slot schedule ke available bila perlu
        if ($booking->slotSchedule) {
            $booking->slotSchedule->cancelParticipants((int) $booking->num_participants);
        }

        return response()->json([
            'success' => true,
            'message' => 'Your booking has been cancelled.',
        ]);
    }

    /**
     * Pay remaining amount (Manual)
     * ✅ TETAP ADA
     */
    public function payRemaining(Request $request, WorkshopBooking $booking)
    {
        // ... (kode lama tetap utuh)
        $user = auth('web')->user();
        if (!$user || $user->id !== $booking->user_id) {
            abort(403);
        }

        if ($booking->remaining_amount <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'No remaining payment for this booking.',
            ], 422);
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:bank_transfer,ewallet,credit_card',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_file')) {
            $proofPath = $request->file('proof_file')->store('workshop-payments', 'public');
        }

        $payment = $booking->payments()->create([
            'amount' => $booking->remaining_amount,
            'type' => 'remaining',
            'payment_method' => $validated['payment_method'],
            'payment_status' => 'pending',
            'proof_file_path' => $proofPath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Remaining payment submitted for verification.',
        ]);
    }

    /**
     * Create booking reminders
     */
    private function createBookingReminders(WorkshopBooking $booking)
    {
        $workshopDate = $booking->workshopDate;

        $booking->reminders()->create([
            'workshop_date_id' => $workshopDate->id,
            'type' => 'booking_confirmed',
            'delivery_method' => 'email',
            'status' => 'pending',
            'scheduled_for' => now(),
        ]);

        $booking->reminders()->create([
            'workshop_date_id' => $workshopDate->id,
            'type' => '7_days_before',
            'delivery_method' => 'email',
            'status' => 'pending',
            'scheduled_for' => $workshopDate->date->copy()->subDays(7)->startOfDay(),
        ]);

        $booking->reminders()->create([
            'workshop_date_id' => $workshopDate->id,
            'type' => '1_day_before',
            'delivery_method' => 'email',
            'status' => 'pending',
            'scheduled_for' => $workshopDate->date->copy()->subDay()->startOfDay(),
        ]);

        $booking->reminders()->create([
            'workshop_date_id' => $workshopDate->id,
            'type' => 'day_of',
            'delivery_method' => 'email',
            'status' => 'pending',
            'scheduled_for' => $workshopDate->date->copy()->setTime(8, 0),
        ]);

        $booking->reminders()->create([
            'workshop_date_id' => $workshopDate->id,
            'type' => 'post_workshop',
            'delivery_method' => 'email',
            'status' => 'pending',
            'scheduled_for' => $workshopDate->date->copy()->addDay()->startOfDay(),
        ]);
    }

    /**
     * Extract payment details from Tripay response untuk ditampilkan di modal
     * 
     * TRIPAY FIELD MAPPING:
     * - VA: pay_code (kode bayar Virtual Account)
     * - QRIS: qr_url (URL gambar QRIS)
     * - Convenience Store: reference (kode referensi)
     */
    private function extractPaymentDetails(string $method, array $tripayData): array
    {
        $details = [
            'method' => $method,
            'method_name' => $tripayData['payment_name'] ?? $tripayData['method_name'] ?? config("payment.methods.{$method}.name", $method),
        ];

        Log::debug('Extract Payment Details from Tripay', [
            'method' => $method,
            'available_fields' => array_keys($tripayData),
        ]);

        switch ($method) {
            case 'BCAVA':
            case 'BRIVA':
            case 'BNIVA':
            case 'MANDIRIVA':
            case 'PERMATAVA':
            case 'MYBVA':
            case 'CIMBVA':
                // Virtual Account - Tripay returns 'pay_code' as the VA account number
                $details['type'] = 'virtual_account';
                
                // IMPORTANT: Tripay field is 'pay_code', not customer_id
                $vaCode = $tripayData['pay_code'] ?? $tripayData['customer_id'] ?? null;
                
                if (!$vaCode) {
                    $vaCode = substr(str_pad(rand(0, 999999999999), 13, '0', STR_PAD_LEFT), 0, 13);
                    Log::warning('VA code not from Tripay, using generated', ['va' => $vaCode]);
                }
                
                $details['pay_code'] = $vaCode;
                $details['account_number'] = $vaCode; // Alias untuk frontend
                $details['va_number'] = $vaCode; // Alias untuk frontend
                $details['bank_name'] = $tripayData['payment_name'] ?? $tripayData['method_name'] ?? config("payment.methods.{$method}.name", $method);
                
                Log::info('VA Payment Details Extracted', [
                    'va_code' => $vaCode,
                    'bank' => $details['bank_name']
                ]);
                break;

            case 'QRIS':
                // QRIS Payment - Tripay returns 'qr_url' for the QR code image
                $details['type'] = 'qris';
                
                // IMPORTANT: Tripay field is 'qr_url', not qr_image_url
                $qrUrl = $tripayData['qr_url'] ?? $tripayData['qr_image_url'] ?? null;
                $qrString = $tripayData['qr_string'] ?? null;
                
                if (!$qrUrl) {
                    // Fallback: use qr_string to generate or show placeholder
                    Log::warning('QR URL not from Tripay, will use qr_string or placeholder', [
                        'has_qr_string' => !is_null($qrString)
                    ]);
                }
                
                $details['qr_url'] = $qrUrl;
                $details['qr_image_url'] = $qrUrl; // Alias untuk frontend
                $details['qr_string'] = $qrString;
                
                Log::info('QRIS Payment Details Extracted', [
                    'has_qr_url' => !is_null($qrUrl),
                    'has_qr_string' => !is_null($qrString)
                ]);
                break;

            case 'ALFAMART':
            case 'INDOMARET':
                // Convenience Store - Tripay returns 'reference' as payment code
                $details['type'] = 'convenience_store';
                
                $refCode = $tripayData['reference'] ?? $tripayData['reference_code'] ?? null;
                
                if (!$refCode) {
                    $prefix = $method === 'ALFAMART' ? 'ALFM' : 'INDO';
                    $refCode = $prefix . str_pad(rand(0, 999999999), 9, '0', STR_PAD_LEFT);
                    Log::warning('Reference code not from Tripay, using generated', ['ref' => $refCode]);
                }
                
                $details['reference'] = $refCode;
                $details['reference_code'] = $refCode; // Alias untuk frontend
                $details['store_name'] = $method === 'ALFAMART' ? 'Alfamart' : 'Indomaret';
                
                Log::info('Store Payment Details Extracted', [
                    'reference' => $refCode,
                    'store' => $details['store_name']
                ]);
                break;

            default:
                $details['type'] = 'unknown';
                Log::warning('Unknown payment method for details extraction', ['method' => $method]);
        }

        return $details;
    }
}
