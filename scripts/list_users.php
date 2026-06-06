<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== ALL USERS ===\n";
$users = User::all();
foreach ($users as $user) {
    echo "ID: " . $user->id . " | Name: " . $user->name . " | Email: " . $user->email . " | Role: " . $user->role . "\n";
}
?>
