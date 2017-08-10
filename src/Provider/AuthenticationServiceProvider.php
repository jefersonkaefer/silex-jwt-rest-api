<?php

namespace Provider;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;

class AuthenticationServiceProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $authentication = $app['controllers_factory'];

        $authentication->get('/', 'AuthenticationController:index');

        return $authentication;
    }
}