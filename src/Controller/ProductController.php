<?php

namespace Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class ProductController extends BaseController
{
    public function index()
    {
        return new JsonResponse([]);
    }
}