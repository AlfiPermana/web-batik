<?php
require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$dates = \DB::table('workshop_available_dates')->where('workshop_id', 1)->whereNotNull('deleted_at')->get();
echo 'Restoring ' . count($dates) . ' deleted dates...' . PHP_EOL;

\DB::table('workshop_available_dates')->where('workshop_id', 1)->whereNotNull('deleted_at')->update(['deleted_at' => null]);

echo 'Done! Dates restored.' . PHP_EOL;

// Verify
$active = \DB::table('workshop_available_dates')->where('workshop_id', 1)->whereNull('deleted_at')->count();
echo 'Active dates now: ' . $active . PHP_EOL;
?>
