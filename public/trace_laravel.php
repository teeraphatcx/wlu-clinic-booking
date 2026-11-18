<?php
$t0 = microtime(true);
function mark($label) {
    global $t0;
    static $prev = null;
    if ($prev === null) $prev = $t0;
    $now = microtime(true);
    printf("[%8.3f ms] (+%8.3f ms) %s\n", ($now - $t0) * 1000, ($now - $prev) * 1000, $label);
    $prev = $now;
}

mark("Start");
require __DIR__ . '/../vendor/autoload.php';
mark("Autoload done");

$app = require __DIR__ . '/../bootstrap/app.php';
mark("App bootstrap/app.php done");

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
mark("Kernel instantiated");

$kernel->bootstrap();
mark("Kernel bootstrap done");

$request = Illuminate\Http\Request::create('/admin/login', 'GET');
mark("Request created");

$response = $kernel->handle($request);
mark("Kernel handle done (Status: " . $response->getStatusCode() . ")");
