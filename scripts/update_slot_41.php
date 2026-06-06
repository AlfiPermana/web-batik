<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\WorkshopSlotSchedule;
use App\Models\WorkshopPayment;

echo "=== CEK SLOT 41 ===\n\n";

$slot = WorkshopSlotSchedule::find(41);
if ($slot) {
    echo "Slot ID: {$slot->id}\n";
    echo "Status: {$slot->status}\n";
    
    // Cek apakah ada confirmed payment untuk slot ini
    $payments = WorkshopPayment::whereHas('booking', function ($q) {
        $q->where('workshop_slot_schedule_id', 41);
    })->where('payment_status', 'confirmed')->get();
    
    echo "Confirmed payments: " . $payments->count() . "\n\n";
    
    if ($payments->count() > 0) {
        echo "Updating slot 41 to PAID...\n";
        $slot->update(['status' => 'PAID']);
        echo "✅ Slot 41 updated to PAID\n";
    }
} else {
    echo "Slot not found!\n";
}
