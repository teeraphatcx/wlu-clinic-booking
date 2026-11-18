<?php
header('Content-Type: application/json');
$status = function_exists('opcache_get_status') ? opcache_get_status(false) : null;
echo json_encode([
    'opcache_enabled' => $status ? $status['opcache_enabled'] : false,
    'cache_full' => $status ? $status['cache_full'] : false,
    'num_cached_scripts' => $status ? $status['opcache_statistics']['num_cached_scripts'] : 0,
    'hits' => $status ? $status['opcache_statistics']['hits'] : 0,
    'misses' => $status ? $status['opcache_statistics']['misses'] : 0,
    'memory_used_mb' => $status ? round($status['memory_usage']['used_memory'] / 1024 / 1024, 2) : 0,
]);
