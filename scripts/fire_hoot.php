<?php
// Quick script to dispatch HootCreated event for local testing.
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

// Boot the application so service providers are registered
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$events = $app->make(Illuminate\Contracts\Events\Dispatcher::class);

// Create a lightweight Hoot model instance without persisting to DB
$hoot = new App\Models\Hoot(["id" => 9999, "message" => "Hello from local test", "created_at" => now()]);
$hoot->setRelation('user', (object)["id" => 1, "name" => "Local Tester"]);

// Ensure broadcasts run synchronously for this test (don't require queue DB)
$app['config']->set('queue.default', 'sync');

$events->dispatch(new App\Events\HootCreated($hoot));

echo "HootCreated event dispatched\n";
