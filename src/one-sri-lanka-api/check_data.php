<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check how many common complaints exist
$count = \App\Models\CommonComplaint::count();
echo "Total Common Complaints in database: " . $count . "\n";

// Show the latest 5 records
$latest = \App\Models\CommonComplaint::with('category')->latest()->take(5)->get();
echo "\nLatest 5 records:\n";
foreach ($latest as $complaint) {
    echo "ID: {$complaint->id} - {$complaint->title}\n";
}

// Run the seeder
echo "\nRunning CommonComplaintSeeder...\n";
try {
    $seeder = new \Database\Seeders\CommonComplaintSeeder();
    $seeder->run();
    echo "Seeder completed successfully!\n";
    
    // Check count after seeding
    $newCount = \App\Models\CommonComplaint::count();
    echo "New total: " . $newCount . "\n";
    echo "Added: " . ($newCount - $count) . " new records\n";
} catch (Exception $e) {
    echo "Error running seeder: " . $e->getMessage() . "\n";
}
