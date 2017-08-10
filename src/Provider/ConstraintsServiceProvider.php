<?php

namespace Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Validator\UniqueUsernameValidator;

class ConstraintsServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['validator.unique_username'] = function () use ($app) {
            $validator = new UniqueUsernameValidator();
            $validator->setDependency($app['db']);

            return $validator;
        };
    }
}