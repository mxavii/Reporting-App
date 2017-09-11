<?php

namespace App\Controllers\api;

use App\Models\CommentModel;
use App\Models\Users\UserToken;
use App\Models\Users\UserModel;
use App\Models\Item;
use App\Models\UserGroupModel;

class CommentController extends BaseController
{
    //Get all comment
    public function index($request, $response)
    {
        $comment = new CommentModel($this->db);

        $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');
        $getComment = $comment->getAllComment()->setPaginate($page, 5);
        $query = $request->getQueryParams();

        if ($getComment) {
            $data = $this->responseDetail(200, 'Data tersedia', [
                'query' => $query,
                'result'  => $getComment['data'],
                'meta' => $getComment['pagination']
            ]);

        } else {
            $data = $this->responseDetail(200, 'OK', [
            'result' => null,
            'query'  => $query
            ]);
        }

        return $data;
    }

    public function createComment($request, $response)
    {
        $comments = new CommentModel($this->db);

        $token = $request->getHeader('Authorization')[0];
        $user = $comments->getUserByToken($token);

        $rules = ['required'  => [['comment']]];

        $this->validator->rule('required', ['comment', 'item_id']);
        $this->validator->labels([
            'comment'      => 'Komentar'
        ]);

        if ($this->validator->validate()) {

            $addComment = $comments->add($request->getParsedBody());
            $recentComment = $comments->find('id', $addComment);

            return $this->responseDetail(201, false, 'Komentar berhasil ditambahkan', [
                'data'  => $recentComment
            ]);

        } else {

            return $this->responseDetail(400, true, $this->validator->errors());
        }
    }

    //Delete comment by id
    public function deleteComment($request, $response, $args)
    {
        $item = new Item($this->db);
        $comment = new CommentModel($this->db);
        $userGroup = new UserGroupModel($this->db);

        $token = $request->getHeader('Authorization')[0];
        $findUser = $comment->getUserByToken($token);
        $findComment = $comment->find('id', $args['id']);
        $findItem = $item->find('id', $findComment['item_id']);
        $member = $userGroup->findTwo('group_id', $findItem['group_id'],
                    'user_id', $findUser['id']);
                    // var_dump($findUser);die();
        if (($findComment && $member[0]['status'] == 1) || $findUser['status'] == 1) {
            $comment->hardDelete($args['id']);
            $data['id'] = $args['id'];
            return $this->responseDetail(200, false, 'Komentar berhasil dihapus',[
                'data' =>[
                    'item_id'   =>  $findComment['item_id']
                    ]
            ]);
        } elseif ($findComment && $member[0]['status'] != 1 ) {
            return $this->responseDetail(401, true, 'Anda tidak boleh menghapus komentar');
        } else {
            return $this->responseDetail(404, true, 'Data tidak ditemukan');
        }

    }

    //Edit by id
    public function editComment($request, $response, $args)
    {
        $comment = new CommentModel($this->db);
        $findComment = $comment->find('id', $args['id']);

        if ($findComment) {
            $this->validator->rule('required', 'comment');
            $this->validator->rule('lengthMin', 'comment', 1);

            if ($this->validator->validate()) {
                $comment->updateData($request->getParsedBody(), $args['id']);
                $data['update data'] = $request->getParsedBody();

                $data = $this->responseDetail(200, 'Komentar berhasil diperbarui', [
                    'result'  => $data,
                    'query' => $request->getParsedBody()
                ]);
            } else {
                $data = $this->responseDetail(400, $this->validator->errors(), [
                    'query' => $request->getParsedBody()
                ]);
            }
        } else {
            $data = $this->responseDetail(404, 'Data tidak ditemukan');
        }

        return $data;
    }

    //Find User by id
    public function findComment($request, $response, $args)
    {
        $comment = new CommentModel($this->db);
        $findComment = $comment->find('id', $args['id']);

        if ($findComment) {
            $data = $this->responseDetail(200, 'Data tersedia', [
                'result'    => $findComment,
            ]);

        } else {
            $data = $this->responseDetail(404, 'Data tidak ditemukan');
        }

        return $data;
    }

    //Find User by id
    public function getItemComment($request, $response, $args)
    {
        $comment = new CommentModel($this->db);
        $findComment = $comment->getComment($args['id']);

        if ($findComment) {
            $data = $this->responseDetail(200, false, 'Data tersedia', [
                'data'    => $findComment,
            ]);

        } else {
            $data = $this->responseDetail(404, true, 'Data tidak ditemukan');
        }
// var_dump($data);die();
        return $data;
    }

}
