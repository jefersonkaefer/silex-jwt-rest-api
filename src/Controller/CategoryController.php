<?php

namespace Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CategoryController extends BaseController
{
    public function getCategories(Request $request)
    {
        $availableFields = [
            'id'            => 'c.id',
            'user_id'       => 'c.user_id',
            'name'          => 'c.name',
            'created_at'    => 'c.created_at'
        ];

        $availableOperations = ['eq', 'neq', 'lt', 'lte', 'gt', 'gte'];

        $qb = $this->app['db']->createQueryBuilder();

        if ($fields = $request->query->get('fields')) {
            foreach ($fields as $field) {
                if (empty($field)) {
                    return new JsonResponse([
                        'message' => "Fields[] can not be empty."
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

                $qb->addSelect($availableFields[$field]);
            }
        } else {
            if ($request->query->has('includeUser')) {
                $qb->select('c.id, c.name, c.created_at');
            } else {
                $qb->select('c.*');
            }
        }

        $qb->from('categories', 'c');

        if ($request->query->has('includeUser')) {
            $qb->addSelect('u.id AS user_id');
            $qb->addSelect('u.username');
            $qb->leftJoin('c', 'users', 'u', 'u.id = c.user_id');
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
                        $qb->expr()->eq($field, $value)
                    );
                break;
                case 'neq':
                    $qb->where(
                        $qb->expr()->neq($field, $value)
                    );
                break;
                case 'lt':
                    $qb->where(
                        $qb->expr()->lt($field, $value)
                    );
                break;
                case 'lte':
                    $qb->where(
                        $qb->expr()->lte($field, $value)
                    );
                break;
                case 'gt':
                    $qb->where(
                        $qb->expr()->gt($field, $value)
                    );
                break;
                case 'gte':
                    $qb->where(
                        $qb->expr()->gte($field, $value)
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
                        $qb->expr()->eq($field, $value)
                    );
                break;
                case 'neq':
                    $qb->orWhere(
                        $qb->expr()->neq($field, $value)
                    );
                break;
                case 'lt':
                    $qb->orWhere(
                        $qb->expr()->lt($field, $value)
                    );
                break;
                case 'lte':
                    $qb->orWhere(
                        $qb->expr()->lte($field, $value)
                    );
                break;
                case 'gt':
                    $qb->orWhere(
                        $qb->expr()->gt($field, $value)
                    );
                break;
                case 'gte':
                    $qb->orWhere(
                        $qb->expr()->gte($field, $value)
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
                        $qb->expr()->eq($field, $value)
                    );
                break;
                case 'neq':
                    $qb->andWhere(
                        $qb->expr()->neq($field, $value)
                    );
                break;
                case 'lt':
                    $qb->andWhere(
                        $qb->expr()->lt($field, $value)
                    );
                break;
                case 'lte':
                    $qb->andWhere(
                        $qb->expr()->lte($field, $value)
                    );
                break;
                case 'gt':
                    $qb->andWhere(
                        $qb->expr()->gt($field, $value)
                    );
                break;
                case 'gte':
                    $qb->andWhere(
                        $qb->expr()->gte($field, $value)
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

                $qb->addOrderBy($field, $order);
            }
        } else {
            $qb->orderBy('c.id', 'ASC');
        }

        if ($page = $request->query->get('page')) {
            if ($itemsPerPage = $request->query->get('itemsPerPage')) {
                if (!($itemsPerPage > 0 && $itemsPerPage <= 20)) {
                    $itemsPerPage = getenv('ITEMS_PER_PAGE');
                }
            } else {
                $itemsPerPage = getenv('ITEMS_PER_PAGE');
            }

            $categoriesCountQuery = clone $qb;
            $categoriesCountQuery->select('COUNT(*)');

            $categoriesCount = $this->app['db']->fetchColumn($categoriesCountQuery->getSQL());

            if ($categoriesCount > $itemsPerPage) {
                $availablePages = ceil($categoriesCount / $itemsPerPage);

                $currentPage = ($page <= $availablePages && $page > 0) ? $page : 1;

                $start = ($currentPage > 0) ? $currentPage * $itemsPerPage - $itemsPerPage : 0;

                $qb->setFirstResult($start);
                $qb->setMaxResults($itemsPerPage);

                $pagination = [
                    'items'             => $categoriesCount,
                    'availablePages'    => $availablePages,
                    'currentPage'       => $currentPage
                ];

                $pagination['links']['self'] = $this->app['url_generator']->generate('getCategories', [
                    'page'          => $currentPage,
                    'itemsPerPage'  => $itemsPerPage
                ], UrlGeneratorInterface::ABSOLUTE_URL);

                if ($currentPage != 1) {
                    $pagination['links']['first'] = $this->app['url_generator']->generate('getCategories', [], UrlGeneratorInterface::ABSOLUTE_URL);
                }

                if (($currentPage - 1) > 0) {
                    $pagination['links']['prev'] = $this->app['url_generator']->generate('getCategories', [
                        'page'          => $currentPage - 1,
                        'itemsPerPage'  => $itemsPerPage
                    ], UrlGeneratorInterface::ABSOLUTE_URL);
                }

                if (($currentPage + 1) <= $availablePages) {
                    $pagination['links']['next'] = $this->app['url_generator']->generate('getCategories', [
                        'page'          => $currentPage + 1,
                        'itemsPerPage'  => $itemsPerPage
                    ], UrlGeneratorInterface::ABSOLUTE_URL);
                }

                if ($currentPage != $availablePages) {
                    $pagination['links']['last'] = $this->app['url_generator']->generate('getCategories', [
                        'page'          => $availablePages,
                        'itemsPerPage'  => $itemsPerPage
                    ], UrlGeneratorInterface::ABSOLUTE_URL);
                }
            }
        }

        $rows = $this->app['db']->fetchAll($qb->getSQL());

        foreach ($rows as $key => $row) {
            $data[$key] = [
                'id' => $row['id']
            ];

            if (!$request->query->has('includeUser')) {
                $data[$key]['user_id'] = $row['user_id'];
            } else {
                $data[$key]['user'] = [
                    'id'        => $row['user_id'],
                    'username'  => $row['username']
                ];
            }

            $data[$key]['name'] = $row['name'];
            $data[$key]['created_at'] = $row['created_at'];
        }

        if ($pagination) $response['pagination'] = $pagination;

        $response['data'] = $data;

        return new JsonResponse($response);
    }

    public function getCategory(Request $request, $id)
    {
        $availableFields = [
            'id'            => 'c.id',
            'user_id'       => 'c.user_id',
            'name'          => 'c.name',
            'created_at'    => 'c.created_at'
        ];

        $availableOperations = ['eq', 'neq', 'lt', 'lte', 'gt', 'gte'];

//        $qb = (new Connection())->createQueryBuilder();
        $qb = $this->app['db']->createQueryBuilder();

        if ($fields = $request->query->get('fields')) {
            foreach ($fields as $field) {
                if (empty($field)) {
                    return new JsonResponse([
                        'message' => "Fields[] can not be empty."
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

                $qb->addSelect($availableFields[$field]);
            }
        } else {
            if ($request->query->has('includeUser')) {
                $qb->select('c.id, c.name, c.created_at');
            } else {
                $qb->select('c.*');
            }
        }

        $qb->from('categories', 'c');

        if ($request->query->has('includeUser')) {
            $qb->addSelect('u.id AS user_id');
            $qb->addSelect('u.username');
            $qb->leftJoin('c', 'users', 'u', 'u.id = c.user_id');
        }

        $qb->where('c.id = :cid');

        $qb->setParameters([
            ':cid' => $id
        ]);

        $row = $qb->execute()->fetchAll()[0];

        if (!$row) {
            return new JsonResponse([
                'message' => 'Category not found.'
            ], 404);
        }

        $data = [
            'id' => $row['id']
        ];

        if (!$request->query->has('includeUser')) {
            $data['user_id'] = $row['user_id'];
        } else {
            $data['user'] = [
                'id'        => $row['user_id'],
                'username'  => $row['username']
            ];
        }

        $data['name'] = $row['name'];
        $data['created_at'] = $row['created_at'];

        return new JsonResponse($data);
    }
}
