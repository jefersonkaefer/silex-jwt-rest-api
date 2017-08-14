<?php

namespace Controller;

use Service\Paginator;
use Validator\CategoryIdExists;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;

class ProductController extends BaseController
{
    public function getProducts(Request $request)
    {
        $availableFields = [
            'id'            => 'p.id',
            'category_id'   => 'p.category_id',
            'user_id'       => 'p.user_id',
            'name'          => 'p.name',
            'description'   => 'p.description',
            'price'         => 'p.price',
            'created_at'    => 'p.created_at'
        ];

        $availableOperations = ['eq', 'neq', 'lt', 'lte', 'gt', 'gte'];

        $selectedFields = [];

        $qb = $this->app['db']->createQueryBuilder();

        if ($fields = $request->query->get('fields')) {
            foreach ($fields as $field) {
                if (empty($field)) {
                    return new JsonResponse([
                        'message' => "Fields[] parameter can not be empty."
                    ], 400);
                };

                if (!in_array($field, array_keys($availableFields))) {
                    return new JsonResponse([
                        'message' => "Field {$field} does not exists."
                    ], 400);
                }

                if ($request->query->has('includeUser') && $field === 'user_id') {
                    return new JsonResponse([
                        'message' => "You can not select user_id with user include."
                    ], 400);
                }

                if ($request->query->has('includeCategory') && $field === 'category_id') {
                    return new JsonResponse([
                        'message' => "You can not select category_id with category include."
                    ], 400);
                }

                $selectedFields[] = $availableFields[$field];
            }
        } else {
            if ($request->query->has('includeUser') || $request->query->has('includeCategory')) {
                if ($request->query->has('includeCategory') && !$request->query->has('includeUser')) {
                    $selectedFields = ['p.id', 'p.user_id', 'p.name', 'p.description', 'p.price', 'p.created_at'];
                }

                if (!$request->query->has('includeCategory') && $request->query->has('includeUser')) {
                    $selectedFields = ['p.id', 'p.category_id', 'p.name', 'p.description', 'p.price', 'p.created_at'];
                }

                if ($request->query->has('includeUser') && $request->query->has('includeCategory')) {
                    $selectedFields = ['p.id', 'p.name', 'p.description', 'p.price', 'p.created_at'];
                }
            } else {
                $selectedFields = ['p.*'];
            }
        }

        $qb->select(implode(', ', $selectedFields));

        $qb->from('products', 'p');

        if ($request->query->has('includeCategory')) {
            $qb->addSelect('c.id AS category_id, c.name AS category_name');
            $qb->leftJoin('p', 'categories', 'c', 'c.id = p.category_id');
        }

        if ($request->query->has('includeUser')) {
            $qb->addSelect('u.id AS user_id, u.username AS user_username');
            $qb->leftJoin('p', 'users', 'u', 'u.id = p.user_id');
        }

        if ($where = $request->query->get('where')) {
            $where = explode(',', $where);

            if (count($where) != 3) {
                return new JsonResponse([
                    'message' => "[where] Parameters form is not valid."
                ], 400);
            }

            $field      = $where[0];
            $operation  = $where[1];
            $value      = $where[2];

            if (!in_array($field, array_keys($availableFields))) {
                return new JsonResponse([
                    'message' => "[where] Field {$field} does not exists."
                ], 400);
            }

            if (!in_array($operation, $availableOperations)) {
                return new JsonResponse([
                    'message' => "[where] Operation {$operation} does not exists."
                ], 400);
            }

            if (empty($value)) {
                return new JsonResponse([
                    'message' => "[where] Value can not be empty."
                ], 400);
            }

            switch ($operation) {
                case 'eq':
                    $qb->where(
                        $qb->expr()->eq($availableFields[$field], $value)
                    );
                    break;
                case 'neq':
                    $qb->where(
                        $qb->expr()->neq($availableFields[$field], $value)
                    );
                    break;
                case 'lt':
                    $qb->where(
                        $qb->expr()->lt($availableFields[$field], $value)
                    );
                    break;
                case 'lte':
                    $qb->where(
                        $qb->expr()->lte($availableFields[$field], $value)
                    );
                    break;
                case 'gt':
                    $qb->where(
                        $qb->expr()->gt($availableFields[$field], $value)
                    );
                    break;
                case 'gte':
                    $qb->where(
                        $qb->expr()->gte($availableFields[$field], $value)
                    );
                    break;
            }
        }

        if ($orWhere = $request->query->get('orWhere')) {
            $orWhere = explode(',', $orWhere);

            if (count($orWhere) != 3) {
                return new JsonResponse([
                    'message' => "[orWhere] Parameters form is not valid."
                ], 400);
            }

            $field      = $orWhere[0];
            $operation  = $orWhere[1];
            $value      = $orWhere[2];

            if (!in_array($field, array_keys($availableFields))) {
                return new JsonResponse([
                    'message' => "[orWhere] Field {$field} does not exists."
                ], 400);
            }

            if (!in_array($operation, $availableOperations)) {
                return new JsonResponse([
                    'message' => "[orWhere] Operation {$operation} does not exists."
                ], 400);
            }

            if (empty($value)) {
                return new JsonResponse([
                    'message' => "[orWhere] Value can not be empty."
                ], 400);
            }

            switch ($operation) {
                case 'eq':
                    $qb->orWhere(
                        $qb->expr()->eq($availableFields[$field], $value)
                    );
                    break;
                case 'neq':
                    $qb->orWhere(
                        $qb->expr()->neq($availableFields[$field], $value)
                    );
                    break;
                case 'lt':
                    $qb->orWhere(
                        $qb->expr()->lt($availableFields[$field], $value)
                    );
                    break;
                case 'lte':
                    $qb->orWhere(
                        $qb->expr()->lte($availableFields[$field], $value)
                    );
                    break;
                case 'gt':
                    $qb->orWhere(
                        $qb->expr()->gt($availableFields[$field], $value)
                    );
                    break;
                case 'gte':
                    $qb->orWhere(
                        $qb->expr()->gte($availableFields[$field], $value)
                    );
                    break;
            }
        }

        if ($andWhere = $request->query->get('andWhere')) {
            $andWhere = explode(',', $andWhere);

            if (count($andWhere) != 3) {
                return new JsonResponse([
                    'message' => "[andWhere] Parameters form is not valid."
                ], 400);
            }

            $field      = $andWhere[0];
            $operation  = $andWhere[1];
            $value      = $andWhere[2];

            if (!in_array($field, array_keys($availableFields))) {
                return new JsonResponse([
                    'message' => "[andWhere] Field {$field} does not exists."
                ], 400);
            }

            if (!in_array($operation, $availableOperations)) {
                return new JsonResponse([
                    'message' => "[andWhere] Operation {$operation} does not exists."
                ], 400);
            }

            if (empty($value)) {
                return new JsonResponse([
                    'message' => "[andWhere] Value can not be empty."
                ], 400);
            }

            switch ($operation) {
                case 'eq':
                    $qb->andWhere(
                        $qb->expr()->eq($availableFields[$field], $value)
                    );
                    break;
                case 'neq':
                    $qb->andWhere(
                        $qb->expr()->neq($availableFields[$field], $value)
                    );
                    break;
                case 'lt':
                    $qb->andWhere(
                        $qb->expr()->lt($availableFields[$field], $value)
                    );
                    break;
                case 'lte':
                    $qb->andWhere(
                        $qb->expr()->lte($availableFields[$field], $value)
                    );
                    break;
                case 'gt':
                    $qb->andWhere(
                        $qb->expr()->gt($availableFields[$field], $value)
                    );
                    break;
                case 'gte':
                    $qb->andWhere(
                        $qb->expr()->gte($availableFields[$field], $value)
                    );
                    break;
            }
        }

        if ($request->query->has('orderBy')) {
            foreach ($request->query->get('orderBy') as $orderBy) {
                $orderBy = explode(',', $orderBy);

                if (count($orderBy) != 2) {
                    return new JsonResponse([
                        'message' => "[orderBy] Parameters form is not valid."
                    ], 400);
                }

                $field = $orderBy[0];
                $order = strtoupper($orderBy[1]);

                if (!in_array($field, array_keys($availableFields))) {
                    return new JsonResponse([
                        'message' => "[orderBy] Field {$field} does not exists."
                    ], 400);
                }

                if (!in_array($order, ['ASC', 'DESC'])) {
                    return new JsonResponse([
                        'message' => "[orderBy] Order type {$order} does not exists."
                    ]);
                }

                $qb->addOrderBy($availableFields[$field], $order);
            }
        } else {
            $qb->orderBy('p.id', 'ASC');
        }

        if ($page = $request->query->get('page')) {
            if ($request->query->get('itemsPerPage')) {
                $itemsPerPage = $request->query->get('itemsPerPage');
            } else {
                $itemsPerPage = getenv('ITEMS_PER_PAGE');
            }

            $paginator = new Paginator($qb, $page, $itemsPerPage);

            $pagination = $paginator->paginate();
        } else {
            $pagination = null;
        }

        $rows = $this->app['db']->fetchAll($qb->getSQL());

        foreach ($rows as $key => $row) {
            if (in_array('p.id', $selectedFields) || $selectedFields[0] == 'p.*') {
                $products[$key]['id'] = $row['id'];
            }

            if (!$request->query->has('includeCategory')) {
                if (in_array('p.category_id', $selectedFields) || $selectedFields[0] == 'p.*') {
                    $products[$key]['category_id'] = $row['category_id'];
                }
            } else {
                $products[$key]['category'] = [
                    'id'    => $row['category_id'],
                    'name'  => $row['category_name']
                ];
            }

            if (!$request->query->has('includeUser')) {
                if (in_array('p.user_id', $selectedFields) || $selectedFields[0] == 'p.*') {
                    $products[$key]['user_id'] = $row['user_id'];
                }
            } else {
                $products[$key]['user'] = [
                    'id'        => $row['user_id'],
                    'username'  => $row['user_username']
                ];
            }

            if (in_array('p.name', $selectedFields) || $selectedFields[0] == 'p.*') {
                $products[$key]['name'] = $row['name'];
            }

            if (in_array('p.description', $selectedFields) || $selectedFields[0] == 'p.*') {
                $products[$key]['description'] = $row['description'];
            }

            if (in_array('p.price', $selectedFields) || $selectedFields[0] == 'p.*') {
                $products[$key]['price'] = $row['price'];
            }

            if (in_array('p.created_at', $selectedFields) || $selectedFields[0] == 'p.*') {
                $products[$key]['created_at'] = $row['created_at'];
            }
        }

        if ($pagination) $response['pagination'] = $pagination;

        $response['products'] = $products;

        return new JsonResponse($response);
    }

    public function getProduct(Request $request, $productId)
    {
        $availableFields = [
            'id'            => 'p.id',
            'category_id'   => 'p.category_id',
            'user_id'       => 'p.user_id',
            'name'          => 'p.name',
            'description'   => 'p.description',
            'price'         => 'p.price',
            'created_at'    => 'p.created_at'
        ];

        $selectedFields = [];

        $qb = $this->app['db']->createQueryBuilder();

        if ($fields = $request->query->get('fields')) {
            foreach ($fields as $field) {
                if (empty($field)) {
                    return new JsonResponse([
                        'message' => "Fields[] parameter can not be empty."
                    ], 400);
                }

                if (!in_array($field, array_keys($availableFields))) {
                    return new JsonResponse([
                        'message' => "Field {$field} does not exists."
                    ], 400);
                }

                if ($request->query->has('includeUser') && $field === 'user_id') {
                    return new JsonResponse([
                        'message' => "You can not select user_id with user include."
                    ], 400);
                }

                if ($request->query->has('includeCategory') && $field === 'category_id') {
                    return new JsonResponse([
                        'message' => "You can not select category_id with category include."
                    ], 400);
                }

                $selectedFields[] = $availableFields[$field];
            }
        } else {
            if ($request->query->has('includeUser') || $request->query->has('includeCategory')) {
                if ($request->query->has('includeCategory') && !$request->query->has('includeUser')) {
                    $selectedFields = ['p.id', 'p.user_id', 'p.name', 'p.description', 'p.price', 'p.created_at'];
                }

                if (!$request->query->has('includeCategory') && $request->query->has('includeUser')) {
                    $selectedFields = ['p.id', 'p.category_id', 'p.name', 'p.description', 'p.price', 'p.created_at'];
                }

                if ($request->query->has('includeUser') && $request->query->has('includeCategory')) {
                    $selectedFields = ['p.id', 'p.name', 'p.description', 'p.price', 'p.created_at'];
                }
            } else {
                $selectedFields[] = 'p.*';
            }
        }

        $qb->select(implode(', ', $selectedFields));

        $qb->from('products', 'p');

        if ($request->query->has('includeCategory')) {
            $qb->addSelect('c.id AS category_id, c.name AS category_name');
            $qb->leftJoin('p', 'categories', 'c', 'p.category_id = c.id');
        }

        if ($request->query->has('includeUser')) {
            $qb->addSelect('u.id AS user_id, u.username AS user_username');
            $qb->leftJoin('p', 'users', 'u', 'p.user_id = u.id');
        }

        $qb->where('p.id = :pid');

        $qb->setParameters([
            ':pid' => $productId
        ]);

        $row = $qb->execute()->fetchAll()[0];

        if (!$row) {
            return new JsonResponse([
                'message' => 'Product not found.'
            ], 404);
        }

        if (in_array('p.id', $selectedFields) || $selectedFields[0] == 'p.*') {
            $product['id'] = $row['id'];
        }

        if (!$request->query->has('includeCategory')) {
            if (in_array('p.category_id', $selectedFields) || $selectedFields[0] == 'p.*') {
                $product['category_id'] = $row['category_id'];
            }
        } else {
            $product['category'] = [
                'id'    => $row['category_id'],
                'name'  => $row['category_name']
            ];
        }

        if (!$request->query->has('includeUser')) {
            if (in_array('p.user_id', $selectedFields) || $selectedFields[0] == 'p.*') {
                $product['user_id'] = $row['user_id'];
            }
        } else {
            $product['user'] = [
                'id'        => $row['user_id'],
                'username'  => $row['user_username']
            ];
        }

        if (in_array('p.name', $selectedFields) || $selectedFields[0] == 'p.*') {
            $product['name'] = $row['name'];
        }

        if (in_array('p.description', $selectedFields) || $selectedFields[0] == 'p.*') {
            $product['description'] = $row['description'];
        }

        if (in_array('p.price', $selectedFields) || $selectedFields[0] == 'p.*') {
            $product['price'] = $row['price'];
        }

        if (in_array('p.created_at', $selectedFields) || $selectedFields[0] == 'p.*') {
            $product['created_at'] = $row['created_at'];
        }

        return new JsonResponse([
            'product' => $product
        ]);
    }

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