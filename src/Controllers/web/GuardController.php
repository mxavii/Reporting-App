<?php
namespace App\Controllers\web;


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Exception\BadResponseException as GuzzleException;
use GuzzleHttp;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class GuardController extends BaseController
{
    // Function show user by guard_id
    public function getUserByGuard(Request $request, Response $response)
    {
            $_SESSION['search'] = 1;
        try {
            $result = $this->client->request('GET',
            $this->router->pathFor('api.guard.show.user'), [
                 'query' => [
                     'perpage' => 10,
                     'page' => $request->getQueryParam('page'),
                     'id' => $_SESSION['login']['id']
 			]]);
            // $content = json_decode($result->getBody()->getContents());
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }
        $data = json_decode($result->getBody()->getContents(), true);
        // var_dump($data);die();
        return $this->view->render($response, 'users/guard/all-user.twig', [
            'data'          =>  $data['data'] ,
            'pagination'    =>  $data['pagination']
        ]);    // return $this->view->render($response, 'guard/show-user.twig', $content->reporting);
    }

    // Function Delete Guardian
    public function deleteGuardian(Request $request, Response $response, $args)
    {
        try {
            $result = $this->client->request('DELETE', 'guard/delete/'.$args['id']);
            $content = json_decode($result->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            $content = json_decode($e->getResponse()->getBody()->getContents(), true);
        }
        // var_dump($content);
        if ($content['code'] == 200) {
            $this->flash->addMessage('success', $content['message']);
            return $response->withRedirect($this->router->pathFor('user.view.profile'));
        }else {
            $this->flash->addMessage('warning', $content['message']);
            return $response->withRedirect($this->router->pathFor('user.view.profile'));
        }
    }

    // Function Create Guardian
    public function createGuardian(Request $request, Response $response, $args)
    {
        try {
            $result = $this->client->request('POST', 'guard/create',
                ['form_params' => [
                    'user_id' 	=> $request->getParam('guard_id'),
                    'guard_id'	=> $request->getParam('user_id')
                ]
            ]);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }
        $data = json_decode($result->getBody()->getContents(), true);
        //  var_dump($data); die();
        // $search = $_SESSION['search'];
        if ($data['code'] == 201 ) {
            if (!empty($user= $request->getParam('req_id'))) {
                try {
                    $result = $this->client->request('DELETE', 'request/delete/'.$request->getParam('req_id'));
                } catch (GuzzleException $e) {
                    $result = $e->getResponse();
                }
            }
            $this->flash->addMessage('success', $data['message']);
        } else {
            $this->flash->addMessage('warning', $data['message']);
        }
        if (!empty($user= $request->getParam('req_id'))) {
            return $response->withRedirect($this->router->pathFor('notification'));
        } else {
            $base = $request->getUri()->getBaseUrl();
            $search = $request->getParam('search');
            return $response->withRedirect($base.'/pic/search/user/guard?search='.$search);
        }
    }


    // Function show guard by user_id
    public function showGuardByUser(Request $request,Response $response, $args)
    {
        $id = $_SESSION['login']['id'];
// var_dump($id);die();
        $query = $request->getQueryParams();
        try {
            $result = $this->client->request('GET', $this->router->pathFor('api.guard.show'));
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }
        $data = json_decode($result->getBody()->getContents(), true);
            var_dump($data);die();
    }

    // Function get user
    public function getUser(Request $request, Response $response, $args)
    {
        $guard = new \App\Models\GuardModel($this->db);
        $id = $_SESSION['login']['id'];
        // $guards = $guard->find('guard_id', $id);
        // $findGuard = $guard->findGuards('guard_id', $args['id'], $id);
        // var_dump($guards);die();
        try {
           //  $client = $this->client->request('GET','guard/user',[
           //      'query' => [
           //          'perpage'   => 10,
           //          'page'      => $request->getQueryParam('page'),
           //          'user_id'   => $_SESSION['login']['id']
           // ]]);
            $result = $this->client->request('GET', 'guard/user'. $request->getUri()->getQuery());
        } catch (GuzzleException $e) {
            // $content = json_decode($e->getResponse()->getBody()->getContents(), true);
            $result = $e->getResponse();
        }
        $data = json_decode($result->getBody()->getContents(), true);
        // print_r($data);die();
        // var_dump($data['data']);die();
        // return $this->view->render($response, 'users/guard/all-user.twig', [
        //     'data'          =>  $content['data'],
        //     'pagination'    =>  $content['pagination'],
        //     'guard'         =>  $content['data'],
       if (!isset($data['pagination'])) {
            $data['pagination'] = null;
        }
        return $this->view->render($response, 'users/guard/all-user.twig', [
            'data'          =>  $data['data'] ,
            'pagination'    =>  $data['pagination']
        ]);
    }

    public function getSearch($request, $response, $args)
    {
        $_SESSION['search'] = $args['search'];
        if ($args['search'] == 1){
            return $this->view->render($response,'users/guard/search-user.twig');
        } else {
            return $this->view->render($response,'users/fellow/search-guard.twig');
        }
    }

    public function searchUser($request, $response, $args)
    {
        // var_dump(    $_SESSION['search']);die;
        $user = new \App\Models\Users\UserModel($this->db);
        $searchParam = $request->getParam('search');
        $_SESSION['search_param'] = $searchParam;
        $search = $_SESSION['search'];
        $userId = $_SESSION['login']['id'];
        $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');
        $perpage = $request->getQueryParam('perpage');
        $result = $user->search($searchParam, $userId)->setPaginate($page, 8);
        // var_dump($result); die();
        $data['users']      = $result['data'];
        $data['count']      = count($data['users']);
        $data['pagination'] = $result['pagination'];
        $data['search']     = $_SESSION['search_param'];
        // var_dump($search); die();
        if ($search == 1) {
            // $data['guard'] = $_SESSION['guard'];
            return $this->view->render($response, 'users/guard/search-user.twig', $data);
        }else {
            // $data['guard'] = $_SESSION['login']['id'];
            return $this->view->render($response, 'users/fellow/search-guard.twig', $data);
        }
    }

    public function deleteUser(Request $request, Response $response, $args)
    {
        try {
            $result = $this->client->request('DELETE', 'guard/delete/user/'.$args['id']);
            $content = json_decode($result->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            $content = json_decode($e->getResponse()->getBody()->getContents(), true);
        }
            // var_dump($content);
        if ($content['code'] == 200) {
            $this->flash->addMessage('success', $content['message']);
            return $response->withRedirect($this->router->pathFor('guard.show.user'));
        }else {
            $this->flash->addMessage('warning', $content['message']);
            return $response->withRedirect($this->router->pathFor('guard.show.user'));
        }
    }
}
