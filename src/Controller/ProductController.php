<?php

namespace Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends BaseController
{
    public function index()
    {
        return new JsonResponse([]);
    }
}