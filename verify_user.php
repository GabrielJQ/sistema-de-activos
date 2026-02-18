<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $user = App\Models\User::where('email', 'admin@admin.com')->first();
    if ($user) {
        echo "User found: " . $user->name . "\n";
        echo "Supabase ID: " . ($user->supabase_user_id ?? 'NULL') . "\n";
    } else {
        echo "User not found.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
