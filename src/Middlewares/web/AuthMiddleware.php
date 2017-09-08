<?php

namespace App\Middlewares\web;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class AuthMiddleware extends BaseMiddleware
{
    public function __invoke($request, $response, $next)
    {
        if ($_SESSION['login']) {
            $_SESSION['back'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $response = $next($request, $response);

            return $response;
        } else {
            $this->container->flash->addMessage('warning', 'Anda harus login untuk mengakses halaman ini!');

            return $response->withRedirect($this->container->router->pathFor('login'));
        }
    }
}
