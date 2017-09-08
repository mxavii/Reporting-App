<?php

namespace App\Controllers\web;

use GuzzleHttp\Exception\BadResponseException as GuzzleException;

class CommentController extends BaseController
{

    public function getAllComment($request, $response)
    {
        try {
            $result = $this->client->request('GET', 'comment'. $request->getUri()->getQuery());
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }

        $data = json_decode($result->getBody()->getContents(), true);

        var_dump($data);
    }


    public function postComment($request, $response)
    {
        // var_dump($request->getParams());die();
        try {
            $result = $this->client->request('POST', 'comment',
                ['form_params' => [
                    'comment' => $request->getParam('comment'),
                    'item_id' => $request->getParam('item_id'),
                    'creator' => $_SESSION['login']['id']
                ]
            ]);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }
        $data = json_decode($result->getBody()->getContents(), true);

        if ($data['code'] == 201) {

                $this->flash->addMessage('success', $data['message']);
                return $response->withRedirect($this->router->pathFor('show.item',
                 ['id' => $request->getParam('item_id')]));

        } else {
            $this->flash->addMessage('warning', 'Terjadi kesalahan');
            return $response->withRedirect($this->router->pathFor('show.item',
             ['id' => $request->getParam('item_id')]));
        }
    }


    public function logout($request, $response)
    {
        if ($_SESSION['login']['status'] == 2) {
            session_destroy();
            return $response->withRedirect($this->router->pathFor('login'));

        } elseif ($_SESSION['login']['status'] == 1) {
            session_destroy();
            return $response->withRedirect($this->router->pathFor('login.admin'));
        }
    }

    public function getSignUp($request, $response)
    {
        return  $this->view->render($response, 'auth/register.twig');
    }

    public function signUp($request, $response)
    {
        $this->validator
            ->rule('required', ['username', 'password', 'email'])
            ->message('{field} tidak boleh kosong')
            ->label('Username', 'Password', 'Email');
        $this->validator->rule('email', 'email');
        $this->validator->rule('alphaNum', 'username');
        $this->validator
             ->rule('lengthMax', [
                'username',
                'password'
             ], 30);

        $this->validator
             ->rule('lengthMin', [
                'username',
                'password'
             ], 5);

        if ($this->validator->validate()) {

            try {
                $result = $this->client->request('POST', 'register',
                    ['form_params' => [
                        'username' => $request->getParam('username'),
                        'password' => $request->getParam('password'),
                        'email' => $request->getParam('email')
                    ]
                ]);
            } catch (GuzzleException $e) {
                $result = $e->getResponse();
            }

            $data = json_decode($result->getBody()->getContents(), true);

            // var_dump($data);die();

            if ($data['code'] == 201) {
                $this->flash->addMessage('success', 'Pendaftaran berhasil,
                silakan cek email anda untuk mengaktifkan akun');
                return $response->withRedirect($this->router->pathFor('signup'));
            } else {
                $_SESSION['old'] = $request->getParams();
                $this->flash->addMessage('warning', $data['message']);
                return $response->withRedirect($this->router->pathFor('signup'));
            }

        } else {
            $_SESSION['errors'] = $this->validator->errors();
            $_SESSION['old'] = $request->getParams();

            // $this->flash->addMessage('info');
            return $response->withRedirect($this->router->pathFor('signup'));
        }
    }

    public function postPicComment($request, $response)
    {
        // var_dump( $request->getParams());die();
        try {
            $result = $this->client->request('POST', 'comment',
                ['form_params' => [
                    'comment' => $request->getParam('comment'),
                    'item_id' => $request->getParam('item_id'),
                    'creator' => $_SESSION['login']['id']
                ]
            ]);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }
        $data = json_decode($result->getBody()->getContents(), true);

        if ($data['code'] == 201) {

                $this->flash->addMessage('success', $data['message']);
                return $response->withRedirect($this->router->pathFor('web.pic.show.item',
                 ['id' => $request->getParam('item_id')]));

        } else {
            $this->flash->addMessage('warning', 'Terjadi kesalahan');
            return $response->withRedirect($this->router->pathFor('web.pic.show.item',
             ['id' => $request->getParam('item_id')]));
        }
    }

}
