<?php

if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check ' . basename(__FILE__) . ' for more information.');
}

require_once __DIR__ . '/../vendor/autoload.php';

(new \Dotenv\Dotenv(__DIR__ . '/../app'))->load();

$app = new Silex\Application();

require_once __DIR__ . '/../app/config/dev.php';

require_once __DIR__ . '/../app/providers.php';

require_once __DIR__ . '/../app/routes.php';

$app->run();