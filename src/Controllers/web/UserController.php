<?php

namespace App\Controllers\web;

use GuzzleHttp\Exception\BadResponseException as GuzzleException;

class UserController extends BaseController
{

    public function getAllUser($request, $response)
    {
        try {
            $result = $this->client->request('GET', 'user'. $request->getUri()->getQuery());
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }

        $data = json_decode($result->getBody()->getContents(), true);

        // var_dump($data); die();
    }

    public function getLogin($request, $response)
    {
        return  $this->view->render($response, 'auth/login.twig');
    }

     public function login($request, $response)
    {
        // var_dump($request->getParams());die();
        try {
            $result = $this->client->request('POST', 'login',
                ['form_params' => [
                    'username' => $request->getParam('username'),
                    'password' => $request->getParam('password')
                ]
            ]);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }
        $data = json_decode($result->getBody()->getContents(), true);

        if ($data['code'] == 200) {
            $_SESSION['key'] = $data['key'];
            $_SESSION['login'] = $data['data'];
            if (!empty($request->getParams()['guard'])) {
                $_SESSION['guard'] = $_SESSION['login']['id'];
            }
            if ($_SESSION['login']['status'] == 2) {
                $_SESSION['user_group'] = $groups;
                $this->flash->addMessage('succes', 'Selamat datang, '. $_SESSION['login']['username']);
                return $response->withRedirect($this->router->pathFor('home'));
            } else {
                $this->flash->addMessage('warning',
                'Anda belum terdaftar sebagai user atau akun anda belum diverifikasi');
                return $response->withRedirect($this->router->pathFor('login'));
            }
        } else {
            $this->flash->addMessage('warning', 'Username atau password tidak cocok');
            return $response->withRedirect($this->router->pathFor('login'));
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

    public function searchUser($request, $response)
    {
        $user = new \App\Models\Users\UserModel($this->db);

        $search = $request->getParam('search');

        $userId = $_SESSION['login']['id'];
        $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');
        $perpage = $request->getQueryParam('perpage');
        $result = $user->search($search, $userId)->setPaginate($page, 8);
        // $page = $result['pagination']['current_page'];
        // $perpage = $result['pagination']['perpage'];

        // var_dump($result); die();

        $data['group'] = $request->getParam('group');
        // $data['users']    = $this->paginateArray($result['data'], $page, $perpage
        $data['users'] = $result['data'];
        $data['count']    = count($data['users']);
        $data['pagination'] = $result['pagination'];
        $data['search'] = $search;
        // var_dump($data['users']); die();
        if (!empty($data['group'])) {
            return $this->view->render($response, 'pic/search-result.twig', $data);
        }

    }

    public function viewProfile($request, $response)
    {
        $_SESSION['search'] = 2;
        try {
            $result = $this->client->request('GET', 'user/detail'. $request->getUri()->getQuery());
            try {
                $guard = $this->client->request('GET', 'guard/show/'. $_SESSION['login']['id']);
            } catch (GuzzleException $e) {
                $guard = $e->getResponse();
            }
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }

        $data = json_decode($result->getBody()->getContents(), true);
        $dataGuard = json_decode($guard->getBody()->getContents(), true);
// var_dump($dataGuard['data']);die();
        return $this->view->render($response, 'users/view-profile.twig', [
            'user'  => $data['data'],
            'guard' => $dataGuard['data']
            ]);
    }

    public function settingProfile($request, $response, $args)
    {
         try {
            $result = $this->client->request('GET', 'user/detail'. $request->getUri()->getQuery());
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }

        $data = json_decode($result->getBody()->getContents(), true);
        return $this->view->render($response, 'users/setting-profile.twig', $data);
    }

    public function updateProfile($request, $response, $args)
    {
        // var_dump($args['id']);die;
        // $this->validator
        //     ->rule('required', ['name', 'email', 'address', 'phone', 'gender'])
        //     ->message('{field} tidak boleh kosong')
        //     ->label('Nama', 'Email', 'Alamat', 'Nomor Telepon', 'Jenis kelamin');
        // $this->validator->rule('email', 'email');
        // $this->validator->rule('alphaNum', 'username');
        // if ($this->validator->validate()) {
        $id = $_SESSION['login']['id'];

        try {
            $result = $this->client->request('POST', 'user/update/'.$id,
            ['form_params' => [
                'name'      => $request->getParam('name'),
                'username'  => $request->getParam('username'),
                'email'     => $request->getParam('email'),
                'address'   => $request->getParam('address'),
                'phone'     => $request->getParam('phone'),
                'gender'    => $request->getParam('gender')
            ]
        ]);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }

        $data = json_decode($result->getBody()->getContents(), true);
        // var_dump($data);die();
        if ($data['error'] == false) {
            $this->flash->addMessage('success', 'Info akun berhasil dipebarui');
            return $response->withRedirect($this->router->pathFor('user.setting.profile'));
            $_SESSION['login'] = $data['data'];
        } else {
            $_SESSION['old'] = $request->getParams();
            $this->flash->addMessage('error', $data['message']);
            return $response->withRedirect($this->router->pathFor('user.setting.profile'));
        }
    }

    public function changeImage($request, $response)
    {
        // var_dump($_FILES);die();
        $path = $_FILES['image']['tmp_name'];
        $mime = $_FILES['image']['type'];
        $name  = $_FILES['image']['name'];
        $id = $request->getParam('id');

        try {
            $result = $this->client->request('POST', 'user/'.$id.'/change-image', [
                'multipart' => [
                    [
                        'name'     => 'image',
                        'filename' => $name,
                        'Mime-Type'=> $mime,
                        'contents' => fopen( $path, 'r' )
                    ]
                ]
            ]);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }

        try {
            $user = $this->client->request('GET', 'user/'.$id);
        } catch (GuzzleException $e) {
            $user = $e->getResponse();
        }

        $data = json_decode($result->getBody()->getContents(), true);
        $newUser = json_decode($user->getBody()->getContents(), true);

        // var_dump($newUser);die();
        if ($data['error'] == false) {
            $this->flash->addMessage('success', 'Foto profil berhasil diubah');
            return $response->withRedirect($this->router->pathFor('user.view.profile'));
            $_SESSION['login'] = $newUser['data'];
        } else {
            $this->flash->addMessage('warning', $data['message']);
            return $response->withRedirect($this->router->pathFor('user.view.profile'));
        }
    }

    public function getChangePassword($request, $response)
    {
         return $this->view->render($response, 'users/change-password.twig');
    }

    public function postChangePassword($request, $response, $args)
    {
        $password1 = $request->getParam('new_password');
        $password2 = $request->getParam('confirm_password');
        // var_dump( $request->getParams());die;
        if ($password1 != $password2) {
            $this->flash->addMessage('warning', 'Konfirmasi password baru tidak cocok');
            return $response->withRedirect($this->router->pathFor('change.password'));
        }

        try {
            $result = $this->client->request('POST', 'user/password/change',
                ['form_params' => [
                    'password' => $request->getParam('password'),
                    'new_password' => $request->getParam('new_password')
                ]
            ]);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }

        $data = json_decode($result->getBody()->getContents(), true);
// var_dump($data);die;
        if ($data['error'] == false) {
            $this->flash->addMessage('success', $data['message']);
            return $response->withRedirect($this->router->pathFor('change.password'));
        } else {
            $this->flash->addMessage('error', $data['message']);
            return $response->withRedirect($this->router->pathFor('change.password'));
        }
    }

    public function forgotPassword($request, $response, $args)
    {
        try {
            $result = $this->client->request('POST', 'reset',
                ['form_params' => [
                    'email' => $request->getParam('email')
                ]
            ]);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }

        $data = json_decode($result->getBody()->getContents(), true);
        // var_dump($data);die;
        if ($data['error'] == false) {
            $this->flash->addMessage('success', $data['message']);
            return $response->withRedirect($this->router->pathFor('login'));
        } else {
            $this->flash->addMessage('error', $data['message']);
            return $response->withRedirect($this->router->pathFor('login'));
        }
    }

    public function resetPassword($request, $response, $args)
    {
        try {
            $result = $this->client->request('POST', 'password/reset',
                ['form_params' => [
                    'token' => $request->getParam('token'),
                    'email' => $request->getParam('email'),
                    'password' => $request->getParam('password'),
                    'password2' => $request->getParam('password2')
                ]
            ]);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }

        $data = json_decode($result->getBody()->getContents(), true);
        // var_dump($data);die;
        if ($data['error'] == false) {
            $this->flash->addMessage('success', $data['message']);
            return $response->withRedirect($this->router->pathFor('login'));
        } else {
            $_SESSION['errors'] = $data['message'];
            $_SESSION['old'] = $request->getParams();
            return  $this->view->render($response, 'auth/reset-password.twig', [
                'token' =>  $request->getParam('token')
            ]);

        }
    }

    public function getResetPassword($request, $response, $args)
    {
        try {
            $result = $this->client->request('GET', 'password/reset/'.$args['token']);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }

        $data = json_decode($result->getBody()->getContents(), true);
        // var_dump($data);die;
        if ($data['error'] == false) {
            $this->flash->addMessage('success', $data['message']);
            return  $this->view->render($response, 'auth/reset-password.twig',[
                'token' => $args['token']
            ]);
        } else {
            $this->flash->addMessage('error', $data['message']);
            return $response->withRedirect($this->router->pathFor('login'));
        }
    }


}
