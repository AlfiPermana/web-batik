<?php

namespace App\Http\Controllers\Api;

use App\Models\Workshop;
use App\Models\WorkshopBooking;
use App\Models\WorkshopSlotInstance;
use App\Models\WorkshopSlotSchedule;
use App\Models\WorkshopPayment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WorkshopBookingController extends \App\Http\Controllers\Controller
{
    /**
     * Create a new workshop booking dengan flexible participants
     */
    public function createBooking(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'workshop_slot_schedule_id' => 'required|exists:workshop_slot_schedules,id',
                'customer_name' => 'required|string|max:255',
                'customer_email' => 'required|email|max:255',
                'customer_phone' => 'required|string|max:20',
                'num_participants' => 'required|integer|min:1',
                'special_requests' => 'nullable|string',
            ]);

            // Get schedule
            $schedule = WorkshopSlotSchedule::with(['timeSlot', 'workshop'])->findOrFail(
                $validated['workshop_slot_schedule_id']
            );

            // Validate dapat book
            if (!$schedule->canBook($validated['num_participants'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal ini sudah tidak tersedia.',
                ], 422);
            }

            // Calculate price (harga PER PESERTA)
            $workshop = $schedule->workshop;
            $unitPrice = (float) $workshop->amount;
            $totalPrice = $unitPrice * (int) $validated['num_participants'];
            $depositAmount = (int) ceil($totalPrice / 2);


            // Create booking
            $bookingNumber = 'WS-' . now()->format('Ym') . '-' . Str::random(6);
            
            $booking = DB::transaction(function () use ($validated, $schedule, $workshop, $bookingNumber, $totalPrice, $depositAmount) {
                $lockedSchedule = WorkshopSlotSchedule::query()->lockForUpdate()->findOrFail($schedule->id);

                if (!$lockedSchedule->canBook((int) $validated['num_participants'])) {
                    throw new \RuntimeException('Jadwal ini sudah tidak tersedia.');
                }

                $booking = WorkshopBooking::create([
                    'user_id' => optional(auth()->user())->id,
                    'workshop_date_id' => $lockedSchedule->available_date_id,
                    'workshop_slot_schedule_id' => $lockedSchedule->id,
                    'booking_number' => $bookingNumber,
                    'customer_name' => $validated['customer_name'],
                    'customer_email' => $validated['customer_email'],
                    'customer_phone' => $validated['customer_phone'],
                    'workshop_date' => $lockedSchedule->date,
                    'workshop_name' => $workshop->title,
                    'start_time' => $lockedSchedule->timeSlot->start_time,
                    'end_time' => $lockedSchedule->timeSlot->end_time,
                    'num_participants' => $validated['num_participants'],
                    'total_price' => $totalPrice,
                    'deposit_amount' => $depositAmount,
                    'remaining_amount' => $totalPrice - $depositAmount,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'special_requests' => $validated['special_requests'],
                ]);

                if (!$lockedSchedule->bookParticipants((int) $validated['num_participants'])) {
                    throw new \RuntimeException('Gagal mengunci jadwal workshop.');
                }

                return $booking;
            });

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully',
                'data' => [
                    'booking_id' => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'num_participants' => $booking->num_participants,
                    'total_price' => $booking->total_price,
                    'deposit_amount' => $booking->deposit_amount,
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get booking details
     */
    public function getBooking($bookingId): JsonResponse
    {
        try {
            $booking = WorkshopBooking::with(['payments'])
                ->findOrFail($bookingId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'customer_name' => $booking->customer_name,
                    'customer_email' => $booking->customer_email,
                    'customer_phone' => $booking->customer_phone,
                    'date' => $booking->workshop_date,
                    'time' => $booking->start_time . ' - ' . $booking->end_time,
                    'workshop_name' => $booking->workshop_name,
                    'num_participants' => $booking->num_participants,
                    'total_price' => $booking->total_price,
                    'deposit_amount' => $booking->deposit_amount,
                    'remaining_amount' => $booking->remaining_amount,
                    'status' => $booking->status,
                    'payment_status' => $booking->payment_status,
                    'special_requests' => $booking->special_requests,
                    'created_at' => $booking->created_at,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Cancel booking
     */
    public function cancelBooking(Request $request, $bookingId): JsonResponse
    {
        try {
            $booking = WorkshopBooking::findOrFail($bookingId);

            if ($booking->status === 'cancelled') {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking is already cancelled',
                ], 422);
            }

            if ($booking->status === 'confirmed' || in_array($booking->payment_status, ['deposit_paid', 'fully_paid'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking yang sudah dikonfirmasi atau dibayar tidak bisa dibatalkan otomatis',
                ], 422);
            }

            if ($booking->payments()->where('payment_status', 'confirmed')->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Booking dengan pembayaran terkonfirmasi tidak bisa dibatalkan otomatis',
                ], 422);
            }

            $validated = $request->validate([
                'cancellation_reason' => 'nullable|string|max:255',
            ]);

            // Free up slots using new schedule model
            $schedule = WorkshopSlotSchedule::find($booking->workshop_slot_schedule_id);
            if ($schedule) {
                $schedule->cancelParticipants($booking->num_participants);
            }

            // Cancel booking
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $validated['cancellation_reason'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update number of participants (for pending bookings)
     */
    public function updateParticipants(Request $request, $bookingId): JsonResponse
    {
        try {
            $booking = WorkshopBooking::findOrFail($bookingId);

            if ($booking->status !== 'pending' || in_array($booking->payment_status, ['deposit_paid', 'fully_paid'], true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Can only update unpaid pending bookings',
                ], 422);
            }

            $validated = $request->validate([
                'num_participants' => 'required|integer|min:1',
            ]);

            $schedule = WorkshopSlotSchedule::find($booking->workshop_slot_schedule_id);
            
            // Check if new number fits
            $difference = $validated['num_participants'] - $booking->num_participants;
            
            if ($difference > 0 && !$schedule->canBook($difference)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jadwal ini sudah tidak tersedia untuk update peserta.',
                ], 422);
            }

            // Update slots
            if ($difference > 0) {
                $schedule->bookParticipants($difference);
            } else {
                $schedule->cancelParticipants(abs($difference));
            }

            // Update booking (harga PER PESERTA)
            $schedule->loadMissing('workshop');
            $unitPrice = (float) ($schedule->workshop?->amount ?? 0);

            $totalPrice = $unitPrice * (int) $validated['num_participants'];
            $depositAmount = (int) ceil($totalPrice / 2);

            $booking->update([
                'num_participants' => $validated['num_participants'],
                'total_price' => $totalPrice,
                'deposit_amount' => $depositAmount,
                'remaining_amount' => $totalPrice - $depositAmount,
            ]);


            return response()->json([
                'success' => true,
                'message' => 'Participants updated successfully',
                'data' => [
                    'num_participants' => $booking->num_participants,
                    'total_price' => $booking->total_price,
                    'deposit_amount' => $booking->deposit_amount,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get all bookings for a slot schedule (Admin)
     */
    public function getSlotBookings($scheduleId): JsonResponse
    {
        try {
            $bookings = WorkshopBooking::where('workshop_slot_schedule_id', $scheduleId)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $bookings->map(fn ($b) => [
                    'id' => $b->id,
                    'booking_number' => $b->booking_number,
                    'customer_name' => $b->customer_name,
                    'customer_email' => $b->customer_email,
                    'num_participants' => $b->num_participants,
                    'status' => $b->status,
                    'payment_status' => $b->payment_status,
                ]),
                'count' => $bookings->count(),
                'total_participants' => $bookings->sum('num_participants'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
