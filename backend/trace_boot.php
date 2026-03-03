<?php
define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
echo "Step 1: Autoload done\n";
$app = require_once __DIR__.'/bootstrap/app.php';
echo "Step 2: App loaded\n";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
echo "Step 3: Kernel resolved\n";
$status = $kernel->bootstrap();
echo "Step 4: Bootstrapped\n";
echo "Step 5: Querying via DB facade...\n";
$user = \Illuminate\Support\Facades\DB::table('users')->first();
echo "Step 6: Found user via DB: " . ($user ? $user->email : 'none') . "\n";
use App\Models\User;
echo "Step 7: Querying via Eloquent...\n";
$user = User::first();
echo "Step 8: Found user via Eloquent: " . ($user ? $user->email : 'none') . "\n";
