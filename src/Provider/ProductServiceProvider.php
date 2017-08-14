<?php

namespace Provider;

use Firebase\JWT\JWT;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductServiceProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $product = $app['controllers_factory'];

        $product->before(function (Request $request) use ($app) {
            $authorizationHeader = $request->headers->get('Authorization');

            if ($authorizationHeader) {
                if (strpos($authorizationHeader, 'Bearer ') === false) {
                    return new JsonResponse(['message' => 'Unauthorized'], 401);
                }

                $token = str_replace('Bearer ', '', $authorizationHeader);

                try {
                    $app['user'] = JWT::decode($token, getenv('JWT_KEY'), [getenv('JWT_ALGORITHM')]);
                } catch (\Exception $e) {
                    return new JsonResponse(['message' => 'Unauthorized'], 401);
                }
            } else {
                return new JsonResponse(['message' => 'Bad Request'], 400);
            }
        });

        $product
            ->post('/', 'ProductController:postProduct')
            ->bind('postProduct')
        ;

        $product
            ->put('/{productId}', 'ProductController:putProduct')
            ->bind('putProduct')
        ;

        return $product;
    }
}