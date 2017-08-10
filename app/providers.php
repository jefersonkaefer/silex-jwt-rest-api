<?php

$app->register(new \Silex\Provider\ServiceControllerServiceProvider());

$app->register(new \Provider\ControllersServiceProvider());

$app->register(new \Silex\Provider\DoctrineServiceProvider(), [
    'db.options' => [
        'driver'    => getenv('DATABASE_DRIVER'),
        'host'      => getenv('DATABASE_HOST'),
        'user'      => getenv('DATABASE_USER'),
        'password'  => getenv('DATABASE_PASSWORD'),
        'dbname'    => getenv('DATABASE_NAME'),
        'charset'   => getenv('DATABASE_CHARSET')
    ]
]);

$app->register(new \Silex\Provider\ValidatorServiceProvider());