<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Restoring soft-deleted schedules...\n";

$restored = \DB::table('workshop_slot_schedules')
    ->where('workshop_id', 1)
    ->whereNotNull('deleted_at')
    ->update(['deleted_at' => null]);

echo "Restored: $restored schedules\n";

// Verify
$active = \DB::table('workshop_slot_schedules')->where('workshop_id', 1)->whereNull('deleted_at')->count();
$deleted = \DB::table('workshop_slot_schedules')->where('workshop_id', 1)->whereNotNull('deleted_at')->count();

echo "Active schedules: $active\n";
echo "Soft-deleted schedules: $deleted\n";
?>
