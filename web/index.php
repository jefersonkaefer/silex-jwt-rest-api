<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new \Dotenv\Dotenv(__DIR__ . '/../app'))->load();

$app = new Silex\Application();

require_once __DIR__ . '/../app/config/prod.php';

require_once __DIR__ . '/../app/providers.php';

require_once __DIR__ . '/../app/routes.php';

$app->run();