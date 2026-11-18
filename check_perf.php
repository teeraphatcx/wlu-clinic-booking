<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';

$app->bootstrapWith([
    Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class,
    Illuminate\Foundation\Bootstrap\LoadConfiguration::class,
    Illuminate\Foundation\Bootstrap\HandleExceptions::class,
    Illuminate\Foundation\Bootstrap\RegisterFacades::class,
]);

echo "Measuring Providers registration:\n";
$providers = (require __DIR__ . '/bootstrap/providers.php');
$registered = [];
foreach ($providers as $p) {
    $t0 = microtime(true);
    $instance = $app->register($p);
    $t1 = microtime(true);
    printf("  Register %s: %.4fs\n", class_basename($p), $t1 - $t0);
    $registered[] = $instance;
}

echo "Measuring Providers boot:\n";
foreach ($registered as $inst) {
    $t0 = microtime(true);
    if (method_exists($inst, 'boot')) {
        $app->call([$inst, 'boot']);
    }
    $t1 = microtime(true);
    if (($t1 - $t0) > 0.05) {
        printf("  SLOW BOOT %s: %.4fs\n", class_basename($inst), $t1 - $t0);
    }
}
