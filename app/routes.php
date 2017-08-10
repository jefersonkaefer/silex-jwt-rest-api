<?php

$app->mount('/api/v1', new \Provider\AuthenticationServiceProvider());

$app->mount('/api/v1/products', new \Provider\ProductServiceProvider());

$app->mount('/api/v1/categories', new \Provider\CategoryServiceProvider());