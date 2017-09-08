<?php
namespace App\Models;

class GuardModel extends BaseModel
{
	protected $table = 'guardian';
	protected $column = ['guard_id', 'user_id'];

	public function add(array $data)
	{
		$data = [
			'guard_id' 	=> 	$data['guard_id'],
			'user_id'	=>	$data['user_id'],
		];
		$this->createData($data);
		return $this->db->lastInsertId();
	}

	// Get all users of guard by guard_id
	public function findAllGuard()
	{
		$qb = $this->db->createQueryBuilder();
		$this->query = $qb->select('users.*')
			 ->from('users', 'users')
			 ->join('users', $this->table, 'guard', 'users.id = guard.guard_id')
			 ->where('users.status = 2');
			 // $result = $qb->execute();
			 return array_map("unserialize", array_unique(array_map("serialize", $this->fetchAll())));

	}

	public function update(array $data, $images, $id)
	{
		$data = [
			'guard_id' 	=> 	$data['guard_id'],
			'user_id'	=>	$data['user_id'],
		];
		$this->updateData($data, $id);
	}

	 public function getAllUser()
	 {
		 $qb = $this->db->createQueryBuilder();
		 $this->query = $qb->select('id', 'guard_id', 'user_id')
		 ->from($this->table)
		 ->where('id', $id);
		 return $this;
	 }

	public function getUser($id)
    {
        $qb = $this->db->createQueryBuilder();
		$qb->select('*')
            ->from($this->table)
            ->where('guard_id ='. $id);
		$query = $qb->execute();
	    return $query->fetchAll();
    }

	// Get all users of guard by guard_id
	public function findAllUser($guardId)
	{
		$qb = $this->db->createQueryBuilder();
		$this->query = $qb->select('users.*')
			 ->from('users', 'users')
			 ->join('users', $this->table, 'guard', 'users.id = guard.user_id')
			 ->where('guard.guard_id = :id')
			 ->setParameter(':id', $guardId);
			 // $result = $qb->execute();
 			return $this;
	}

	//Get all users are not registered to guard
	public function notUser($guardId)
	{
		$qb = $this->db->createQueryBuilder();
		$query1 = $qb->select('user_id')
					 ->from($this->table)
					 ->where('guard_id =' . $guardId)
					 ->execute();
		$qb1 = $this->db->createQueryBuilder();
		$this->query = $qb1->select('u.*')
			 ->from($this->table, 'g')
	 		 ->join('g', 'users', 'u', $qb1->expr()->notIn('u.id', $query1))
			 ->where('u.status != 1')
			 ->andWhere('u.deleted = 0')
			 ->andWhere('u.id !='. $guardId)
			 ->groupBy('u.id');
			//  var_dump($this->fetchAll());die();
		return $this;
	}

	//Find id guardian table by column
	public function findGuards($column1, $val1, $column2, $val2)
	{
		$param1 = ':'.$column1;
		$param2 = ':'.$column2;
		$qb = $this->db->createQueryBuilder();
		$qb->select('*')
			->from($this->table)
			->setParameter($param1, $val1)
			->setParameter($param2, $val2)
			->where($column1 . ' = '. $param1 .'&&'. $column2 . ' = '. $param2);
		$result = $qb->execute();
		return $result->fetch();
	}

	//Find all user user by column
	public function findGuard($column, $value)
    {
        $param = ':'.$column;
        $qb = $this->db->createQueryBuilder();
        $qb->select('*')
            ->from($this->table)
            ->setParameter($param, $value)
            ->where($column . ' = '. $param);
        $result = $qb->execute();
        return $result->fetchAll();
    }

    public function getUserId($id)
    {
        $qb = $this->db->createQueryBuilder();
		$this->query = $qb->select('*')
            ->from($this->table)
            ->where('user_id ='. $id);
		$query = $qb->execute();
	    return $this;
    }

    //HardDelete
    public function deleteGuard($guardId)
    {
        $qb = $this->db->createQueryBuilder();
         $qb->delete($this->table)
            ->where('guard_id = ' . $guardId)
            ->execute();
    }

	// Get all users of guard by guard_id
	public function findGuardbyUser($userId)
	{
		$qb = $this->db->createQueryBuilder();
		$query = $qb->select('u.*')
			 ->from('users', 'u')
			 ->join('u', $this->table, 'g', 'u.id = g.guard_id')
			 ->where('g.user_id = :id')
			 ->setParameter(':id', $userId);
		$result = $query->execute();
		return $result->fetchAll();
	}
}
