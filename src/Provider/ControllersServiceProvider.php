<?php

namespace Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Controller\ProductController;
use Controller\CategoryController;
use Controller\AuthenticationController;

class ControllersServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['AuthenticationController'] = function () use ($app) {
            return new AuthenticationController($app);
        };

        $app['ProductController'] = function () use ($app) {
            return new ProductController($app);
        };

        $app['CategoryController'] = function () use ($app) {
            return new CategoryController($app);
        };
    }
}