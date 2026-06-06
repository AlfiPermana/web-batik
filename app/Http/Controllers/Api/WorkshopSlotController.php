<?php

namespace App\Http\Controllers\Api;

use App\Models\Workshop;
use App\Models\WorkshopTimeSlot;
use App\Models\WorkshopAvailableDate;
use App\Models\WorkshopSlotSchedule;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WorkshopSlotController extends \App\Http\Controllers\Controller
{
    /**
     * Get time slots template untuk workshop
     */
    public function getTimeSlots($workshopId): JsonResponse
    {
        try {
            $workshop = Workshop::findOrFail($workshopId);
            $slots = $workshop->timeSlots()->active()->ordered()->get();

            return response()->json([
                'success' => true,
                'data' => $slots->map(fn ($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'start_time' => $s->start_time,
                    'end_time' => $s->end_time,
                    'time_range' => $s->getTimeRangeFormatted(),
                ]),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    /**
     * Get available dates untuk workshop
     */
    public function getAvailableDates($workshopId): JsonResponse
    {
        try {
            $dates = WorkshopAvailableDate::forWorkshop($workshopId)
                ->active()
                ->upcoming()
                ->with(['schedules' => function ($q) {
                    $q->where('status', '!=', 'cancelled');
                }])
                ->get();

            return response()->json([
                'success' => true,
                'data' => $dates->map(fn ($d) => [
                    'id' => $d->id,
                    'date' => $d->date->format('Y-m-d'),
                    'date_formatted' => $d->getDateFormatted(),
                    'has_available_slots' => $d->hasAvailableSlots(),
                    'available_slots_count' => $d->schedules()->where('status', 'available')->where('is_cancelled', false)->count(),
                ]),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Get slot schedules untuk tanggal spesifik
     */
    public function getSchedulesForDate($workshopId, $date): JsonResponse
    {
        try {
            $schedules = WorkshopSlotSchedule::forWorkshop($workshopId)
                ->forDate($date)
                ->with(['timeSlot', 'availableDate'])
                ->orderBy('id')
                ->get();

            if ($schedules->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'No schedules for this date',
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $schedules->map(fn ($s) => [
                    'id' => $s->id,
                    'date' => $s->date->format('Y-m-d'),
                    'time_slot_id' => $s->time_slot_id,
                    'time_slot_name' => $s->timeSlot->name,  // Changed to time_slot_name
                    'slot_name' => $s->timeSlot->name,  // Keep for backward compatibility
                    'start_time' => substr($s->timeSlot->start_time, 0, 5),
                    'end_time' => substr($s->timeSlot->end_time, 0, 5),
                    'booked_count' => $s->booked_count,
                    'status' => $s->status,
                    'is_available' => !$s->is_cancelled && $s->status === 'available',
                ]),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * ADMIN: Create time slot template
     */
    public function createTimeSlot(Request $request, $workshopId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'order' => 'integer|min:0',
            ]);

            $workshop = Workshop::findOrFail($workshopId);
            
            $defaultMaxCapacity = 1000000;

            $slot = WorkshopTimeSlot::create([
                'workshop_id' => $workshopId,
                'name' => $validated['name'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'max_capacity' => $defaultMaxCapacity,
                'order' => $validated['order'] ?? 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Time slot created',
                'data' => $slot,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * ADMIN: Add available date untuk workshop
     */
    public function addAvailableDate(Request $request, $workshopId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'date' => 'required|date|after_or_equal:today',
                'notes' => 'nullable|string',
            ]);

            $workshop = Workshop::findOrFail($workshopId);

            // Check if date already exists
            $existing = WorkshopAvailableDate::where([
                'workshop_id' => $workshopId,
                'date' => $validated['date'],
            ])->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Date already added',
                ], 422);
            }

            // Create available date
            $availableDate = WorkshopAvailableDate::create([
                'workshop_id' => $workshopId,
                'date' => $validated['date'],
                'notes' => $validated['notes'],
            ]);

            // Auto-create schedules for all active time slots
            $timeSlots = $workshop->timeSlots()->active()->get();
            $schedules = [];

            foreach ($timeSlots as $slot) {
                $schedule = WorkshopSlotSchedule::create([
                    'workshop_id' => $workshopId,
                    'time_slot_id' => $slot->id,
                    'available_date_id' => $availableDate->id,
                    'date' => $validated['date'],
                    'max_capacity' => $slot->max_capacity,
                    'booked_count' => 0,
                    'status' => 'available',
                ]);
                $schedules[] = $schedule;
            }

            return response()->json([
                'success' => true,
                'message' => 'Date added with ' . count($schedules) . ' schedules created',
                'data' => [
                    'available_date' => $availableDate,
                    'schedules' => $schedules,
                ],
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * ADMIN: Remove available date
     */
    public function removeAvailableDate($dateId): JsonResponse
    {
        try {
            $availableDate = WorkshopAvailableDate::findOrFail($dateId);
            
            // Check if there are active bookings
            $activeBookings = $availableDate->schedules()
                ->with('bookings')
                ->get()
                ->flatMap(fn ($s) => $s->bookings)
                ->where('status', 'pending')
                ->count();

            if ($activeBookings > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete date with active bookings',
                ], 422);
            }

            // Soft delete
            $availableDate->delete();

            return response()->json([
                'success' => true,
                'message' => 'Date removed',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Get schedule details
     */
    public function getScheduleDetails($scheduleId): JsonResponse
    {
        try {
            $schedule = WorkshopSlotSchedule::with(['timeSlot', 'availableDate', 'bookings'])->findOrFail($scheduleId);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $schedule->id,
                    'date' => $schedule->date->format('Y-m-d'),
                    'time_slot' => [
                        'name' => $schedule->timeSlot->name,
                        'start_time' => $schedule->timeSlot->start_time,
                        'end_time' => $schedule->timeSlot->end_time,
                    ],
                    'booked_count' => $schedule->booked_count,
                    'status' => $schedule->status,
                    'bookings_count' => $schedule->bookings->count(),
                    'total_participants' => $schedule->bookings->sum('num_participants'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }
}
