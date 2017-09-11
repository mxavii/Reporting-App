<?php

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AuthToken extends BaseMiddleware
{
    public function __invoke($request, $response, $next)
    {
        $token = $request->getHeader('Authorization')[0];

        $userToken = new \App\Models\Users\UserToken($this->container->db);
        $users = new \App\Models\Users\UserModel($this->container->db);

        $findUser = $userToken->find('token', $token);
        $user = $users->find('id', $findUser['user_id']);

        $now = date('Y-m-d H:i:s');
// var_dump($now);die();
        if (!$findUser) {
            $data['code'] = 401;
            $data['error'] = true;
            $data['message'] = "Anda harus login";

            return $response->withHeader('Content-type', 'application/json')->withJson($data, $data['code']);
        }

            $response = $next($request, $response);

            // Tambah Waktu Token
            $addTime['expired_date'] = date('Y-m-d H:i:s', strtotime($now. '+30 minute'));
            $userToken->update($addTime, 'user_id', $findUser['user_id']);
            return $response;
    }
}
