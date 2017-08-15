<?php

namespace Provider;

use Firebase\JWT\JWT;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CategoryServiceProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $category = $app['controllers_factory'];

        $category->before(function (Request $request) use ($app) {
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

        $category
            ->get('/', 'CategoryController:getCategories')
            ->bind('getCategories')
        ;

        $category
            ->get('/{categoryId}', 'CategoryController:getCategory')
            ->assert('categoryId', '^[1-9]+[0-9]*$')
            ->bind('getCategory')
        ;

        $category
            ->get('/{categoryId}/products', 'CategoryController:getProductsByCategory')
            ->assert('categoryId', '^[1-9]+[0-9]*$')
            ->bind('getProductsByCategory')
        ;

        $category
            ->post('/', 'CategoryController:postCategory')
            ->bind('postCategory')
        ;

        $category
            ->patch('/{categoryId}', 'CategoryController:putCategory')
            ->assert('categoryId', '^[1-9]+[0-9]*$')
            ->bind('putCategory')
        ;

        $category
            ->delete('/{categoryId}', 'CategoryController:deleteCategory')
            ->assert('categoryId', '^[1-9]+[0-9]*$')
            ->bind('deleteCategory')
        ;

        return $category;
    }
}