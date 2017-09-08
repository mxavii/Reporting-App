<?php
namespace App\Controllers\api;


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\GuardModel;
use App\Models\Users\UserModel;

class GuardController extends BaseController
{

    public function getAll(Request $request, Response $response)
    {
        $guard = new GuardModel($this->db);
        $userToken = new \App\Models\Users\UserToken($this->db);
        $token = $request->getHeader('Authorization')[0];
        $userId = $userToken->getUserId($token);

        $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');
        $perPage = $request->getQueryParam('perpage');
        $getGuard = $guard->findAllGuard();
        $result = $this->paginateArray($getGuard, $page, $perPage);

        if ($getGuard) {
            $data = $this->responseDetail(200, false, 'Data tersedia', $result);
        } else {
            $data = $this->responseDetail(200, false, 'Data kosong');
        }
        return $data;
    }

    // Function Create Guardian
    public function createGuardian(Request $request, Response $response, $args)
    {
        $guard = new \App\Models\GuardModel($this->db);
        // $userToken = new \App\Models\Users\UserToken($this->db);
        // $userId = $userToken->getUserId($token);
        $userId = $request->getParam('guard_id');
        $guardId = $request->getParam('user_id');
        $findGuard = $guard->findTwo('guard_id', $guardId, 'user_id', $userId);

        $data = [
            'guard_id'  => $guardId,
            'user_id'   => $userId
        ];

        if (empty($findGuard[0])) {
            $addGuardian = $guard->add($data);

            return $this->responseDetail(201, false, 'Pengguna berhasil ditambahkan', [
                'data' => $data
            ]);
        } else {
            return $this->responseDetail(400, true, 'Pengguna sudah ditambahkan');
        }
    }

    // Function Delete Guardian
   public function deleteGuardian(Request $request, Response $response, $args)
   {
       $guard = new GuardModel($this->db);
       $userToken = new \App\Models\Users\UserToken($this->db);
       $token = $request->getHeader('Authorization')[0];
       $user  = $userToken->getUserId($token);
       // $findUser = $userToken->find('token', '72af357cae642386ccaaf5c4e86b669a');
       $findGuard = $guard->findGuards('user_id', $user, 'guard_id', $args['id']);
    //    $findUser  = $guard->findGuards('user_id', $args['id'], 'guard_id', $user);
          // var_dump($findGuard);die();
       // $query = $request->getQueryParams();
          if ($findGuard) {
              $guard->hardDelete($findGuard['id']);
              $data = $this->responseDetail(200, false, 'Anda berhasil menghapus salah satu wali anda');
          }else {
              $data = $this->responseDetail(404, true, 'Data tidak ditemukan');
          }
          return $data;
   }

    // Function show user by guard_id
    public function getUserByGuard(Request $request, Response $response, $args)
    {
        $guards = new GuardModel($this->db);

        $token = $request->getHeader('Authorization')[0];
        $user = $guards->getUserByToken($token);
        $guardId = $request->getQueryParam('id');
        $findGuard = $guards->find('guard_id', $guardId);

        if ($findGuard || $user) {
            $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');
            $perPage = $request->getQueryParam('perpage');
            $findAll = $guards->findAllUser($guardId)->setPaginate($page, $perPage);

            return $this->responseDetail(200, false, 'Berhasil menampilkan user', [
                'data'          =>  $findAll['data'],
                'pagination'    =>  $findAll['pagination']
            ]);
        } else {
            return $this->responseDetail(404, true, 'User tidak ditemukan');
        }
    }

    // Function show guard by user_id
    public function getGuardByUser(Request $request, Response $response, $args)
    {
        $guard = new GuardModel($this->db);
        // $userToken = new \App\Models\Users\UserToken($this->container->db);

        // $token = $request->getHeader('Authorization')[0];
        // $userId = $userToken->find('token', '90c4a9cebeaae6515c7dd4d265271bf6');
        // $userId = $userToken->getUserId($token);
        $userGuard = $guard->findGuardbyUser($args['id']);
// var_dump($userGuard);die();
        // $query = $request->getQueryParams();
        if ($userGuard) {
            return  $this->responseDetail(200, false, 'Berhasil menampilkan data', [
                'data'    =>  $userGuard
                // 'pagination'      =>  $userGuard['pagination'],
            ]);
        } else {
            return  $this->responseDetail(400, true, 'Gagal menampilkan data');
        }
        return $data;
      }

    // Function get user by guard login
    public function getUser(Request $request, Response $response, $args)
    {
        $guard = new GuardModel($this->db);
        $userToken = new \App\Models\Users\UserToken($this->container->db);

        $token = $request->getHeader('Authorization')[0];
        $userId = $userToken->getUserId($token);
        // $query = $request->getQueryParams();
         if ($userId) {
             $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');
             $perPage = $request->getQueryParam('perpage');
                $userGuard = $guard->findAllUser($userId)->setPaginate($page, $perPage);
                // var_dump($userGuard);die();
             $data = $this->responseDetail(200, false, 'Berhasil menampilkan user', [
                    'data'    =>  $userGuard['data'],
                    'pagination'      =>  $userGuard['pagination'],
                ]);
        } else {
            $data = $this->responseDetail(400, true, 'Gagal menampilkan user');
        }
        return $data;
    }

    public function getSearch($request, $response)
    {
        // var_dump($_SESSION['search']); die();
        if ($_SESSION['search'] == 1){
            return $this->view->render($response,'users/guard/search-user.twig');
        } else {
            return $this->view->render($response,'users/fellow/search-guard.twig');
        }
    }

    public function searchUser($request, $response, $args)
    {
        $user = new \App\Models\Users\UserModel($this->db);
        $searchParam = $request->getParam('search');
        $_SESSION['search_param'] = $searchParam;
        $search = $_SESSION['search'];
        $userId = $_SESSION['login']['id'];
        $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');
        $perpage = $request->getQueryParam('perpage');
        $result = $user->search($searchParam, $userId)->setPaginate($page, 8);
        // var_dump($result); die();
        $data['users'] = $result['data'];
        $data['count']    = count($data['users']);
        $data['pagination'] = $result['pagination'];
        $data['search'] = $_SESSION['search_param'];
        // var_dump($search); die();
        if ($search == 1) {
            $data['guard'] = $_SESSION['guard'];
            return $this->view->render($response, 'users/guard/search-user.twig', $data);
        }else {
            $data['guard'] = $_SESSION['login']['id'];
            return $this->view->render($response, 'users/fellow/search-guard.twig', $data);
        }
    }

    // Function show guardian by user_id
    public function getUserGuard(Request $request, Response $response)
    {
        $_SESSION['search'] = 2;
        try {
            $result = $this->client->request('GET',
            $this->router->pathFor('api.guard.show'), [
                 'query' => [
                     'perpage' => 10,
                     'page' => $request->getQueryParam('page'),
                    //  'id' => $_SESSION['login']['id']
 			]]);
            // $content = json_decode($result->getBody()->getContents());
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }
        $data = json_decode($result->getBody()->getContents(), true);
        // var_dump($_SESSION['search']);die();
        return $this->view->render($response, 'users/fellow/all-guard.twig', [
            'data'          =>  $data['data'] ,
            'pagination'    =>  $data['pagination']
        ]);    // return $this->view->render($response, 'guard/show-user.twig', $content->reporting);
    }

    public function deleteUser(Request $request, Response $response, $args)
    {
        $guard = new GuardModel($this->db);
        $userToken = new \App\Models\Users\UserToken($this->db);
        $token = $request->getHeader('Authorization')[0];
        $user  = $userToken->getUserId($token);
        // $findGuard = $guard->findGuards('user_id', $user, 'guard_id', $args['id']);
        $findUser  = $guard->findGuards('user_id', $args['id'], 'guard_id', $user);
           // var_dump($findGuard);die();
        // $query = $request->getQueryParams();
           if ($findUser) {
               $guard->hardDelete($findUser['id']);
               $data = $this->responseDetail(200, false, 'Anda berhasil menghapus salah satu anak anda');
           }else {
               $data = $this->responseDetail(404, true, 'Data tidak ditemukan');
           }
           return $data;
    }

}
