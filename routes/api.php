<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WorkshopSlotController;
use App\Http\Controllers\Api\WorkshopBookingController;
use App\Http\Controllers\Api\AddressController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// User Address Routes (Protected - supports session and sanctum auth)
Route::middleware('auth:sanctum')->prefix('user/addresses')->group(function () {
    Route::get('/', [AddressController::class, 'index']);
    Route::post('/', [AddressController::class, 'store']);
    Route::delete('/{id}', [AddressController::class, 'destroy']);
});

// Get all active workshops
Route::get('/workshops/active', function() {
    return \App\Models\Workshop::where('is_active', true)
        ->select('id', 'title', 'description', 'amount', 'location')
        ->get();
});

// Workshop Slot Routes (Public)
Route::prefix('workshop-slots')->group(function () {
    // Get all time slots for a workshop
    Route::get('/{workshopId}/time-slots', [WorkshopSlotController::class, 'getTimeSlots']);
    
    // Get available slots for a specific date
    Route::get('/{workshopId}/date/{date}/available', [WorkshopSlotController::class, 'getAvailableSlotsForDate']);
    
    // Get all slot instances for a specific date
    Route::get('/{workshopId}/date/{date}/instances', [WorkshopSlotController::class, 'getSlotInstancesByDate']);
    
    // Get slot instance details
    Route::get('/instance/{instanceId}', [WorkshopSlotController::class, 'getSlotInstanceDetails']);
});

// Workshop Booking Routes
Route::prefix('workshop-bookings')->group(function () {
    // Public routes
    Route::post('/', [WorkshopBookingController::class, 'createBooking']);
    Route::get('/{bookingId}', [WorkshopBookingController::class, 'getBooking']);
    
    // Authenticated routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/{bookingId}/cancel', [WorkshopBookingController::class, 'cancelBooking']);
        Route::put('/{bookingId}/participants', [WorkshopBookingController::class, 'updateParticipants']);
    });
});

// Admin Workshop Slot Management Routes
Route::middleware(['auth:sanctum', 'admin'])->prefix('admin/workshop-slots')->group(function () {
    // Create time slot
    Route::post('/{workshopId}/time-slots', [WorkshopSlotController::class, 'createTimeSlot']);
    
    // Update time slot
    Route::put('/time-slots/{slotId}', [WorkshopSlotController::class, 'updateTimeSlot']);
    
    // Create slot instances for a date
    Route::post('/instances/create-for-date', [WorkshopSlotController::class, 'createSlotInstancesForDate']);
    
    // Get all bookings for a slot instance
    Route::get('/instance/{slotInstanceId}/bookings', [WorkshopSlotController::class, 'getSlotBookings']);
});
