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
                if (preg_match('/(?<=Bearer\s)([a-zA-Z0-9_=]+)\.([a-zA-Z0-9_=]+)\.([a-zA-Z0-9_\-\+\/=]+)/', $authorizationHeader, $matches)) {
                    $token = $matches[0];
                } else {
                    return new JsonResponse(['message' => 'Unauthorized'], 401);
                }

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
            ->get('/', 'ProductController:getProducts')
            ->bind('getProducts')
        ;

        $product
            ->get('/{productId}', 'ProductController:getProduct')
            ->assert('productId', '^[1-9]+[0-9]*$')
            ->bind('getProduct')
        ;

        $product
            ->post('/', 'ProductController:postProduct')
            ->bind('postProduct')
        ;

        $product
            ->put('/{productId}', 'ProductController:putProduct')
            ->assert('productId', '^[1-9]+[0-9]*$')
            ->bind('putProduct')
        ;

        $product
            ->delete('/{productId}', 'ProductController:deleteProduct')
            ->assert('productId', '^[1-9]+[0-9]*$')
            ->bind('deleteProduct')
        ;

        return $product;
    }
}