<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Set Address 6 as default
\App\Models\Address::where('id', 6)->update(['is_default' => 1]);
\App\Models\Address::where('id', '<>', 6)->update(['is_default' => 0]);

echo "✓ Updated: Address 6 (Purbalingga - KALIMANAH) is now default\n";

// Show all addresses
$addresses = \App\Models\Address::select('id', 'label', 'city', 'district', 'is_default')->get();
foreach ($addresses as $addr) {
    $default = $addr->is_default ? '✓ DEFAULT' : '';
    echo "ID {$addr->id}: {$addr->label} ({$addr->city}/{$addr->district}) {$default}\n";
}
?>
