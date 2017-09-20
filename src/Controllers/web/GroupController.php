<?php
namespace App\Controllers\web;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use GuzzleHttp\Exception\BadResponseException as GuzzleException;
use App\Models\UserGroupModel;
use App\Models\GroupModel;
use GuzzleHttp;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class GroupController extends BaseController
{
	//Get active Group
	public function index($request, $response)
	{
		$query = $request->getQueryParams();
        try {
            $result = $this->client->request('GET', 'group/list'.$request->getUri()->getQuery());
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }
        $data = json_decode($result->getBody()->getContents(), true);
		// var_dump($data);die();
		return $this->view->render($response, 'users/group-list.twig', [
			'data'			=> $data['data'],
			'pagination'	=> $data['pagination']
		]);
	}
	//Get Group user
	public function getGeneralGroup($request, $response)
	{
        try {
            $result = $this->client->request('GET', 'user/groups',[
				'query' => [
					'perpage' => 5,
					'page' => $request->getQueryParam('page')
		   ]]);
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }
        $data = json_decode($result->getBody()->getContents(), true);
		// var_dump($data);die();
		if (!isset($data['pagination'])) {
			$data['pagination'] = null;
		}
		return $this->view->render($response, 'users/group-list.twig', [
			'data'			=>	$data['data'],
			'pagination'	=>	$data['pagination']
		]);
	}
	public function enter($request, $response, $args)
	{
		$query = $request->getQueryParams();
        try {
            $result = $this->client->request('GET', 'group/'.$args['id'].'/member'.
			$request->getUri()->getQuery());
			// $result->addHeader('Authorization', '7e505da11dd87b99ba9a4ed644a20ba4');
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
        }
        $data = json_decode($result->getBody()->getContents(), true);
		return $this->view->render($response, 'pic/group-timeline.twig', [
			'members'	=> $data['data'],
			'group'	=> $args['id'],
			'pagination'	=> $data['pagination'],
		]);
	}
	//Find group by id
	public function findGroup($request, $response, $args)
	{
		try {
			$client = $this->client->request('GET',
						$this->router->pathFor('api.group.detail', ['id' => $args['id']]));
			$content = json_decode($client->getBody()->getContents());
		} catch (GuzzleException $e) {
			$content = json_decode($e->getResponse()->getBody()->getContents());
			$this->flash->addMessage('errors', 'Data tidak ditemukan');
		}
		return $this->view->render($response, 'admin/group/detail.twig', $content->reporting);
	}
	//Get create group
	public function getAdd($request, $response)
	{
		return $this->view->render($response, 'admin/group/add.twig');
	}
	//Post create group
	public function add($request, $response)
	{
		try {
            $result = $this->client->request('POST', 'group/create',
                ['form_params' => [
                    'name' 			=> $request->getParam('name'),
                    'description'	=> $request->getParam('description')
                ]
            ]);
		} catch (GuzzleException $e) {
			$result = $e->getResponse();
		}
		$content = $result->getBody()->getContents();
        $data = json_decode($content, true);
		if ($data['error'] == false) {
			$this->flash->addMessage('success', $data['message']);
			return $response->withRedirect($this->router->pathFor('group.user'));
		} else {
			$this->flash->addMessage('error', $data['message']);
			return $response->withRedirect($this->router->pathFor('group.user'));
		}
	}
	//Get edit group
	public function getUpdate($request, $response, $args)
	{
		$group = new GroupModel($this->db);
        $data['group'] = $group->find('id', $args['id']);
		return $this->view->render($response, 'admin/group/edit.twig', $data);
	}
	//Edit group
	public function update($request, $response, $args)
	{
		try {
			$client = $this->client->request('GET',
						$this->router->pathFor('api.group.update', ['id' => $args['id']]));
			$content = json_decode($client->getBody()->getContents());
		} catch (GuzzleException $e) {
			$content = json_decode($client->getResponse()->getBody()->getContents());
		}
	}
	//Set inactive/soft delete group
	public function setInactive($request, $response, $args)
	{
		try {
			$client = $this->client->request('POST',
						$this->router->pathFor('api.softdelete.group', ['id' => $args['id']]));
			$content = json_decode($client->getBody()->getContents());
            $this->flash->addMessage('success', 'Berhasil menghapus data');
		} catch (GuzzleException $e) {
			$content = json_decode($e->getResponse()->getBody()->getContents());
            $this->flash->addMessage('errors', 'Data tidak ditemukan');
		}
		return $response->withRedirect($this->router->pathFor('group.user'));
	}
	//restore
	public function restore($request, $response, $args)
	{
		try {
			$client = $this->client->request('POST',
						$this->router->pathFor('api.restore.group', ['id' => $args['id']]));
			$content = json_decode($client->getBody()->getContents());
		} catch (GuzzleException $e) {
			$content = json_decode($e->getResponse()->getBody()->getContents());
            $this->flash->addMessage('errors', 'Data tidak ditemukan');
		}
		return $response->withRedirect($this->router->pathFor('group.user'));
	}
	//Set user as member or PIC of group
	public function setUserGroup($request, $response)
	{
		// var_dump($request->getParams());die;
		try {
			$client = $this->client->request('POST',
			$this->router->pathFor('api.user.add.group'), [
				'form_params'	=> [
					'group_id' 			=>	$request->getParams()['group_id'],
					'user_id'			=>	$request->getParams()['user_id'],
					'status'			=>	0
				]
			]);
		} catch (GuzzleException $e) {
			$client = $e->getResponse();
		}

		$content = json_decode($client->getBody()->getContents(), true);

		// var_dump($content);die;

		if ($content['code'] == 201) {
			if (!empty($request->getParams()['req_id'])) {
				try {
					$result = $this->client->request('GET', 'request/delete/'.$request->getParams()['req_id']);
				} catch (GuzzleException $e) {
					$result = $e->getResponse();
				}
				$data = json_decode($result->getBody()->getContents(), true);
				// var_dump($data);die;
				$this->flash->addMessage('success', $content['message']);
				return $response->withRedirect($this->router->pathFor('notification'));
			} else {
				$this->flash->addMessage('success', $content['message']);
				return  $response->withRedirect($this->router->pathFor('pic.group.member', [
					'id' => $request->getParams()['group_id']
				]));
			}
		} else {
			$this->flash->addMessage('error', $content['message']);
			if (!empty($request->getParams()['req_id'])) {
				return  $response->withRedirect($this->router->pathFor('notification'));
			}
			return  $response->withRedirect($this->router->pathFor('pic.group.member', [
				'id' => $request->getParams()['group_id']
			]));
		}
	}

	//Get all member in group
	public function getAllGroupMember($request, $response)
	{
		try {
			$client = $this->client->request('GET','group/member/all',[
				'query' => [
					'perpage' 	=> 10,
					'page' 		=> $request->getQueryParam('page'),
					'user_id' 	=> $_SESSION['login']['id'],
					'group_id' 	=> $_SESSION['group']['id']
		   ]]);
			$content = json_decode($client->getBody()->getContents(), true);
		} catch (GuzzleException $e) {
			$content = json_decode($e->getResponse()->getBody()->getContents(), true);
		}
		if ($content['error'] == false) {
			return $this->view->render($response, 'users/group/member.twig', [
				'data'			=>	$content['data'],
				'pagination'  	=>	$content['pagination'],
				// 'group' 		=> 	$dataGroup['data']
			]);
		} else {
			$this->flash->addMessage('warning', $content['message']);
			return $response->withRedirect($this->router->pathFor('login'));
		}
	}
	//Get all user in group
	public function getGroupMember($request, $response, $args)
	{
		try {
			$client = $this->client->request('GET','group/members',[
				'query' => [
					'perpage' 	=> 10,
					'page' 		=> $request->getQueryParam('page'),
					'user_id' 	=> $_SESSION['login']['id'],
					'group_id' 	=> $args['id']
		   ]]);
			$content = json_decode($client->getBody()->getContents(), true);
		} catch (GuzzleException $e) {
			$content = json_decode($e->getResponse()->getBody()->getContents(), true);
		}
		if ($data['error'] == false) {
			return $this->view->render($response, 'users/group/member.twig', [
				'data'			=>	$content['data'],
				'pagination'  	=>	$content['pagination'],
				// 'group' 		=> 	$dataGroup['data']
			]);
		} else {
			$this->flash->addMessage('warning', $content['message']);
			return $response->withRedirect($this->router->pathFor('login'));
		}
	}
	public function getGroupPic($request, $response, $args)
	{
		try {
			$client = $this->client->request('GET','group/pics',[
				'query' => [
					'perpage' 	=> 10,
					'page' 		=> $request->getQueryParam('page'),
					'user_id' 	=> $_SESSION['login']['id'],
					'group_id' 	=> $args['id']
		   ]]);
			$content = json_decode($client->getBody()->getContents(), true);
		} catch (GuzzleException $e) {
			$content = json_decode($e->getResponse()->getBody()->getContents(), true);
		}
		// var_dump($content);die();
		if ($data['error'] == false) {
			return $this->view->render($response, 'users/group/pic.twig', [
				'data'			=>	$content['data'],
				'pagination'  	=>	$content['pagination'],
				// 'group' 		=> 	$dataGroup['data']
			]);
        } else {
            $this->flash->addMessage('warning', $content['message']);
            return $response->withRedirect($this->router->pathFor('login'));
        }
		// return $this->view->render($response, '', $content->reporting);
	}
	//Get all user in group
	public function getNotMember($request, $response, $args)
	{
		try {
			$client = $this->client->request('GET',
						$this->router->pathFor('api.getNotMember', ['id' => $args['id']]));
			$content = json_decode($client->getBody()->getContents());
		} catch (GuzzleException $e) {
			$content = json_decode($e->getResponse()->getBody()->getContents());
		}
		return $this->router->pathFor('home');
	}
	//Set user as member of group
	public function setMemberGroup($request, $response)
	{
		// var_dump($request->getParams()); die();
		try {
			$client = $this->client->request('POST',
						'group/pic/addusers', [
					'form_params' => [
						'group_id' => $request->getParam('group_id'),
						'user_id'  => $request->getParam('user_id'),
					]
			]);
			// $client = $client->getBody()->getContents();
			// $content = json_decode($client);
		} catch (GuzzleException $e) {
			$client = $e->getResponse();
			// $content = json_decode($e->getResponse()->getBody()->getContents());
		}
		$groupId = $request->getParam('group_id');
		$content = json_decode($client->getBody()->getContents(), true);
		var_dump($content); die();
		if ($content['code'] == 201) {
			$this->flash->addMessage('success', $content['message']);
			return $response->withRedirect($this->router->pathFor('pic.group.member', ['id' => $groupId]));
		} else  {
			$this->flash->addMessage('warning', $content['message']);
			return $response->withRedirect($this->router->pathFor('pic.group.member', ['id' => $groupId]));
		}
	}
	public function getGroup($request, $response)
	{
		try {
			$client = $this->client->request('GET',
						$this->router->pathFor('api.getGroup'));
			$content = json_decode($client->getBody()->getContents());
		} catch (GuzzleException $e) {
			$content = json_decode($e->getResponse()->getBody()->getContents());
		}
		return $this->view->render($response, '', $content->reporting);
	}
	// public function getPicGroup($request, $response)
	// {
	// 	try {
	// 		$client = $this->client->request('GET',
	// 					$this->router->pathFor('api.getPic'));
	// 		$content = json_decode($client->getBody()->getContents());
	// 	} catch (GuzzleException $e) {
	// 		$content = json_decode($e->getResponse()->getBody()->getContents());
	// 	}
	// 	return $this->view->render($response, '', $content->reporting);
	// }
	public function getPicGroup($request, $response)
	{
		try {
			$client = $this->client->request('GET',
						$this->router->pathFor('api.getPicGroup'));
			$client = $client->getBody()->getContents();
			$content = json_decode($client);
			var_dump($content);die();
		} catch (GuzzleException $e) {
			$content = json_decode($e->getResponse()->getBody()->getContents());
			$this->flash->addMessage('error', 'Data tidak ditemukan');
		}
		return $this->view->render($response, '', $content->reporting);
	}
	//Post create group
	public function createByUser($request, $response)
	{
		$query = $request->getQueryParams();
		try {
            $result = $this->client->request('POST', 'group/pic/create',
                ['query' => [
                    'name' 			=> $request->getParam('name'),
                    'description'	=> $request->getParam('description'),
                    'image'			=> $request->getParam('description')
                ]
            ]);
			$this->flash->addMessage('success', 'Berhasil menambah group');
		} catch (GuzzleException $e) {
			$result = $e->getResponse();
			$this->flash->addMessage('error', 'Gagal menambahkan group');
		}
		$content = $result->getBody()->getContents();
        $content = json_decode($content, true);
		// return $this->router->pathFor('group.user');
    	return $response->withRedirect($this->router->pathFor('group.user'));
	}
	//Find group by id
	public function delGroup($request, $response, $args)
	{
		try {
			$client = $this->client->request('GET',
						$this->router->pathFor('api.delGroup', ['id' => $args['id']]));
			$content = json_decode($client->getBody()->getContents());
		} catch (GuzzleException $e) {
			$content = json_decode($e->getResponse()->getBody()->getContents());
			$this->flash->addMessage(404, 'Data tidak ditemukan');
		}
		return $this->router->render($response, 'user.group', $content->reporting);
	}
	public function searchGroup($request, $response)
    {
        $query = $request->getQueryParams();
		try {
            $result = $this->client->request('POST', 'group/search',
                ['query' => [
                    'search' => $request->getParam('search'),
                ]
            ]);
		} catch (GuzzleException $e) {
			$result = $e->getResponse();
		}
        $data = json_decode($result->getBody()->getContents(), true);
        // var_dump($data['data']['']);die();
		return $this->view->render($response, 'users/found-group.twig', [
            'data' => $data['data']['groups']
        ]);
    }
    //leave group
	public function leaveGroup($request, $response, $args)
    {
    	$query = $request->getQueryParams();
    	try {
    		$result = $this->client->request('GET', 'group/'.$args['id'].'/leave'.
    			$request->getUri()->getQuery());
            $this->flash->addMessage('succes', 'Berhasil meninggalkan group');
    	} catch (GuzzleException $e) {
    		$result = $e->getResponse();
            $this->flash->addMessage('error', 'Ada kesalahan saat meninggalkan group');
    	}
		$data = json_decode($result->getBody()->getContents(), true);
    	return $response->withRedirect($this->router->pathFor('group.user'));
    }
	//Delete group
	public function delete($request, $response, $args)
	{
		try {
			$client = $this->client->request('GET',
						$this->router->pathFor('api.group.delete', ['id' => $args['id']]));
			$content = json_decode($client->getBody()->getContents());
		} catch (GuzzleException $e) {
			$content = json_decode($e->getResponse()->getBody()->getContents());
			$this->flash->addMessage(404, 'Data tidak ditemukan');
		}
		return $this->view->render($response, 'admin/group/index.twig', $content->reporting);
	}
	//Set user as member of group
	public function joinGroup($request, $response, $args)
    {
        $query = $request->getQueryParams();
        try {
            $result = $this->client->request('GET', 'group/join/'.$args['id'],
                ['query' => [
                    'group_id'  => $args['id'],
                    'user_id'   => $_SESSION['login']['id']
                ]
            ]);
            $this->flash->addMessage('success', 'Berhasil bergabung ke dalam group');
        } catch (GuzzleException $e) {
            $result = $e->getResponse();
            $this->flash->addMessage('error', 'Anda telah bergabung di dalam group');
        }
        $data = json_decode($result->getBody()->getContents(), true);
        return $response->withRedirect($this->router->pathFor('group.user'));
}
	//set As guardian
	public function setAsGuardian($request, $response, $args)
	{
		try {
			$client = $this->client->request('POST',
						$this->router->pathFor('api.user.set.guardian',
								['group' => $args['group'], 'id' => $args['id']]));
			$client = $client->getBody()->getContents();
			$content = json_decode($client);
		} catch (GuzzleException $e) {
			$client = $e->getResponse()->getBody()->getContents();
			$content = json_decode($client);
		}
		return $this->view->render($response, '', $content->reporting);
	}
	//set As member
	public function setAsMemberGroup($request, $response, $args)
	{
		$userGroup = new \App\Models\UserGroupModel($this->db);
		$user = $args['id'];
		$group = $args['group'];
		$finduserGroup = $userGroup->findTwo('user_id', $user, 'group_id', $group);
		// var_dump($finduserGroup); die();
		try {
			$client = $this->client->request('POST',
						'group/pic/set/member/'.$finduserGroup[0]['id']);
			$client = $client->getBody()->getContents();
			$content = json_decode($client, true);
		} catch (GuzzleException $e) {
			$client = $e->getResponse()->getBody()->getContents();
			$content = json_decode($client, true);
		}
		// var_dump($content); die();
		if ($content['code'] == 200 ){
			$this->flash->addMessage('success', $content['message']);
			return $response->withRedirect($this->router->pathFor('pic.group.member', ['id'=> $group]));
		} else {
			$this->flash->addMessage('warning', $content['message']);
			return $response->withRedirect($this->router->pathFor('pic.group.member', ['id'=> $group]));
		}
	}
	//set As PIC
	public function setAsPic($request, $response, $args)
	{
		try {
			$client = $this->client->request('POST',
						$this->router->pathFor('api.user.set.pic',
								['id' => $args['id'], 'group' => $args['group']]));
			$client = $client->getBody()->getContents();
			$content = json_decode($client);
		} catch (GuzzleException $e) {
			$client = $e->getResponse()->getBody()->getContents();
			$content = json_decode($client);
		}
		return $this->view->render($response, '', $content->reporting);
	}
	//delete user
	public function deleteUser($request, $response, $args)
	{
		$user = $args['id'];
		$group = $args['group'];

		try {
			$client = $this->client->request('GET',
			'group/delete/member/'.$args['id'].'/'.$args['group']);
			$client = $client->getBody()->getContents();
			$content = json_decode($client, true);
		} catch (GuzzleException $e) {
			$client = $e->getResponse()->getBody()->getContents();
			$content = json_decode($client, true);
		}
		// var_dump($content); die();
		if ($content['error'] == false) {
			$this->flash->addMessage('success', $content['message']);
			return $response->withRedirect($this->router->pathFor('pic.group.member', ['id'=> $group]));
		} else {
			$this->flash->addMessage('warning', $content['message']);
			return $response->withRedirect($this->router->pathFor('pic.group.member', ['id'=> $group]));
		}
		// return $this->view->render($response, '', $content->reporting);
	}
	//set user as PIC
	public function setAsPicGroup($request, $response, $args)
	{
		$userGroup = new \App\Models\UserGroupModel($this->db);
		// $group = $request->getParam('group');
		// $user  = $request->getParam('user');
		$group = $args['group'];
		$user = $args['id'];
		$findUser = $userGroup->findTwo('user_id', $user, 'group_id', $group);
		// var_dump($user); die();
		try {
			$client = $this->client->request('POST',
			'group/pic/set/status/'.$findUser[0]['id']);
			$client = $client->getBody()->getContents();
			$content = json_decode($client, true);
		} catch (GuzzleException $e) {
			$client = $e->getResponse()->getBody()->getContents();
			$content = json_decode($client, true);
		}
		// var_dump($content); die();
		if ($findUser[0]['status'] == 0) {
			if ($content['code'] == 200) {
				$this->flash->addMessage('success', $content['message']);
				return $response->withRedirect($this->router->pathFor('pic.group.member', ['id'=> $group]));
			} else {
				$this->flash->addMessage('warning', $content['message']);
				return $response->withRedirect($this->router->pathFor('pic.group.member', ['id'=> $group]));
			}
		} else {
			$this->flash->addMessage('warning', 'Pengguna sudah menjadi PIC');
			return $response->withRedirect($this->router->pathFor('pic.group.member', ['id'=> $group]));
		}
		// return $this->view->render($response, '', $content->reporting);
	}
	//delete user
	public function postImage($request, $response, $args)
	{
		try {
			$client = $this->client->request('POST',
						$this->router->pathFor('api.change.photo.group',
								['id' => $args['id']]));
			$client = $client->getBody()->getContents();
			$content = json_decode($client);
		} catch (GuzzleException $e) {
			$client = $e->getResponse()->getBody()->getContents();
			$content = json_decode($client);
		}
		return $this->view->render($response, '', $content->reporting);
	}
	public function enterGroup($request, $response, $args)
	{
		try {
			$result = $this->client->request('GET', 'group/enter/'.$args['id']);
			try {
				$findGroup = $this->client->request('GET', 'group/find/'. $args['id']);
			} catch (GuzzleException $e) {
				$findGroup = $e->getResponse();
			}
			$dataGroup = json_decode($findGroup->getBody()->getContents(), true);

		} catch (GuzzleException $e) {
			$result = $e->getResponse();
		}
		$content = $result->getBody()->getContents();
		$data = json_decode($content, true);
		$_SESSION['group'] = $dataGroup['data'];
		// var_dump($_SESSION['group']);die;
		if ($data['error'] == true) {
			$this->flash->addMessage('error', $data['message']);
			return $response->withRedirect($this->router->pathFor('group.user'));
		} elseif ($data['data'] == 'PIC') {
			$this->flash->addMessage('success', $data['message']);
			return $response->withRedirect($this->router->pathFor('pic.group.reported'));
		} elseif ($data['data'] == 'member') {
			$this->flash->addMessage('success', $data['message']);
			return $response->withRedirect($this->router->pathFor('unreported.item.user.group'));
		}
	}
}
