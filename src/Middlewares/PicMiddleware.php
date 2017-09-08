<?php

namespace App\Middlewares;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class PicMiddleware extends BaseMiddleware
{
    public function __invoke($request, $response, $next)
    {
        $token = $request->getHeader('Authorization')[0];

        $userToken = new \App\Models\Users\UserToken($this->container->db);
        $userGroup = new \App\Models\UserGroupModel($this->container->db);
        $users = new \App\Models\Users\UserModel($this->container->db);

        $findToken = $userToken->find('token', $token);
        $findUser = $users->find('id', $findToken['user_id']);
        $findPic = $userGroup->find('user_id', $findToken['user_id']);

        if (!$findUser || $findUser['status'] == 1 || $findPic['status'] != 1 ) {
            $data['status'] = 401;
            $data['message'] = "You Are Not PIC";

            return $response->withHeader('Content-type', 'application/json')->withJson($data, $data['status']);
        }

            $response = $next($request, $response);

            return $response;
    }
}
