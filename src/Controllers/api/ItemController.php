<?php
namespace App\Controllers\api;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\Item as Item;
use App\Models\UserItem;

class ItemController extends BaseController
{
    //Get all items
    public function all($request, $response)
    {
        $item = new Item($this->db);
        $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');
        $perPage = $request->getParsedBody()['perpage'];
        $getItems = $item->getAllItem()->setPaginate($page,5);
        $countItems = count($getItems);
        $query = $request->getQueryParams();

        if ($getItems['data']) {
                $data = $this->responseDetail(200, false, 'Data tersedia', [
                    'data'        => $getItems['data'],
                    'pagination'  => $getItems['pagination']
                ]);

        } else {
            $data = $data = $this->responseDetail(200, false, 'Data kosong');
        }

        return $data;
    }

    //Get item  by  id
    public function getItemDetail($request, $response, $args)
    {
        $item = new Item($this->db);

        $findItem = $item->find('id', $args['id']);

        if ($findItem) {
            $data = $this->responseDetail(200, false, 'Data Tersedia', [
                'data' => $findItem
            ]);
        } else {
            $data = $this->responseDetail(200, false, 'Item tidak ditemukan');
        }

        return $data;
    }

    //Get group item unreported
    public function getUnreportedGroupItem($request, $response, $args)
    {
        $item     = new Item($this->db);

        $groupId    = $args['group'];
        $page = !$request->getQueryParam('page') ?  1 : $request->getQueryParam('page');
        $perpage = $request->getQueryParam('perpage');
        if (empty($perpage))
        {
            $perpage = 5;
        }
        $findItem   = $item->getUnreportedGroupItem($groupId)->setPaginate($page, $perpage);
        // $countItem  = count($findItem);
        $query      = $request->getQueryParams();
        if ($findItem['data']) {
            $data = $this->responseDetail(200, false, 'Data tersedia', [
                'data'          => $findItem['data'],
                'pagination'    => $findItem['pagination']
            ]);

        } else {
            $data = $this->responseDetail(200, false, 'Data kosong');

        }

        return $data;
    }

    //Get group item reported
    public function getReportedGroupItem($request, $response, $args)
    {
        $item     = new Item($this->db);

        $groupId    = $args['group'];
        $page = !$request->getQueryParam('page') ?  1 : $request->getQueryParam('page');
        $perpage = $request->getQueryParam('perpage');
        $findItem   = $item->reportedGroupItem($groupId);
        if (empty($perpage))
        {
            $perpage = 5;
        }
        $result = $this->paginateArray($findItem, $page, $perpage);
        $countItem  = count($findItem);
        $query      = $request->getQueryParams();
        if ($findItem) {
            $data = $this->responseDetail(200, false, 'Data tersedia', [
                'data'         => $result['data'],
                'pagination'   => $result['pagination']
            ]);

        } else {
            $data = $this->responseDetail(200, false, 'Data kosong');
        }

        return $data;
    }
    //Get user item (unreported)
    public function getUnreportedUserItem($request, $response, $args)
    {
        $item     = new Item($this->db);
        $page = !$request->getQueryParam('page') ?  1 : $request->getQueryParam('page');
        $findItem   = $item->getGroupItem($args['user']);
            // ->setPaginate($page,5);
        $countItem  = count($findItem);
        $query      = $request->getQueryParams();

        // var_dump($findItem); die();
        if ($findItem) {
            $data = $this->responseDetail(200, false, 'Data Tersedia', [
                'data'  => $findItem,

            ]);
        } else {
            $data = $this->responseDetail(200, false, 'Data kosong');

        }

        return $data;
    }

    //get all user item (reported)
    public function getReportedUserItem($request, $response, $args)
    {
        $item       = new Item($this->db);
        $groupId    = $args['group'];
        $page = !$request->getQueryParam('page') ?  1 : $request->getQueryParam('page');
        $perPage = $request->getQueryParam('perpage');
        $findItem   = $item->getItem('user_id',
            $args['user'], 'status', 1)->setPaginate($page, $perPage);
        $countItem  = count($findItem);

        // var_dump($findItem); die();
        if ($findItem['data']) {
            $data = $this->responseDetail(200, false, 'Data Tersedia',[
                'data'  => $findItem['data'],
                'pagination' => $findItem['pagination']
         ]);
        } else {
            $data = $this->responseDetail(200, false, 'Data kosong');

        }

        return $data;
    }

    //Get unreported item user in group
    public function getUnreportedUserGroupItem($request, $response, $args)
    {
        $item = new Item($this->db);

        $token = $request->getHeader('Authorization')[0];
        $groupId = $request->getQueryParam('group_id');
        $userId = $request->getQueryParam('user_id');
        $page = !$request->getQueryParam('page') ?  1 : $request->getQueryParam('page');
        $perPage = $request->getQueryParam('perpage');
        $findItem  = $item->getUnreportedUserItemInGroup($userId, $groupId);
        $result = $this->paginateArray($findItem, $page, $perPage);
// var_dump($result);die;
        if ($findItem) {
            return $this->responseDetail(200, false, 'Data tersedia', [
                'data'          => $result['data'],
                'pagination'    => $result['pagination']
            ]);
        } else {
            return $this->responseDetail(200, false, 'Data kosong');
        }
    }

    //Get reported item user in group
    public function getReportedUserGroupItem($request, $response, $args)
    {
        $item     = new Item($this->db);

        $token = $request->getHeader('Authorization')[0];
        $groupId = $request->getParam('group_id');
        $userId = $request->getParam('user_id');
        $page = !$request->getParam('page') ?  1 : $request->getQueryParam('page');
        $perPage = $request->getParam('perpage');
        $findItem   = $item->getReportedUserItemInGroup($userId, $groupId);
        $result = $this->paginateArray($findItem, $page, $perPage);

        if ($findItem) {
            return $this->responseDetail(200, false, 'Data tersedia', [
                'data'          => $result['data'],
                'pagination'    => $result['pagination']
            ]);
        } else {
            return $this->responseDetail(200, false, 'Data kosong');
        }
    }

    //Create item
    public function createItem($request, $response)
    {
        // var_dump($request->getparams());die;
        $rules = [
            'required' => [
                ['name'],
                ['description'],
                ['start_date'],
                ['group_id'],
                ['creator'],
                ['privacy'],
            ],
        ];

        $this->validator->rules($rules);

        $this->validator->labels([
            'name'        => 'Nama',
            'recurrent'   => 'Perulangan',
            'description' => 'Deskipsi',
            'start_date'  => 'Tanggal mulai',
            'group_id'    => 'Id Grup',
            'creator '    => 'Creator',
            'privacy'     => 'Privasi'
        ]);
        // var_dump($this->validator);
        // die();

        if ($this->validator->validate()) {

            $data = [
                "name"          => $request->getParam('name'),
                "description"   => $request->getParam('description'),
                "recurrent"     => $request->getParam('recurrent'),
                "start_date"    => $request->getParam('start_date'),
                "user_id"       => $request->getParam('user_id'),
                "group_id"      => $request->getParam('group_id'),
                "privacy"       => $request->getParam('privacy'),
                "creator"       => $request->getParam('creator'),
                "status"        => 0,
                "reported_at"   => null,
            ];
            $item = new Item($this->db);

            $newItem = $item->create($data);
            $recentItem = $item->find('id', $newItem);

            if (!empty($_FILES['image']['name'])) {
                $imageItem = new \App\Models\ImageItem($this->db);
                $storage = new \Upload\Storage\FileSystem('assets/images');
                $image = new \Upload\File('image', $storage);
                $image->setName(uniqid());
                $image->addValidations(array(
                    new \Upload\Validation\Mimetype(array('image/png', 'image/gif',
                    'image/jpg', 'image/jpeg')),
                    new \Upload\Validation\Size('1M')
                ));

                $image->upload();
                $imgName = $image->getNameWithExtension();

                $dataImage = [
                    'image'   => $imgName,
                    'item_id' => $newItem
                ];
                $imageItem->createData($dataImage);
            }

            return $this->responseDetail(201, false, 'Item baru telah berhasil ditambahkan', [
                'data' => $recentItem

            ]);

        } else {

            return $this->responseDetail(400, true, $this->validator->errors());
        }
    }

    //Create item
    public function createItemUser($request, $response, $args)
    {
        // $userGroup = new \App\Models\UserGroupModel($this->db);
        $item = new Item($this->db);
        $token = $request->getHeader('Authorization')[0];
        $user = $item->getUserByToken($token);

        $rules = [
            'required' => [
                ['name'],
                ['start_date'],
                // ['public'],
                ['group_id'],
                ['creator'],
                ['privacy'],
            ],
        ];

        $this->validator->rules($rules);
        $this->validator->labels([
            'name'        => 'Name',
            'recurrent'   => 'Recurrent',
            'description' => 'Description',
            'start_date'  => 'Start date',
            'user_id'     => 'User id',
            'group_id'    => 'Group id',
            'creator '    => 'Creator',
            'privacy'     => 'Privacy'
        ]);
        // var_dump($this->validator);
        // die();

        if ($this->validator->validate()) {

            $data = [
                'name'          => $request->getParams()['name'],
                'description'   => $request->getParams()['description'],
                'recurrent'     => $request->getParams()['recurrent'],
                'start_date'    => $request->getParams()['start_date'],
                'user_id'       => $request->getParams()['user_id'],
                'group_id'      => $request->getParams()['group_id'],
                // 'image'         => $request->getParams()['image'],
                'privacy'       => $request->getParams()['privacy'],
                'creator'       => $user['id'],
                'status'        => 0,
                'reported_at'   => null,
            ];
            $newItem = $item->create($data);
            $recentItem = $item->find('id', $newItem);

            return $this->responseDetail(201, false, 'Item baru berhasil ditambahkan', [
                'data' => $recentItem
            ]);

        } else {

            return $this->responseDetail(401, true, $this->validator->errors());
        }
    }

    //Edit item
    public function updateItem( $request, $response, $args)
    {
        $item     = new Item($this->db);
        $findItem = $item->find('id', $args['id']);

        if ($findItem) {
            $rules = [
                'required' => [
                    ['name'],
                    ['recurrent'],
                    ['description'],
                    ['start_date'],
                    ['group_id'],
                    // ['user_id'],
                    ['privacy'],
                ],

            ];

            $this->validator->rules($rules);

            $this->validator->labels([
                'name'        => 'Name',
                'recurrent'   => 'Recurrent',
                'description' => 'Description',
                'start_date'  => 'Start date',
                'user_id'     => 'User id',
                'privacy'     => 'Privacy'
            ]);


            if ($this->validator->validate()) {
                // $item = new \App\Models\Item($this->db);
                $updateItem = $item->update($request->getParsedBody(), $args['id']);
                $recentItemUpdated = $item->find('id', $args['id']);

                $data = $this->responseDetail(200, false, 'Item berhasil diperbarui', [
                    'data' => $recentItemUpdated,
                ]);

            } else {

                $data = $this->responseDetail(400, true, $this->validator->errors());
            }
        } else {
            $data = $this->responseDetail(404, true, 'Item tidak ditemukan');
        }

        return $data;
    }

    //Delete item by Admin/PIC
    public function deleteItem( $request, $response, $args)
    {
        $item = new Item($this->db);
        $userGroup = new \App\Models\UserGroupModel($this->db);

        $findItem = $item->find('id', $args['id']);
        $token = $request->getHeader('Authorization')[0];
        $user = $item->getUserByToken($token);
        $userId = $user['id'];
        $userStatus = $user['status'];
        $groupId  = $findItem['group_id'];

        $checkPic = $userGroup->findTwo('user_id', $userId, 'group_id', $groupId);
        if (!empty($checkPic)) {
        $pic = $checkPic[0]['status'];
        }
        if ($findItem) {
            if ($userStatus == 1 || $pic == 1) {
                $item->hardDelete($args['id']);
                $data = $this->responseDetail(200, false, 'Item telah dihapus');

            } else {
                $data = $this->responseDetail(401, true, 'Anda tidak berhak menghapus item ini');
            }
        } else {
            $data = $this->responseDetail(404, true, 'Item tidak ditemukan');
        }

        return $data;

    }

    //Delete item by User
    public function deleteItemByUser( $request, $response, $args)
    {
        $item = new Item($this->db);
        $userToken = new \App\Models\Users\UserToken($this->db);

        $findItem = $item->find('id', $args['item']);
        $token = $request->getHeader('Authorization')[0];
        $user = $userToken->getUserId($token);

        $userIdItem = $findItem['user_id'];
            if ($findItem) {
                if ($userIdItem == $user) {
                $item->hardDelete($args['item']);
                $data = $this->responseDetail(200, false, 'Item telah dihapus');
            } else {
                $data = $this->responseDetail(401, true, 'Anda tidak berhak menghapus item ini');
            }

        } else {
            $data = $this->responseDetail(404, true, 'Item tidak ditemukan');
        }

        return $data;
    }

    //Delete item reported
    public function deleteItemReported( $request, $response, $args)
    {
        $item = new Item($this->db);
        $userToken = new \App\Models\Users\UserToken($this->db);

        $findItem = $item->find('id', $args['item']);
        $token = $request->getHeader('Authorization')[0];
        $user = $userToken->getUserId($token);

        $userIdItem = $findItem['user_id'];
            if ($findItem) {
                if ($userIdItem == $user) {
                $item->hardDelete($args['item']);
                $data = $this->responseDetail(200, false, 'Item telah dihapus');
            } else {
                $data = $this->responseDetail(401, true, 'Anda tidak berhak menghapus item ini');
            }

        } else {
            $data = $this->responseDetail(404, true, 'Item tidak ditemukan');
        }

        return $data;
    }
//
//     public function reportItem($request, $response, $args)
//     {
//         $item = new Item($this->db);
//         $mailer = new \App\Extensions\Mailers\Mailer();
//         $users = new \App\Models\Users\UserModel($this->db);
//         $reportedItem = new \App\Models\ReportedItem($this->db);
//         $guards  = new \App\Models\GuardModel($this->db);
//         $userGroups = new \App\Models\UserGroupModel($this->db);
// // die(kk);
//         $token = $request->getHeader('Authorization')[0];
//         // var_dump($token);die();
//         $user  = $item->getUserByToken($token);
//         $userId = $user['id'];
//         $userName = $user['name'];
//         $guardian = $guards->find('user_id', $userId);
//         $guard = $users->find('id', $guardian['guard_id']);
//         $findItem = $item->find('id', $args['item']);
//         $picGroup = $userGroups->finds('group_id', $findItem['group_id'], 'status', 1);
//         if (!empty($picGroup)) {
//             $pic = $users->find('id', $picGroup[0]['user_id']);
//         }
//
//         // var_dump($findItem); die();
//         if ($findItem) {
//             $rules = [
//                 'required' => [
//                     ['description']
//                 ],
//             ];
//
//             $this->validator->rules($rules);
//             $this->validator->labels([
//                 'description' => 'Description',
//                 'public'      => 'Public',
//             ]);
//             // var_dump($this->validator); die();
//
//             if ($this->validator->validate()) {
//                 $date = date('Y-m-d H:i:s');
//                 $dataNewItem = [
//                     'name'        => $findItem['name'],
//                     'recurrent'   => $findItem['recurrent'],
//                     'description' => $request->getParams()['description'],
//                     'start_date'  => $findItem['start_date'],
//                     'group_id'    => $findItem['group_id'],
//                     'creator'     => $findItem['creator'],
//                     'reported_at' => $date,
//                     'privacy'      => $findItem['privacy'],
//                     'status'      => 1,
//                     'user_id'     => $userId,
//                 ];
//                 $updateData = [
//                     'description' => $request->getParsedBody()['description'],
//                     'status'      => 1,
//                     'reported_at' => $date
//                 ];
//
//                 if ($findItem['creator'] == $findItem['user_id']) {
//                     $item->updateData($updateData, $args['item']);
//                     $result = $findItem;
//
//
//                 } else {
//                     $newItem = $item->create($dataNewItem);
//                     $result= $item->find('id', $newItem);
//                     $reportedItem->createData([
//                         'user_id' => $userId,
//                         'item_id' => $newItem
//                     ]);
//                 }
//
//                 $date = date('d M Y H:i:s');
//                 $content = $userName.' telah melaporkan '.$findItem['name'].' pada '.$date;
//
//                 if ($guard) {
//                     $dataGuard = [
//                         'subject' => $userName.' laporan item',
//                         'from'    =>'reportingmit@gmail.com',
//                         'to'      => $guard['email'],
//                         'sender'  => 'Reporting App',
//                         'receiver'=> $guard['name'],
//                         'content' => $content,
//                     ];
//
//                     $mailer->send($dataGuard);
//                 }
//
//                 if ($pic && $pic['id'] != $guard['id']) {
//                     $dataPic = [
//                         'subject' => $userName.'laporan item',
//                         'from'    =>'reportingmit@gmail.com',
//                         'to'      => $pic['email'],
//                         'sender'  => 'Reporting App',
//                         'receiver'=> $pic['name'],
//                         'content' => $content,
//                     ];
//
//                     $mailer->send($dataPic);
//                 }
//
//                 $data = $this->responseDetail(200, false, 'Item telah berhasil dilaporkan',
//                 [
//                     'data' => $result
//                 ]);
//
//             } else {
//
//                 $data = $this->responseDetail(400, true, $this->validator->errors());
//             }
//
//         } else {
//             $data = $this->responseDetail(404, true, 'Item tidak ditemukan ');
//         }
//
//         return $data;
//
//     }

    public function postImages($request, $response, $args)
    {
        $item = new Item($this->db);
        $imageItem = new \App\Models\ImageItem($this->db);

        $findItem = $item->find('id', $args['item']);
        $imageUploaded = $request->getUploadedFiles();
        $cnt = count($imageUploaded);
        // var_dump($imageUploaded); die();
        if ($findItem) {
            if (!empty($imageUploaded)) {
                $storage = new \Upload\Storage\FileSystem('assets/images');
                $image = new \Upload\File('image' , $storage);
                if (count($image)>1) {
                    $base = $request->getUri()->getBaseUrl();
                    $validate = $image->addValidations(array(
                        new \Upload\Validation\Mimetype(['image/png', 'image/gif',
                         'image/jpg', 'image/jpeg']),
                        new \Upload\Validation\Size('1M')));
                    for ($i = 0; $i < count($image); $i++) {
                        $image[$i]->setName(uniqid('img-'.date('Ymd'). '-'));
                        $data = array(
                            'name'       => $image[$i]->getNameWithExtension(),
                            'extension'  => $image[$i]->getExtension(),
                            'mime'       => $image[$i]->getMimetype(),
                            'size'       => $image[$i]->getSize(),
                            'md5'        => $image[$i]->getMd5(),
                            'dimensions' => $image[$i]->getDimensions()
                        );
                        $imageName = $base. '/assets/images/' .$data['name'];
                        $datas = [
                            'image'   => $imageName,
                            'item_id' => $args['item']
                        ];
                        $imageItem->create($datas);
                    }
                    if ($validate->isValid()) {
                        $image->upload();
                        $uploaded = $imageItem->findAllImage($args['item']);
                        return   $this->responseDetail(200, false, 'Foto berhasil diunggah', [
                            'data' => $uploaded
                        ]);
                    } else {
                        foreach ($validate->getErrors() as $value) {
                            $val = $value;
                        }
                        return   $this->responseDetail(400, true, $val);
                    }

                } else {

                    $validate = $image->addValidations(array(
                        new \Upload\Validation\Mimetype(['image/png', 'image/gif', 'image/jpg', 'image/jpeg']),
                        new \Upload\Validation\Size('1M')));
                    $data = array(
                        'name'       => $image->getNameWithExtension(),
                        'extension'  => $image->getExtension(),
                        'mime'       => $image->getMimetype(),
                        'size'       => $image->getSize(),
                        'md5'        => $image->getMd5(),
                        'dimensions' => $image->getDimensions()
                    );
                    $image->setName(uniqid('img-'.date('Ymd'). '-'));
                    $base = $request->getUri()->getBaseUrl();
                    $imageName = $base. '/assets/images/' .$data['name'];


                    if ($validate->isValid()) {
                        $image->upload();
                        $datas = [
                            'image'   => $imageName,
                            'item_id' => $args['item']
                        ];
                        $imageItem->create($datas);
                        $uploaded = $imageItem->findAllImage($args['item']);
                        return   $this->responseDetail(200, 'Foto berhasil diunggah', ['result' => $uploaded,
                         'query'  => null,
                         'meta'   => null,
                        ]);


                    } else {
                        foreach ($validate->getErrors() as $value) {
                            $val = $value;
                        }
                        return   $this->responseDetail(400, true, $val);

                    }
                }

             } else {
                return $this->responseDetail(400, true, 'File foto belum dipilih');
            }
        } else {
            return $this->responseDetail(404, true, 'Item tidak ditemukan');

        }
    }

    public function getImageItem($request, $response, $args)
    {
        $item = new item($this->db);
        $imageItem = new \App\Models\ImageItem($this->db);
        $findImageItem = $imageItem->find('item_id', $args['item']);

        if ($findImageItem){
            $result = $imageItem->findAllImage($args['item']);

            $data = $this->responseDetail(200, false, 'Data tersedia', [
                'data' => $result,
            ]);

        } else {
            $data = $this->responseDetail(200, false, 'Data tidak ditemukan');
        }

        return $data;

    }

    public function deleteImageItem($request, $response, $args)
    {
        $item = new item($this->db);
        $imageItem = new \App\Models\ImageItem($this->db);
        $findImageItem = $imageItem->find('id', $args['image']);

        if ($findImageItem){
            $result = $imageItem->hardDelete($args['image']);

            $data = $this->responseDetail(200, false, 'Gambar telah dihapus');

        } else {
            $data = $this->responseDetail(404, true, 'Data tidak ditemukan');
        }

        return $data;

    }

    public function userTimeline($request, $response, $args)
    {
        $items = new Item($this->db);

        $findItem = $items->getAllGroupItem($args['id']);
        $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');
        $perPage = $request->getQueryParam('perpage');

        $newItem = array();
        if ($findItem){
            foreach ($findItem as $item) {
                if (!empty($newItem[$item['id']])) {
                    $currentValue1 = (array) $newItem[$item['id']]['image'];
                    $currentValue2 = (array) $newItem[$item['id']]['comment'];
                    $newItem[$item['id']]['image'] =
                     array_unique(array_merge($currentValue1, (array) $item['image']));
                    $newItem[$item['id']]['comment'] =
                     array_unique(array_merge($currentValue2, (array) $item['comment']));
                } else {
                    $newItem[$item['id']] = $item;
                }
            }
            if (empty($perpage)) {
                $perpage = 5;
            }
            $result = $this->paginateArray($newItem, $page, $perpage);
            $data = $this->responseDetail(200, false, 'Data tersedia', [
                'data'        => $result['data'],
                'pagination'  => $result['pagination']
            ]);

        } else {
            $data = $this->responseDetail(404, true, 'Data tidak ditemukan');
        }

        return $data;
    }

    public function guardTimeline($request, $response, $args)
    {
        $items = new Item($this->db);

        $findItem = $items->getUserGuardItem($args['id']);
        $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');
        $perPage = $request->getQueryParam('perpage');

        $newItem = array();
        if ($findItem){
            foreach ($findItem as $item) {
                if (!empty($newItem[$item['id']])) {
                    $currentValue1 = (array) $newItem[$item['id']]['image'];
                    $currentValue2 = (array) $newItem[$item['id']]['comment'];
                    $newItem[$item['id']]['image'] =
                     array_unique(array_merge($currentValue1, (array) $item['image']));
                    $newItem[$item['id']]['comment'] =
                     array_unique(array_merge($currentValue2, (array) $item['comment']));
                } else {
                    $newItem[$item['id']] = $item;
                }
            }

            $result = $this->paginateArray($newItem, $page, $perPage);
            // var_dump($result);die;
            $data = $this->responseDetail(200, false, 'Data tersedia', [
                'data'        => $result['data'],
                'pagination'  => $result['pagination']
            ]);

        } else {
            $data = $this->responseDetail(404, true, 'Data tidak ditemukan');
        }

        return $data;
    }

    public function showItemDetail($request, $response, $args)
    {
        $items = new \App\Models\Item($this->db);

        $findItem = $items->find('id', $args['id']);

        if ($findItem){
            if ($findItem['reported_at'] == null) {
                $itemDetails = $items->getUnreportedItemDetail($args['id']);

            }else {
                $itemDetails = $items->getReportedItemDetail($args['id']);
            }
// var_dump();die();
            $newItem = array();
            foreach ($itemDetails as $item) {
                if (!empty($newItem[$item['id']])) {
                    $currentValue = (array) $newItem[$item['id']]['comment'];
                    $currentValue1 = (array) $newItem[$item['id']]['image'];
                    $newItem[$item['id']]['comment'] =
                     array_unique(array_merge($currentValue, (array) $item['comment']));
                    $newItem[$item['id']]['image'] =
                     array_unique(array_merge($currentValue1, (array) $item['image']));
                } else {
                    $newItem[$item['id']] = $item;
                }
            }
            // $result = array_values($newItem);
            $data = $this->responseDetail(200, false, 'Data tersedia', [
                'data'        => array_values($newItem)[0]
            ]);
        } else {
            $data = $this->responseDetail(404, true, 'Data tidak ditemukan');
        }

        return $data;
        //  return $this->view->render($response, 'users/show-item.twig', ['items' => $findItem]);
     }


     public function getReportedByMonth($request, $response, $args)
     {
         $item = new Item($this->db);
        //   var_dump($request->getQueryParams());die();

         $month = $request->getQueryParam('month');
         $year = $request->getQueryParam('year');
         $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');
         $perPage = $request->getQueryParam('perpage');
         $findItem = $item->getByMonth($month, $year, $args['user']);
         $result = $this->paginateArray($findItem, $page, $perPage);

        if ($findItem) {
            $data = $this->responseDetail(200, false, 'Data Tersedia', [
                'data'  => $result['data'],
                'pagination'  => $result['pagination']

            ]);
        } else {
            $data = $this->responseDetail(200, false, 'Data kosong');

        }
        return $data;
     }

     public function getReportedByYear($request, $response, $args)
     {
         $item = new Item($this->db);

         $year = $request->getQueryParam('year');
         $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');
         $perPage = $request->getQueryParam('perpage');
         $findItem = $item->getByYear($year, $args['user']);
         $result = $this->paginateArray($findItem, $page, $perPage);
        //  var_dump($result);die();

        if ($findItem) {
            $data = $this->responseDetail(200, false, 'Data Tersedia', [
                'data'  => $result['data'],
                'pagination'  => $result['pagination']

            ]);
        } else {
            $data = $this->responseDetail(200, false, 'Data kosong');
        }
        return $data;
     }

     public function reportItem($request, $response)
     {
         $items = new Item($this->db);
         $mailer = new \App\Extensions\Mailers\Mailer();
         $guards = new \App\Models\GuardModel($this->db);
         $imageItem = new \App\Models\ImageItem($this->db);
         $users = new \App\Models\Users\UserModel($this->db);
         $itemDone = new \App\Models\ReportedItem($this->db);
         $userGroups = new \App\Models\UserGroupModel($this->db);
// var_dump($request->getHeader('Authorization'));die;
         //  $itemId = $request->getParams()['item_id'];
        //  var_dump($request-> $request->getParsedBody()('item'));die;
         $token = $request->getHeader('Authorization')[0];
         $reporter = $items->getUserByToken($token);
         $itemId =  $request->getParsedBody()['item'];
         $userId =  $request->getParsedBody()['user'];
         $user = $users->find('id', $userId);
         $item = $items->find('id', $itemId);
         $pic = $users->find('id', $item['creator']);
         $guardian = $guards->findTwo('user_id', $userId, 'user_id', $userId);
         $member = $userGroups->findTwo('user_id', $userId, 'group_id', $item['group_id']);
         $date = date('Y-m-d H:i:s');

         if (!$item) {
            return $this->responseDetail(404, true, 'Item tidak ditemukan ');
         }

         if ($reporter['status'] == 1 || $member[0]['status'] == 1 || $item['user_id'] == $reporter['id']
         || ($item['user_id'] == null & $member[0])) {
             $data = [
                 'description'	=>	 $request->getParsedBody()['description'],
                 'reported_at'	=>	$date,
                 'user_id'	    =>	$userId,
                 'status'		=>	1,
             ];

             if ($item['user_id'] == null) {

                 $newItem = $items->create($item);
                 $items->updateData($data, $newItem);

                 $itemDone->createData([
                 'item_id' 	=>	$newItem,
                 'user_id'	=>	$userId,
                 ]);

                 if (!empty($_FILES['image']['name'])) {
                     $storage = new \Upload\Storage\FileSystem('assets/images');
                     $image = new \Upload\File('image', $storage);
                     $image->setName(uniqid());
                     $image->addValidations(array(
                         new \Upload\Validation\Mimetype(array('image/png', 'image/gif',
                         'image/jpg', 'image/jpeg')),
                         new \Upload\Validation\Size('1M')
                     ));

                     $image->upload();
                     $imgName = $image->getNameWithExtension();

                     $dataImage = [
                         'image'   => $imgName,
                         'item_id' => $newItem
                     ];
                     $imageItem->createData($dataImage);
                 }
             } else {
                 $addItems = $items->updateData($data, $itemId);
                 if (!empty($_FILES['image']['name'])) {
                     $storage = new \Upload\Storage\FileSystem('assets/images');
                     $image = new \Upload\File('image', $storage);
                     $image->setName(uniqid());
                     $image->addValidations(array(
                         new \Upload\Validation\Mimetype(array('image/png', 'image/gif',
                         'image/jpg', 'image/jpeg')),
                         new \Upload\Validation\Size('1M')
                     ));

                     $image->upload();
                     $imgName = $image->getNameWithExtension();

                     $dataImage = [
                         'image'   => $imgName,
                         'item_id' => $itemId
                     ];
                     $imageItem->createData($dataImage);
                 }
             }

             $itemDone->createData([
             'item_id' 	=>	$itemId,
             'user_id'	=>	$userId,
             ]);

             $content =  $user['username'].' telah menyelesaikan '.$item['name'].' pada '.$date;

             if ($guardian) {
                 foreach ($guardian as $guard) {
                     $findGuard = $users->find('id', $guard['guard_id']);
                     $dataGuard = [
                         'subject' 	=>	$user['username'].' melaporkan item',
                         'from'     =>	'reportingmit@gmail.com',
                         'to'	    =>	$findGuard['email'],
                         'sender'	=>	'Reporting App',
                         'receiver'	=>	$findGuard['username'],
                         'content'	=>	$content,
                     ];
                     $mailer->send($dataGuard);
                 }
             }

             if (($pic['id'] != $user['id']) && $pic) {
                 $dataPic = [
                     'subject' 	=>	$user['username'].' melaporkan item',
                     'from'     =>	'reportingmit@gmail.com',
                     'to'	    =>	$pic['email'],
                     'sender'	=>	'Reporting App',
                     'receiver'	=>	$pic['username'],
                     'content'	=>	$content,
                 ];

                 //  $this->sendWebNotif($report, $pic['id']);
                 $mailer->send($dataPic);
             }

             return $this->responseDetail(200, false, 'Item telah dilaporkan');
         } else {
             return $this->responseDetail(401, true, 'Anda tidak boleh melaporkan item ini');

         }
     }

     public function postImage($request, $response)
     {
         $item = new Item($this->db);
         $imageItem = new \App\Models\ImageItem($this->db);

         $itemId =  $request->getParsedBody()['item_id'];
         $findItem = $item->find('id', $itemId);
        //  var_dump($findItem);die;

         if ($findItem) {
             $storage = new \Upload\Storage\FileSystem('assets/images');
             $image = new \Upload\File('image', $storage);
             $image->setName(uniqid());
             $image->addValidations(array(
                 new \Upload\Validation\Mimetype(array('image/png', 'image/gif',
                 'image/jpg', 'image/jpeg')),
                 new \Upload\Validation\Size('1M')
             ));

             $image->upload();
             $imgName = $image->getNameWithExtension();

             $dataImage = [
                 'image'   => $imgName,
                 'item_id' => $findItem['id']
             ];
             $imageItem->createData($dataImage);

             return $this->responseDetail(200, false, 'Berhasil menambahkan foto');
         } else {
             return $this->responseDetail(404, true, 'Item tidak ditemukan');

         }

     }

}
