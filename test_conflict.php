<?php
// Simple test to verify conflict checker optimization
require_once __DIR__ . '/vendor/autoload.php';

use App\Services\ReservationConflictService;
use Carbon\Carbon;

// Set up Laravel app
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing optimized conflict checker...\n";

$service = new ReservationConflictService();

// Test the time overlap constraint method
echo "✓ ReservationConflictService instantiated successfully\n";

// Test basic conflict check (assuming lab ID 1 exists)
try {
    $result = $service->checkConflicts(1, '2025-08-20', '09:00', '10:00');
    echo "✓ Basic conflict check completed: " . ($result['has_conflict'] ? 'Has conflicts' : 'No conflicts') . "\n";
} catch (Exception $e) {
    echo "✗ Basic conflict check failed: " . $e->getMessage() . "\n";
}

echo "Conflict checker optimization test completed.\n";
