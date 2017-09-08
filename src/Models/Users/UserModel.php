<?php

namespace App\Models\Users;

use App\Models\BaseModel;

class UserModel extends BaseModel
{
    protected $table = 'users';
    protected $column = ['id', 'name', 'email', 'username', 'password', 'gender',
                'address', 'phone', 'image', 'updated_at', 'created_at', 'status'];

    public function createUser(array $data, $images)
    {
        $data = [
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'image' => $images,
        ];

        $this->createData($data);
        return $this->db->lastInsertId();
    }

    public function register(array $data)
    {
        $data = [
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'gender' => $data['gender'],
            'phone' => $data['phone'],
        ];

        $this->createData($data);
        return $this->db->lastInsertId();
    }

    public function update(array $data, $images, $id)
    {
        $data = [
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'gender' => $data['gender'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'image' => $images,
        ];
        $this->updateData($data, $id);
    }

    public function updateImage(array $data, $id)
    {
        $data = [
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'gender' => $data['gender'],
            'address' => $data['address'],
            'phone' => $data['phone'],
        ];
        $this->updateData($data, $id);
    }

    public function getAllUser()
    {

        $qb = $this->db->createQueryBuilder();

        $this->query = $qb->select('id', 'name', 'username', 'email', 'gender', 'phone',
                        'image','address', 'created_at')
                        ->from($this->table)
                        ->where('status = 2 && deleted = 0');

        return $this;
    }

    public function getUser($column, $val)
    {
        $param = ':'.$column;

        $qb = $this->db->createQueryBuilder();
        $qb->select('id', 'name', 'username', 'email', 'gender', 'phone',
                    'status', 'image','address', 'created_at')
        ->from($this->table)
        ->where($column.' = '. $param)
        ->setParameter($param, $val);

        $query = $qb->execute();
        return $query->fetch();
    }

    public function getInActiveUser()
    {
        $qb = $this->db->createQueryBuilder();
        $qb->select('*')
        ->from($this->table)
        ->where('status = 3 || deleted = 1');

        $query = $qb->execute();
        return $query->fetchAll();
    }

    public function checkDuplicate($username, $email)
    {
        $checkUsername = $this->find('username', $username);
        $checkEmail = $this->find('email', $email);
        if ($checkUsername && $checkEmail) {
            return 3;
        } elseif ($checkUsername) {
            return 1;
        } elseif ($checkEmail) {
            return 2;
        }
        return false;
    }

    //Set user as guardian
	public function setGuardian($id)
	{
		$qb = $this->db->createQueryBuilder();
		$qb->update($this->table)
		   ->set('status', 2)
	 	   ->where('id = ' . $id)
		   ->execute();
	}


    public function changePassword(array $data, $id)
    {
        $dataPassword = [
            'password' => password_hash($data['new_password'], PASSWORD_BCRYPT),
         ];

        $this->updateData($dataPassword, $id);
    }


    public function search($val, $id)
    {
        $qb = $this->db->createQueryBuilder();
        $this->query = $qb->select('*')
                 ->from($this->table)
                 ->where('name LIKE :val')
                 ->orWhere('phone LIKE :val')
                 ->orWhere('username LIKE :val')
                 ->andWhere('id != '. $id)
                 ->andWhere('status != 1')
                 ->andWhere('deleted = 0')
                 ->setParameter('val', '%'.$val.'%');
                //  ->execute();

        // $result = $this->query->execute();

        return $this;
    }

    //Set active user account
    public function setActive($id)
    {
        $qb = $this->db->createQueryBuilder();
        $qb->update($this->table)
        ->set('status', 2)
        ->where('id = ' . $id)
        ->execute();
    }

    public function getUserGuardian($userId)
    {
        $qb = $this->db->createQueryBuilder();

        $query1 = $qb->select('guard_id')
                     ->from('guardian')
                     ->where('user_id =' . $userId)
                     ->execute();

        $qb1 = $this->db->createQueryBuilder();

        $this->query = $qb1->select('u.name', 'u.created_at', 'u.id', 'u.phone', 'u.email', 'u.gender', 'u.address', 'u.image')
             ->from($this->table, 'u')
             ->join('u', 'guardian', 'g', $qb1->expr()->in('u.id', $query1))
             ->groupBy('u.id');

             $result = $this->query->execute();

        return $result->fetchAll();
    }
}
