<?php

namespace App\Controllers\web;

use GuzzleHttp\Exception\BadResponseException as GuzzleException;


class AdminController extends BaseController
{
    public function index($request, $response)
    {
        return $this->view->render($response, 'admin/admin.twig');
    }
    public function getLoginAsAdmin($request, $response)
    {
        return $this->view->render($response, 'admin/login.twig');
    }

    public function login($request, $response)
    {
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
            if ($_SESSION['login']['status'] == 1) {
                $_SESSION['user_group'] = $groups;
                $this->flash->addMessage('success', 'Selamat datang, '. $login['username']);
                return $response->withRedirect($this->router->pathFor('register'));
            } else {
                $this->flash->addMessage('warning',
                'Anda belum terdaftar sebagai user atau akun anda belum diverifikasi');
                return $response->withRedirect($this->router->pathFor('login.admin'));
            }
        } else {
            $this->flash->addMessage('warning', 'Username atau password tidak cocok');
            return $response->withRedirect($this->router->pathFor('login.admin'));
        }
    }

    public function userList($request, $response)
    {
        $id = $_SESSION['login']['id'];
// var_dump($id);die();
        $query = $request->getQueryParams();
        try {
            $result = $this->client->request('GET', $this->router->pathFor('api.user.list'), [
                    'query' => [
                    'perpage' => 10,
                    'page' => $request->getQueryParam('page')
                    ]
                ]);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }
        $data = json_decode($result->getBody()->getContents(), true);

        return $this->view->render($response, 'admin/data/user-list.twig', [
            'data'          =>  $data['data'],
            // 'count_item'    =>  count($data['data']),
            'pagination'     =>  $data['pagination'],
        ]);
    }

    public function groupList($request, $response)
    {
        $query = $request->getQueryParams();
        try {
            $result = $this->client->request('GET', $this->router->pathFor('api.group.list'), [
                    'query' => [
                    'perpage' => 10,
                    'page' => $request->getQueryParam('page')
                    ]
                ]);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }
        $data = json_decode($result->getBody()->getContents(), true);
        // var_dump($data);die();
        return $this->view->render($response, 'admin/data/group/group-list.twig', [
            'data'          =>  $data['data'],
            // 'count_item'    =>  count($data['data']),
            'pagination'     =>  $data['pagination'],
        ]);
    }

    public function guardList($request, $response)
    {
        try {
            $result = $this->client->request('GET', $this->router->pathFor('api.guard'), [
                    'query' => [
                        'perpage'   => 10,
                        'page'      => $request->getQueryParam('page')
                    ]
                ]);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }
        $data = json_decode($result->getBody()->getContents(), true);

        return $this->view->render($response, 'admin/data/guard-list.twig', [
            'data'          =>  $data['data'],
            // 'count_item'    =>  count($data['data']),
            'pagination'     =>  $data['pagination'],
        ]);
    }

    // public function getAllUserGroup($request, $response)
    // {
    //     $query = $request->getQueryParams();
    //     try {
    //         $result = $this->client->request('GET', $this->router->pathFor('api.group.all.user').['id' => $args['id']], [
    //                 'query' => [
    //                 'perpage' => 5,
    //                 'page' => $request->getQueryParam('page')
    //                 ]
    //             ]);
    //     } catch (GuzzleException $e) {
    //         $result = $e->getResponse();
    //     }
    //     $data = json_decode($result->getBody()->getContents(), true);
    //     // var_dump($data);die();
    //     return $this->view->render($response, 'admin/data/guard-list.twig', [
    //         'data'          =>  $data['data'],
    //         // 'count_item'    =>  count($data['data']),
    //         'pagination'     =>  $data['pagination'],
    //     ]);
    // }
    public function getAllItem($request, $response)
    {
        $query = $request->getQueryParams();
        try {
            $result = $this->client->request('GET', $this->router->pathFor('api.item.all'), [
                    'query' => [
                    'perpage' => 10,
                    'page' => $request->getQueryParam('page')
                    ]
                ]);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }
        $data = json_decode($result->getBody()->getContents(), true);
        // var_dump($data);die();
        return $this->view->render($response, 'admin/data/item-list.twig', [
            'data'          =>  $data['data'],
            // 'count_item'    =>  count($data['data']),
            'pagination'     =>  $data['pagination'],
        ]);
    }

    public function getAllUserInGroup($request, $response, $args)
    {
       try {
            $client = $this->client->request('GET','group/member/all',[
                'query' => [
                    'perpage'   => 5,
                    'page'      => $request->getQueryParam('page'),
                    'user_id'   => $_SESSION['login']['id'],
                    'group_id'  => $args['id']
           ]]);
            $data = json_decode($client->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            $data = json_decode($e->getResponse()->getBody()->getContents(), true);
        }

           return $this->view->render($response, 'admin/data/group/group-member.twig', [
            'data'          =>  $data['data'],
            // 'count_item'    =>  count($data['data']),
            'pagination'     =>  $data['pagination'],
        ]);
    }

    public function getAddMember($request, $response)
    {
        $id = $_SESSION['login']['id'];
// var_dump($id);die();
        $query = $request->getQueryParams();
        try {
            $result = $this->client->request('GET', $this->router->pathFor('api.user.list'), [
                    'query' => [
                    'perpage' => 10,
                    'page' => $request->getQueryParam('page')
                    ]
                ]);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }
        $data = json_decode($result->getBody()->getContents(), true);

         return $this->view->render($response, 'admin/data/group/add-member.twig', [
            'data'          =>  $data['data'],
            // 'count_item'    =>  count($data['data']),
            'pagination'     =>  $data['pagination'],
        ]);
    }
}
