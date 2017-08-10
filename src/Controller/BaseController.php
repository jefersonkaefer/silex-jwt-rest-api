<?php

namespace Controller;

use Silex\Application;

abstract class BaseController
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }
}