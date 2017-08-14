<?php

namespace Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;
use Validator\CategoryIdExists;

class ProductController extends BaseController
{
    public function postProduct(Request $request)
    {
        $productCategoryId  = ($request->request->has('category_id')) ? $request->request->get('category_id') : null;
        $productName        = $request->request->get('name');
        $productDescription = $request->request->get('description');
        $productPrice       = $request->request->get('price');

        $errors = $this->app['validator']->validate([
            'productCategoryId'     => $productCategoryId,
            'productName'           => $productName,
            'productDescription'    => $productDescription,
            'productPrice'          => $productPrice
        ], new Assert\Collection([
            'productCategoryId' => [
                new CategoryIdExists()
            ],
            'productName' => [
                new Assert\NotBlank([
                    'message' => 'Product name should not be blank.'
                ]),
                new Assert\Length([
                    'min'           => 4,
                    'max'           => 128,
                    'minMessage'    => 'Product name should have 4 characters or more.',
                    'maxMessage'    => 'Product name should have 128 characters or less.'
                ])
            ],
            'productDescription' => [
                new Assert\NotBlank([
                    'message' => 'Product description should not be blank.'
                ]),
                new Assert\Length([
                    'min'           => 8,
                    'max'           => 65535,
                    'minMessage'    => 'Product description should have 8 characters or more.',
                    'maxMessage'    => 'Product description should have 65535 characters or less.'
                ])
            ],
            'productPrice' => [
                new Assert\NotBlank([
                    'message' => 'Product price should not be blank.'
                ]),
                new Assert\GreaterThan([
                    'value'     => 0,
                    'message'   => 'Product price should be greater than 0.'
                ]),
                new Assert\Regex([
                    'pattern'   => '/^[0-9]{1,6}(?:\.[0-9]{0,2})?$/',
                    'message'   => 'Product price is not valid.'
                ])
            ]
        ]));

        if (count($errors) > 0) {
            return new JsonResponse([
                'message' => $errors[0]->getMessage()
            ], 400);
        }

        $productInserted = $this->app['db']->insert('products', [
            'category_id'   => $productCategoryId,
            'user_id'       => $this->app['user']->user_id,
            'name'          => $productName,
            'description'   => $productDescription,
            'price'         => $productPrice
        ]);

        if (!$productInserted) {
            return new JsonResponse([
                'message' => 'Product insert fail.'
            ], 400);
        }

        return new JsonResponse([
            'message' => 'Product created.'
        ]);
    }

    public function putProduct(Request $request, $productId)
    {
        $productCategoryId  = ($request->request->has('category_id')) ? $request->request->get('category_id') : null;
        $productName        = $request->request->get('name');
        $productDescription = $request->request->get('description');
        $productPrice       = $request->request->get('price');

        $errors = $this->app['validator']->validate([
            'productCategoryId'     => $productCategoryId,
            'productName'           => $productName,
            'productDescription'    => $productDescription,
            'productPrice'          => $productPrice
        ], new Assert\Collection([
            'productCategoryId' => [
                new CategoryIdExists()
            ],
            'productName' => [
                new Assert\NotBlank([
                    'message' => 'Product name should not be blank.'
                ]),
                new Assert\Length([
                    'min'           => 4,
                    'max'           => 128,
                    'minMessage'    => 'Product name should have 4 characters or more.',
                    'maxMessage'    => 'Product name should have 128 characters or less.'
                ])
            ],
            'productDescription' => [
                new Assert\NotBlank([
                    'message' => 'Product description should not be blank.'
                ]),
                new Assert\Length([
                    'min'           => 8,
                    'max'           => 65535,
                    'minMessage'    => 'Product description should have 8 characters or more.',
                    'maxMessage'    => 'Product description should have 65535 characters or less.'
                ])
            ],
            'productPrice' => [
                new Assert\NotBlank([
                    'message' => 'Product price should not be blank.'
                ]),
                new Assert\GreaterThan([
                    'value'     => 0,
                    'message'   => 'Product price should be greater than 0.'
                ]),
                new Assert\Regex([
                    'pattern'   => '/^[0-9]{1,6}(?:\.[0-9]{0,2})?$/',
                    'message'   => 'Product price is not valid.'
                ])
            ]
        ]));

        if (count($errors) > 0) {
            return new JsonResponse([
                'message' => $errors[0]->getMessage()
            ], 400);
        }

        $productUpdated = $this->app['db']->update('products', [
            'category_id'   => $productCategoryId,
            'user_id'       => $this->app['user']->user_id,
            'name'          => $productName,
            'description'   => $productDescription,
            'price'         => $productPrice
        ], [
            'id' => $productId
        ]);

        if (!$productUpdated) {
            return new JsonResponse([
                'message' => 'Product update fail.'
            ], 400);
        }

        return new JsonResponse([
            'message' => 'Product updated.'
        ]);
    }

    public function deleteProduct($productId)
    {
        $productDeleted = $this->app['db']->delete('products', [
            'id' => $productId
        ]);

        if (!$productDeleted) {
            return new JsonResponse([
                'message' => 'Product delete fail.'
            ], 400);
        }

        return new JsonResponse([
            'message' => 'Product deleted.'
        ]);
    }
}