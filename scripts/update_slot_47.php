<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\WorkshopSlotSchedule;

echo "=== UPDATE SLOT 47 KE PAID ===\n\n";

$slot = WorkshopSlotSchedule::find(47);
if ($slot) {
    echo "Slot ID: {$slot->id}\n";
    echo "Status SEBELUM: {$slot->status}\n";
    
    $slot->update(['status' => 'PAID']);
    
    $slot->refresh();
    echo "Status SESUDAH: {$slot->status}\n\n";
    echo "✅ Slot berhasil diupdate!\n";
} else {
    echo "Slot not found!\n";
}
