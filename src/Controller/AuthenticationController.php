<?php

namespace Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

class AuthenticationController extends BaseController
{
    public function index()
    {
        return new JsonResponse([]);
    }
}