<?php

namespace Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class CategoryController extends BaseController
{
    public function index()
    {
        return new JsonResponse([]);
    }
}
