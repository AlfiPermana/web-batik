<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\WorkshopBooking;

echo "=== UPDATE BOOKING 34 STATUS ===\n\n";

$booking = WorkshopBooking::find(34);
if ($booking) {
    echo "Booking ID: {$booking->id}\n";
    echo "Status SEBELUM: {$booking->status}\n";
    
    $booking->update(['status' => 'confirmed']);
    
    $booking->refresh();
    echo "Status SESUDAH: {$booking->status}\n\n";
    echo "✅ Booking berhasil diupdate!\n";
} else {
    echo "Booking not found!\n";
}
