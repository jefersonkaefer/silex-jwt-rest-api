<?php

namespace Controller;

use Firebase\JWT\JWT;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints as Assert;

class AuthenticationController extends BaseController
{
    public function signin(Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        $errors = $this->app['validator']->validate([
            'username' => $username,
            'password' => $password
        ], new Assert\Collection([
            'username' => [
                new Assert\NotBlank(),
                new Assert\Length([
                    'min' => 3,
                    'max' => 32
                ])
            ],
            'password' => [
                new Assert\NotBlank(),
                new Assert\Length([
                    'min' => 4,
                    'max' => 4096
                ])
            ],
        ]));

        if (count($errors) > 0) {
            return new JsonResponse([
                'message' => 'Bad Request'
            ], 400);
        } else {
            $user = $this->app['db']->fetchAssoc('SELECT id, username, password FROM users WHERE username = ?', [$username]);

            if (!$user) {
                return new JsonResponse([
                    'message' => 'Invalid Credentials'
                ], 401);
            } else {
                if (!password_verify($password, $user['password'])) {
                    return new JsonResponse([
                        'message' => 'Invalid Credentials'
                    ], 401);
                } else {
                    return new JsonResponse([
                        'token' => JWT::encode([
                            'exp'       => time() + 900,
                            'user_id'   => $user['id'],
                            'username'  => $user['username']
                        ], getenv('JWT_KEY'), getenv('JWT_ALGORITHM'))
                    ]);
                }
            }
        }
    }
}