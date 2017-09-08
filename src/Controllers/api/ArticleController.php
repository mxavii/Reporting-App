<?php

namespace App\Controllers\api;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\ArticleModel;


class ArticleController extends BaseController
{
	public function index(Request $request, Response $response)
    {
        $article = new ArticleModel($this->db);

        $getArticle = $article->getAll();
        $countArticle = count($getArticle);
        $query = $request->getQueryParams();

        if ($getArticle) {
            $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');

            $get = $article->paginate($page, $getArticle, 5);
            $pagination = $this->paginate($countArticle, 5, $page, ceil($countArticle/5));

            if ($get) {
                $data = $this->responseDetail(200, 'Data available', [
                    'query' => $query,
                    'result'  => $getArticle,
                    'meta' => $pagination
                    ]
                );
            } else {
                $data = $this->responseDetail(404, 'Data not found', ['query' => $query ]);
            }
        } else {
            $data = $this->responseDetail(204, 'Berhasil', [
                'result' => 'Konten tidak tersedia',
                'query'  => $query
            ]);
        }

        return $data;

    }

    public function create(Request $request, Response $response)
    {
		$article = new \App\Models\ArticleModel($this->db);
        $rules = [
            'required' => [
                ['title'],
                ['content'],
                ['image'],
            ]
        ];
        $this->validator->rules($rules);

        $this->validator->labels([
        'title'     =>  'Title',
        'content'   =>  'Content',
        'image'     =>  'Image',
        ]);

        if ($this->validator->validate()) {
            if (!empty($request->getUploadedFiles()['image'])) {
                $storage = New \Upload\Storage\FileSystem('assets/images');
                $image = New \Upload\File('image', $storage);

                $image->setName(uniqid('img-'.date('Ymd').'-'));
                $image->addValidations(array(
                new \Upload\Validation\Mimetype(array('image/png', 'image/gif',
                'image/jpg', 'image/jpeg')),
                new \Upload\Validation\Size('5M')
                ));

                $image->upload();
                $imageName = $image->getNameWithExtension();
            } else {
                $imageName = '';
            }

    			$article->add($request->getParsedBody(), $imageName);
                $data['create data'] = $request->getParsedBody();

                    $data = $this->responseDetail(201, 'Article Succes created', [
                        'result'  => $data,
                        'query' => $request->getParsedBody()
                    ]);

		} else {
			$data = $this->responseDetail(400, 'Errors', $this->validator->errors());
		}

		return $data;
	}

	//Edit article
	public function update(Request $request, Response $response, $args)
    {
        $article = new ArticleModel($this->db);
        $findArticle = $article->find('id', $args['id']);

        if ($findArticle) {
            $this->validator->rule('required', ['title', 'content', 'image']);
            $this->validator->rule('integer', 'id');

            if ($this->validator->validate()) {
                $article->updateData($request->getParsedBody(), $args['id']);
                $data['update data'] = $request->getParsedBody();

                $data = $this->responseDetail(200, 'Data berhasil diperbarui', [
                    'result'  => $data,
                    'query' => $request->getParsedBody()
                ]);
            } else {
                $data = $this->responseDetail(400, $this->validator->errors(), [
                    'query' => $request->getParsedBody()
                ]);
            }
        } else {
            $data = $this->responseDetail(404, 'Data not found');
        }
        return $data;
    }

	//Delete article
	 public function delete(Request $request, Response $response, $args)
    {
        $article = new ArticleModel($this->db);
        $findArticle = $article->find('id', $args['id']);

        if ($findArticle) {
            $article->hardDelete($args['id']);
            $data['id'] = $args['id'];
            $data = $this->responseDetail(200, 'Data has been deleted');
        } else {
            $data = $this->responseDetail(400, 'Data not found');
        }

        return $data;
    }

	public function find(Request $request, Response $response, $args)
    {
        $article = new ArticleModel($this->db);
        $findArticle = $article->find('id', $args['id']);

        if ($findArticle) {
            $data = $this->responseDetail(200, 'Data available', [
                'result'    => $findArticle,
            ]);
        } else {
            $data = $this->responseDetail(400, 'Data not found');
        }

        return $data;
    }

    public function postImage(Request $request, Response $response, $args)
    {
        $article = New ArticleModel($this->db);
        $findArticle = $article->getAll('id', $args['id']);

        if ($findArticle) {
            return $this->responseDetail(404, 'Data Not Found');
        }

        if (!empty($request->getUploadFiles()['image'])) {
            $storage = New \Upload\Storage\FileSystem('assets/images');
            $image = new \Upload\File('image', $storage);

            $image->setName(uniqid('img-', $storage). '-');
            $image->addValidation(array(
                    New \Upload\Validation\MimeType(array('image/png', 'image/gif', 'image/jpg', 'image/jpeg')),
                    New \Upload\Validation\Size('5M')
                ));

            $image->upload();
            $data['image'] = $image->getNameWithExtension();

            $article->update($data, $args['id']);
            $newArticle = $article->getAll('id', $args['id']);

            return $this->responseDetail(200, 'Photo uploaded successfully', [
                    'result' => $newArticle
                ]);
        } else {
            return $this->responseDetail(400, 'File foto belum dipilih');
        }
    }
}
?>
