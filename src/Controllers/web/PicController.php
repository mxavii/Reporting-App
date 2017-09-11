<?php

namespace App\Controllers\web;

use GuzzleHttp\Exception\BadResponseException as GuzzleException;

class PicController extends BaseController
{

    public function getMemberGroup($request, $response, $args)
	{
		// $query = $request->getQueryParams();
        // $userGroup = new \App\Models\UserGroupModel($this->db);
        try {
            $result = $this->client->request('GET', 'group/'.$args['id'].'/member', [
                'query' => [
                    'perpage' => 9,
                    'page'    => $request->getQueryParam('page')
                ]
            ]);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }

        $data = json_decode($result->getBody()->getContents(), true);
        $count = count($data['data']);
        // $findUser =
		// var_dump($data); die();
        if ($data['error'] == false) {
            return $this->view->render($response, 'pic/group-member.twig', [
                'members'	=> $data['data'],
                'group'	    => $args['id'],
                'pagination'=> $data['pagination'],
            ]);
        } else {
            $this->flash->addMessage('warning', $data['message']);
            return $response->withRedirect($this->router->pathFor('group.user'));

        }
        // var_dump($data->reporting->results);die();
    }

    public function getUnreportedItem($request, $response, $args)
    {
        // $userGroup = new \App\Models\UserGroupModel($this->db);
        // $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');

        try {
            $result = $this->client->request('GET', 'item/group/'. $args['id'], [
                'query' => [
                    'page'    => $request->getQueryparam('page'),
                    'perpage' => 10,
                    ]
                ]);

                try {
                    $result2 = $this->client->request('GET', 'group/'.$args['id'].'/member', [
                        'query' => [
                            'perpage' => 9,
                            'page'    => $request->getQueryParam('page')
                        ]
                    ]);
                } catch (GuzzleException $e) {
                    $result2 = $e->getResponse();
                }

                $data2 = json_decode($result2->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }

        $data = json_decode($result->getBody()->getContents(), true);

        // var_dump($data2);
        // echo "<br />";
        // var_dump($data); die();
        return $this->view->render($response, 'pic/tugas.twig', [
            'items'	=> $data['data'],
            'group'	=> $args['id'],
            'member'	=> $data2['data'],
            'pagination'	=> $data['pagination'],
        ]);
    }

    public function getReportedItem($request, $response, $args)
    {
        try {
            $result = $this->client->request('GET', 'item/group/'. $args['id'].'/all-reported', [
                'query' => [
                    'page'    => $request->getQueryparam('page'),
                    'perpage' => 5,
                    ]
                ]);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }

        $data = json_decode($result->getBody()->getContents(), true);
        // var_dump($data); die();
        return $this->view->render($response, 'pic/laporan.twig', [
            'items'	=> $data['data'],
            'group'	=> $args['id'],
            'pagination'	=> $data['pagination'],
        ]);
    }

    public function deleteTugas($request, $response, $args)
	{
        $item = new \App\Models\Item($this->db);
        $findItem = $item->find('id', $args['id']);
		try {
			$client = $this->client->request('DELETE', 'item/'.$args['id']);

			$content = json_decode($client->getBody()->getContents(), true);
            $this->flash->addMessage('success', 'Tugas telah berhasil dihapus');
		} catch (GuzzleException $e) {
			$content = json_decode($e->getResponse()->getBody()->getContents(), true );
			$this->flash->addMessage('warning', 'Anda tidak diizinkan menghapus tugas ini ');
		}
		// return $this->view->render($response, 'pic/tugas.twig');
        // var_dump($content); die();
        return $response->withRedirect($this->router->pathFor('pic.item.group',['id' => $findItem['group_id']]));
	}

    public function createItem($request, $response)
    {
        if (empty($request->getParam('user_id'))) {
            $user_id = null;
        } else {
            $user_id = $request->getParam('user_id');
        }

        $group = $request->getParam('group');
        try {
            $result = $this->client->request('POST', 'item/create', [
                'form_params' => [
                    'name'          => $request->getParam('name'),
                    'description'   => $request->getParam('description'),
                    'recurrent'     => $request->getParam('recurrent'),
                    'start_date'    => $request->getParam('start_date'),
                    'user_id'    	=> $user_id,
                    'group_id'      => $request->getParam('group'),
                    'creator'    	=> $_SESSION['login']['id'],
                    'privacy'       => $request->getParam('privacy'),
                ]
            ]);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }

        $content = $result->getBody()->getContents();
        $contents = json_decode($content, true);
        // var_dump($contents); die();
        if ($contents['code'] == 201) {
            $this->flash->addMessage('success', $contents['message']);
            return $response->withRedirect($this->router->pathFor('pic.item.group',['id' => $group ]));
        } else {
            // foreach ($contents['message'] as $value ) {
            // }
            $_SESSION['errors'] = $contents['message'];
            $_SESSION['old']    = $request->getParams();
            // var_dump($_SESSION['errors']); die();
            return $response->withRedirect($this->router->pathFor('pic.item.group',['id' => $group ]));
        }


    }

    public function showItem($request, $response, $args)
    {
        $user = new \App\Models\Users\UserModel($this->db);
        // $id = $_SESSION['login']['id'];
        try {
            $result = $this->client->request('GET', 'item/show/'.$args['id'].'?'
            . $request->getUri()->getQuery());
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }

        $data = json_decode($result->getBody()->getContents(), true);

        try {
            $comment = $this->client->request('GET', 'item/comment/'.$args['id'].'?'
            . $request->getUri()->getQuery());
        } catch (GuzzleException $e) {
            $comment = $e->getResponse();
        }

        $allComment = json_decode($comment->getBody()->getContents(), true);
// var_dump($allComment);die;
        $userId = $data['data']['user_id'];
        $findUser = $user->find('id', $userId);
        // var_dump($data['data']);die();

        if ($data['data']) {
            return $this->view->render($response, 'pic/show-item-tugas.twig', [
                'items' => $data['data'],
                'comment' => $allComment['data'],
                'user'    => $findUser['username'],
            ]);
        } else {
            $this->flash->addMessage('error', $data['message']);
            return $response->withRedirect($this->router->pathFor('home'));

        }

    }

    public function getSearchUser($request, $response, $args)
    {
        $user = new \App\Models\Users\UserModel($this->db);
        $findUser = $user->find('id', $args['id']);
        $userId['id']   = $findUser['id'];
        $_SESSION['guard'] = $userId['id'];
        return $this->view->render($response, 'pic/search-user.twig', $userId);

    }

    public function searchUser($request, $response, $args)
    {
        // var_dump($request->getQueryParams());die;
        $user = new \App\Models\Users\UserModel($this->db);

        $_SESSION['search'] = $request->getQueryParam('search');
        // $userId = $_SESSION['login']['id'];
        $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');
        // $perpage = $request->getQueryParam('perpage');
        $result = $user->search($_SESSION['search'], $_SESSION['login']['id'])->setPaginate($page, 8);


        // $data['guard']      = $_SESSION['guard'];
        $data['users']      = $result['data'];
        $data['count']      = count($data['users']);
        $data['pagination'] = $result['pagination'];
        $data['search']     = $_SESSION['search'];
        $data['group_id']   = $request->getQueryParam('group_id');
        // var_dump($data); die();
        if (!empty($_SESSION['search'])) {

            return $this->view->render($response, 'pic/search-user.twig', $data);
        }

    }

    public function deleteGroupRequest($request, $response, $args)
	{
        $item = new \App\Models\Item($this->db);
        $findItem = $item->find('id', $args['id']);
		try {
			$client = $this->client->request('DELETE', 'request/'.$args['id']);

			$content = json_decode($client->getBody()->getContents(), true);
            $this->flash->addMessage('success', 'Tugas telah berhasil dihapus');
		} catch (GuzzleException $e) {
			$content = json_decode($e->getResponse()->getBody()->getContents(), true );
			$this->flash->addMessage('warning', 'Anda tidak diizinkan menghapus tugas ini ');
		}
		// return $this->view->render($response, 'pic/tugas.twig');
        // var_dump($content); die();
        return $response->withRedirect($this->router->pathFor('pic.item.group',['id' => $findItem['group_id']]));
	}

    public function getSearch($request, $response, $args)
    {
        // var_dump($args['group']);die;
        return  $this->view->render($response, 'pic/search-user.twig', [
            'group_id' => $args['group']
        ]);
    }

    public function deleteComment($request, $response, $args)
    {
        try {
            $result = $this->client->request('GET', 'comment/delete/'. $args['id']);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }

        $data = json_decode($result->getBody()->getContents(), true);
        // var_dump($data); die();

        if ($data['error'] == false) {
            $this->flash->addMessage('success', $data['message']);
            return $response->withRedirect($this->router->pathFor('web.pic.show.item',['id' => $data['data']['item_id']]));
        } else {
            $this->flash->addMessage('warning', $data['message']);
            return $response->withRedirect($this->router->pathFor('web.pic.show.item',['id' => $data['data']['item_id']]));
        }
    }

}
