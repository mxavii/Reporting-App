<?php

namespace App\Controllers\api;

use App\Models\Users\UserModel;
use App\Models\Users\UserToken;

class UserController extends BaseController
{
    //Get all user
    public function index($request, $response)
    {
        $user = new UserModel($this->db);

        $page = !$request->getQueryParam('page') ? 1 : $request->getQueryParam('page');
        $perPage = $request->getQueryParam('perpage');
        $getUser = $user->getAllUser()->setPaginate($page, $perPage);

        if ($getUser) {
            return $this->responseDetail(200, false, 'Data tersedia', [
                'data' => $getUser['data'],
                'pagination' => $getUser['pagination']
            ]);

        } else {
            return $this->responseDetail(200, false, 'Data kosong');
        }

        // return $data;
    }

    //User register
    public function register($request, $response)
    {
        $mailer = new \App\Extensions\Mailers\Mailer();
        $registers = new \App\Models\RegisterModel($this->db);
        $user = new UserModel($this->db);

        $this->validator
        ->rule('required', ['username', 'password', 'email'])
        ->message('{field} tidak boleh kosong!')
        ->label('Username', 'Password', 'Email');

        $this->validator->rule('email', 'email');
        $this->validator->rule('alphaNum', 'username');
        $this->validator->rule('lengthMax', [
        'username',
        'name',
        'password'
        ], 30);

        $this->validator->rule('lengthMin', ['username','password'], 5);

        if ($this->validator->validate()) {

            $base = $request->getUri()->getBaseUrl();

            if (!empty($request->getUploadedFiles()['image'])) {
                $storage = new \Upload\Storage\FileSystem('assets/images');
                $image = new \Upload\File('image',$storage);

                $image->setName(uniqid('img-'.date('Ymd').'-'));
                $image->addValidations(array(
                new \Upload\Validation\Mimetype(array('image/png', 'image/gif',
                'image/jpg', 'image/jpeg')),
                new \Upload\Validation\Size('512K')
                ));

                $image->upload();
                $imageName = $image->getNameWithExtension();

            } else {
                $imageName = 'user.jpg';
            }

            $register = $user->checkDuplicate($request->getParsedBody()['username'],
            $request->getParsedBody()['email']);

            if ($register == 3) {
                return $this->responseDetail(409, true, 'Email & username sudah digunakan');

            } elseif ($register == 1) {
                return $this->responseDetail(409, true, 'Username sudah digunakan');

            } elseif ($register == 2) {
                return $this->responseDetail(409, true, 'Email sudah digunakan');

            } else {
                $userId = $user->createUser($request->getParsedBody(), $imageName);
                $newUser = $user->getUser('id', $userId);

                $token = md5(openssl_random_pseudo_bytes(8));
                $tokenId = $registers->setToken($userId, $token);
                $userToken = $registers->find('id', $tokenId);

                $keyToken = $userToken['token'];

                $activateUrl = '<a href ='.$base ."/activateaccount/".$keyToken.'>
                <h3>AKTIFKAN AKUN</h3></a>';

                $content = '<html><head></head>
                <body style="font-family: Verdana;font-size: 12.0px;">
                <table border="0" cellpadding="0" cellspacing="0" style="max-width: 600.0px;">
                <tbody><tr><td><table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tbody><tr><td align="left">
                </td></tr></tbody></table></td></tr><tr height="16"></tr><tr><td>
                <table bgcolor="#337AB7" border="0" cellpadding="0" cellspacing="0"
                style="min-width: 332.0px;max-width: 600.0px;border: 1.0px solid rgb(224,224,224);
                border-bottom: 0;" width="100%">
                <tbody><tr><td colspan="3" height="42px"></td></tr>
                <tr><td width="32px"></td>
                <td style="font-family: Roboto-Regular , Helvetica , Arial , sans-serif;font-size: 24.0px;
                color: rgb(255,255,255);line-height: 1.25;">Aktivasi Akun Reporting App</td>
                <td width="32px"></td></tr>
                <tr><td colspan="3" height="18px"></td></tr></tbody></table></td></tr>
                <tr><td><table bgcolor="#FAFAFA" border="0" cellpadding="0" cellspacing="0"
                style="min-width: 332.0px;max-width: 600.0px;border: 1.0px solid rgb(240,240,240);
                border-bottom: 1.0px solid rgb(192,192,192);border-top: 0;" width="100%">
                <tbody><tr height="16px"><td rowspan="3" width="32px"></td><td></td>
                <td rowspan="3" width="32px"></td></tr>
                <tr><td><p>Yang terhormat '.$request->getParsedBody()['username'].',</p>
                <p>Terima kasih telah mendaftar di Reporting App.
                Untuk mengaktifkan akun Anda, silakan klik tautan di bawah ini.</p>
                <div style="text-align: center;"><p>
                <strong style="text-align: center;font-size: 24.0px;font-weight: bold;">
                '.$activateUrl.'</strong></p></div>
                <p>Jika tautan tidak bekerja, Anda dapat menyalin atau mengetik kembali
                 tautan di bawah ini.</p>
                '.$base .'/activateaccount/'.$keyToken.'<p><br>
                <p>Terima kasih, <br /><br /> Admin Reporting App</p></td></tr>
                <tr height="32px"></tr></tbody></table></td></tr>
                <tr height="16"></tr>
                <tr><td style="max-width: 600.0px;font-family: Roboto-Regular , Helvetica , Arial , sans-serif;
                font-size: 10.0px;color: rgb(188,188,188);line-height: 1.5;"></td>
                </tr><tr><td></td></tr></tbody></table></body></html>';

                $mail = [
                'subject'   =>  'Reporting App - Verifikasi Email',
                'from'      =>  'reportingmit@gmail.com',
                'to'        =>  $newUser['email'],
                'sender'    =>  'Reporting App',
                'receiver'  =>  $newUser['name'],
                'content'   =>  $content,
                ];

                $mailer->send($mail);

                return  $this->responseDetail(201, false, 'Pendaftaran berhasil.
                silakan cek email anda untuk mengaktifkan akun');
            }
        } else {
            $errors = $this->validator->errors();

            return  $this->responseDetail(400, true, $errors);
        }

    }


    public function postImage($request, $response, $args)
    {
        $user = new UserModel($this->db);

        $findUser = $user->getUser('id', $args['id']);

        if (!$findUser) {
            return $this->responseDetail(404, true, 'Akun tidak ditemukan');
        }
        if ($this->validator->validate()) {

            if (!empty($request->getUploadedFiles()['image'])) {
                $storage = new \Upload\Storage\FileSystem('assets/images');
                $image = new \Upload\File('image',$storage);

                $image->setName(uniqid('img-'.date('Ymd').'-'));
                $image->addValidations(array(
                    new \Upload\Validation\Mimetype(array('image/png', 'image/gif',
                    'image/jpg', 'image/jpeg')),
                    new \Upload\Validation\Size('512K')
                ));

                try {
                    // Success!
                    $image->upload();
                } catch (\Exception $e) {
                    $errors = $image->getErrors();
                    return $this->responseDetail(400, true, $errors[0]);

                }
                $data['image'] = $image->getNameWithExtension();

                $user->updateData($data, $args['id']);
                $newUser = $user->getUser('id', $args['id']);
                if (file_exists('assets/images/'.$findUser['image'])) {
                    unlink('assets/images/'.$findUser['image']);
                }
                return  $this->responseDetail(200, false, 'Foto berhasil diunggah', [
                    'result' => $newUser
                ]);

            } else {
                return $this->responseDetail(400, true, 'File foto belum dipilih');

            }
        } else {
            $errors = $this->validator->errors();

            return  $this->responseDetail(400, true, $errors);
        }

    }

    //Delete user account by id
    public function deleteUser($request, $response, $args)
    {
        $user = new UserModel($this->db);
        $findUser = $user->find('id', $args['id']);
        $token = $request->getHeader('Authorization')[0];

        if ($findUser) {
            if (file_exists('assets/images/'.$findUser['image'])) {
                unlink('assets/images/'.$findUser['image']);
            }
            $user->hardDelete($args['id']);
            $data['id'] = $args['id'];
            return $this->responseDetail(200, false, 'Akun berhasil dihapus');
        } else {
            return $this->responseDetail(400, true, 'Akun tidak ditemukan');
        }

        // return $data;
    }

    //Delete user account
    public function delAccount($request, $response)
    {
        $users = new UserModel($this->db);
        $userToken = new \App\Models\Users\UserToken($this->container->db);

        $token = $request->getHeader('Authorization')[0];

        $findUser = $userToken->find('token', $token);
        $user = $users->find('id', $findUser['user_id']);

        if ($user) {
            $users->hardDelete($user['id']);
            $data['id'] = $user['id'];
            return $this->responseDetail(200, false, 'Akun berhasil dihapus');
        } else {
            return $this->responseDetail(400, true, 'Akun tidak ditemukan');
        }
        // return $data;
    }

    //Update user account by id
    public function updateUser($request, $response, $args)
    {
        $user = new UserModel($this->db);
        $findUser = $user->find('id', $args['id']);

        if ($findUser) {
            $this->validator->rule('required', ['name', 'email',
            'password', 'gender', 'address', 'phone']);
            $this->validator->rule('email', 'email');
            // $this->validator->rule('alphaNum', 'username');
            $this->validator->rule('numeric', 'phone');
            $this->validator->rule('lengthMin', ['name', 'email'], 5);
            $this->validator->rule('integer', 'id');

            if ($this->validator->validate()) {
                $user->updateData($request->getParams(), $args['id']);
                $data = $user->getUser('id', $args['id']);

                return $this->responseDetail(201, false, 'Data berhasil diperbarui', [
                    'data'  => $data,
                ]);
            } else {
                return $this->responseDetail(400, true, $this->validator->errors());
            }
        } else {
            return $this->responseDetail(404, true, 'Akun tidak ditemukan');
        }
        // return $data;
    }

    //Update user account
    public function editAccount($request, $response)
    {
        $users = new UserModel($this->db);
        $userToken = new \App\Models\Users\UserToken($this->container->db);

        $token = $request->getHeader('Authorization')[0];
        $user = $userToken->find('token', $token);
        $findUser = $users->find('id', $user['user_id']);

        if ($findUser) {
            $this->validator->rule('required', ['name', 'email', 'username',
            'password', 'gender', 'address', 'phone', 'image']);
            $this->validator->rule('email', 'email');
            $this->validator->rule('alphaNum', 'username');
            $this->validator->rule('numeric', 'phone');
            $this->validator->rule('lengthMin', ['name', 'email', 'username', 'password'], 5);
            $this->validator->rule('integer', 'id');
            if ($this->validator->validate()) {
                $users->updateData($request->getParsedBody(), $user['user_id']);
                $data['update data'] = $request->getParsedBody();

                return $this->responseDetail(200, false, 'Data berhasil diupdate', [
                    'data'  => $data
                    ]);
            } else {
                return $this->responseDetail(400, true, $this->validator->errors());
            }
        } else {
            return $this->responseDetail(400, true, 'Data tidak ditemukan');
        }
        // return $data;
    }

    //Find User by id
    public function findUser($request, $response, $args)
    {
        $user = new UserModel($this->db);
        $findUser = $user->find('id', $args['id']);

        if ($findUser) {
            return $this->responseDetail(200, false, 'Data tersedia', [
            'data'    => $findUser,
            ]);
        } else {
            return $this->responseDetail(400, true, 'Akun tidak ditemukan');
        }

        // return $data;
    }

    //Find User by id
    public function detailAccount($request, $response)
    {
        $users = new UserModel($this->db);
        $userToken = new \App\Models\Users\UserToken($this->container->db);

        $token = $request->getHeader('Authorization')[0];
        $user = $userToken->find('token', $token);
        $findUser = $users->find('id', $user['user_id']);

        if ($findUser) {
            return $this->responseDetail(200, false, 'Data tersedia', [
                'data'  => $findUser
                ]);
        } else {
            return $this->responseDetail(400, true, 'Data tidak ditemukan');
        }

        // return $data;
    }

    //User login
    public function login($request, $response)
    {
        $users = new UserModel($this->db);

        $login = $users->find('username', $request->getParam('username'));
        $user = $users->getUser('username', $request->getParam('username'));

        if (empty($login)) {
            return $this->responseDetail(401, true, 'Username tidak terdaftar');
        } else {
            $check = password_verify($request->getParam('password'), $login['password']);

            if ($check) {
                $token = new UserToken($this->db);

                $token->setToken($login['id']);
                $getToken = $token->find('user_id', $login['id']);

                $key = [
                'key_token' => $getToken['token'],
                ];

                return $this->responseDetail(200, false, 'Login berhasil', [
                    'data'   => $user,
                    'key'     => $key
                ]);
            } else {
                return $this->responseDetail(401, true, 'Password salah');
            }
        }
        // return $data;
    }

    public function activateAccount($request, $response, $args)
    {
        $users = new UserModel($this->db);
        $registers = new \App\Models\RegisterModel($this->db);

        $userToken = $registers->find('token', $args['token']);
        $base = $request->getUri()->getBaseUrl();
        $now = date('Y-m-d H:i:s');

        if ($userToken && $userToken['expired_date'] > $now) {

            $user = $users->setActive($userToken['user_id']);
            $registers->hardDelete($userToken['id']);

            return  $this->view->render($response, 'response/activation.twig', [
                'message' => 'Akun telah berhasil diaktivasi'
            ]);

        } elseif ($userToken['expired_date'] > $now) {

            return  $this->view->render($response, 'response/activation.twig', [
                'message' => 'Token telah kadaluarsa'
            ]);
            // return $this->responseDetail(400, true, 'Token telah kadaluarsa');

        } else{

            return  $this->view->render($response, 'response/activation.twig', [
                'message' => 'Token salah atau anda belum mendaftar'
            ]);
            // return $this->responseDetail(400, true, 'Anda belum mendaftar');
        }

    }

    public function logout($request, $response )
    {
        $token = $request->getHeader('Authorization')[0];

        $userToken = new UserToken($this->db);
        $findUser = $userToken->find('token', $token);

        $userToken->delete('user_id', $findUser['user_id']);
        return $this->responseDetail(200, false, 'Logout berhasil');
    }

    public function forgotPassword($request, $response)
    {
        $users = new UserModel($this->db);
        $mailer = new \App\Extensions\Mailers\Mailer();
        $registers = new \App\Models\RegisterModel($this->db);

        $findUser = $users->find('email', $request->getParam('email'));
        $base = $request->getUri()->getBaseUrl();

        if (!$findUser) {
            return $this->responseDetail(404, true, 'Email tidak terdaftar');

        } elseif ($findUser) {
            $token = str_shuffle('r3c0Ve12y').substr(md5(microtime()),rand(0,26),37);
            $tokenId = $registers->setToken($findUser['id'], $token);
            // $data['new_password'] = substr(md5(microtime()),rand(0,26),17);
            // $users->changePassword($data, $findUser['id']);

            $resetUrl = '<a href ='.$base ."/password/reset/".$token.'>
            <h3>RESET PASSWORD</h3></a>';
            $content = '<html><head></head>
            <body style="margin: 0;padding: 0; font-family: Verdana;font-size: 12.0px;">
            <table border="0" cellpadding="0" cellspacing="0" style="max-width: 600.0px;">
            <tbody><tr><td><table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tbody><tr><td align="left">
            </td></tr></tbody></table></td></tr><tr height="16"></tr><tr><td>
            <table bgcolor="#337AB7" border="0" cellpadding="0" cellspacing="0"
             style="min-width: 332.0px;max-width: 600.0px;border: 1.0px solid rgb(224,224,224);
             border-bottom: 0;" width="100%">
            <tbody><tr><td colspan="3" height="42px"></td></tr>
            <tr><td width="32px"></td>
            <td style="font-family: Roboto-Regular , Helvetica , Arial , sans-serif;font-size: 24.0px;
            color: rgb(255,255,255);line-height: 1.25;">Setel Ulang Sandi Reporting App</td>
            <td width="32px"></td></tr>
            <tr><td colspan="3" height="18px"></td></tr></tbody></table></td></tr>
            <tr><td><table bgcolor="#FAFAFA" border="0" cellpadding="0" cellspacing="0"
             style="min-width: 332.0px;max-width: 600.0px;border: 1.0px solid rgb(240,240,240);
             border-bottom: 1.0px solid rgb(192,192,192);border-top: 0;" width="100%">
            <tbody><tr height="16px"><td rowspan="3" width="32px"></td><td></td>
            <td rowspan="3" width="32px"></td></tr>
            <tr><td><p>Yang terhormat '.$findUser["name"].',</p>
            <p>Baru-baru ini Anda meminta untuk menyetel ulang kata sandi akun Reporting App Anda.
              Untuk mengubah kata sandi akun Anda, silakan ikuti tautan di bawah ini.</p>
              <div style="text-align: center;"><p>'.$resetUrl.'</p></div>
             <p>Jika tautan tidak bekerja, Anda dapat menyalin atau mengetik kembali
            tautan berikut.</p>
            <p>'.$base."/password/reset/".$token.'</p>
            <p>Jika Anda tidak seharusnya menerima email ini, mungkin pengguna lain
            memasukkan alamat email Anda secara tidak sengaja saat mencoba menyetel
            ulang sandi. Jika Anda tidak memulai permintaan ini, Anda tidak perlu
            melakukan tindakan lebih lanjut dan dapat mengabaikan email ini dengan aman.</p>
            <p> <br />Terima kasih, <br /><br /> Admin Reporting App</p></td></tr>
            <tr height="32px"></tr></tbody></table></td></tr>
            <tr height="16"></tr>
            <tr><td style="max-width: 600.0px;font-family: Roboto-Regular , Helvetica , Arial , sans-serif;
            font-size: 10.0px;color: rgb(188,188,188);line-height: 1.5;"></td>
            </tr><tr><td></td></tr></tbody></table></body></html>';

            $mail = [
            'subject'   =>  'Setel Ulang Sandi',
            'from'      =>  'reportingmit@gmail.com',
            'to'        =>  $findUser['email'],
            'sender'    =>  'Reporting App Account Recovery',
            'receiver'  =>  $findUser['name'],
            'content'   =>  $content,
            ];

            $mailer->send($mail);

            return $this->responseDetail(200, false, 'Silakan cek email anda untuk mengubah password');
        }

    }

    //Change password
    public function changePassword($request, $response, $args)
    {
        $users = new UserModel($this->db);
        $userToken = new \App\Models\Users\UserToken($this->container->db);

        $token = $request->getHeader('Authorization')[0];
        $findUser = $userToken->find('token', $token);
        $user = $users->find('id', $findUser['user_id']);

        $password = password_verify($request->getParam('password'), $user['password']);
        // var_dump($request->getParams());die();

        if ($password) {
            $this->validator->rule('required', ['new_password', 'password']);
            $this->validator->rule('lengthMin', ['new_password'], 5);

            if ($this->validator->validate()) {
                $newData = [
                'password'  => password_hash($request->getParam('new_password'), PASSWORD_BCRYPT)
                ];
                $users->updateData($newData, $user['id']);
                $data = $findUser;

                return $this->responseDetail(200, false, 'Password berhasil diubah', [
                    'data'  => $data
                    ]);
            } else {
                return $this->responseDetail(400, true, 'Password minimal 5 karakter');
            }
        } else {
            return $this->responseDetail(400, true, 'Password lama tidak sesuai');
        }
    }

      //Update profile account
    public function updateProfile($request, $response)
    {
        $users = new UserModel($this->db);
        $userToken = new \App\Models\Users\UserToken($this->container->db);

        $token = $request->getHeader('Authorization')[0];
        $user = $userToken->find('token', $token);
        $findUser = $users->find('id', $user['user_id']);
        // var_dump($findUser);die();
        if ($findUser) {
            $this->validator->rule('required', ['name', 'email', 'gender', 'address', 'phone']);
            $this->validator->rule('email', 'email');
            // $this->validator->rule('alphaNum', 'username');
            $this->validator->rule('numeric', 'phone');
            $this->validator->rule('lengthMin', ['name', 'email'], 5);
            $this->validator->rule('integer', 'id');
            if ($this->validator->validate()) {
                $users->updateData($request->getParsedBody(), $user['user_id']);
                $data['update data'] = $request->getParsedBody();

                return $this->responseDetail(200, false, 'Data berhasil diupdate', [
                    'data'  => $data
                    ]);
            } else {
                return $this->responseDetail(400, true, $this->validator->errors());
            }
        } else {
            return $this->responseDetail(400, true, 'Data tidak ditemukan');
        }
        // return $data;
    }

    public function getResetPassword($request, $response, $args)
    {
        $users = new UserModel($this->db);
        $registers = new \App\Models\RegisterModel($this->db);

        $findToken = $registers->find('token', $args['token']);
        // var_dump($user);die();
        if ($findToken) {
            return $this->responseDetail(200, false, 'Token diterima', [
                'data'  => [
                    'token' => $request->getParam('token')
                ]
            ]);
        } else {
            return $this->responseDetail(404, true, 'Token salah');
        }
    }

    //Change password
    public function resetPassword($request, $response, $args)
    {
        $users = new UserModel($this->db);
        $registers = new \App\Models\RegisterModel($this->db);

        $this->validator->rule('required', ['email', 'password']);
        $this->validator->rule('equals', 'password2', 'password');
        $this->validator->rule('email', 'email');
        $this->validator->rule('lengthMin', ['password'], 5);

        if ($this->validator->validate()) {
            $findUser = $users->getUser('email', $request->getParam('email'));
            $findToken = $registers->find('token', $request->getParam('token'));
            // var_dump($findToken);die();
            if ($findUser['id'] == $findToken['user_id']) {
                $data['new_password'] = $request->getParam('password');
                $users->changePassword($data, $findUser['id']);
                $registers->hardDelete($findToken['id']);
                return $this->responseDetail(200, false, 'Password berhasil diperbarui', [
                    'data'  => $findUser
                ]);
            } else {
                return $this->responseDetail(404, true, 'Data tidak ditemukan', [
                    'data'  => [
                        'token' => $request->getParam('token')
                    ]
                ]);
            }
        } else {
            return $this->responseDetail(400, true, $this->validator->errors(), [
                'data'  => [
                    'token' => $request->getParam('token')
                ]
            ]);
        }
    }

}
