<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a fake request to the daily overview endpoint
$request = Illuminate\Http\Request::create('/admin/academic/daily-overview?date=' . date('Y-m-d'), 'GET');

// Process the request
$response = $kernel->handle($request);

// Output the response content
echo "Response Status: " . $response->getStatusCode() . "\n";
echo "Response Content:\n";
echo $response->getContent();
echo "\n";

$kernel->terminate($request, $response);
