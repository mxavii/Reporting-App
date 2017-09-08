<?php

namespace App\Middlewares\web;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class GuardMiddleware extends BaseMiddleware
{
    public function __invoke($request, $response, $next)
    {
        if ($_SESSION['guard']['status']== 'guard') {

            $response = $next($request, $response);

            return $response;
        } else {
            $this->container->flash->addMessage('warning', 'You aren\'t authorized to access this page!');

            return $response->withRedirect($this->container->router->pathFor('home'));
        }
    }
}
