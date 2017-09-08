<?php

namespace App\Models;

class UserGroupModel extends BaseModel
{
	protected $table = 'user_group';
	protected $column = ['group_id', 'user_id', 'status'];

	//Set user as member group
	function add(array $data)
	{
		$data = [
			'group_id' 	=> 	$data['group_id'],
			'user_id'	=>	$data['user_id'],
			'status'	=>	$data['status'],
		];
		$this->createData($data);

		return $this->db->lastInsertId();
	}

	//Find all user user by column
	public function findUsers($column, $value)
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

	//Find one user user by column
	public function findUser($column1, $val1, $column2, $val2)
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

	//Set user in group as PIC
	public function setPic($id)
	{
		$qb = $this->db->createQueryBuilder();
		$qb->update($this->table)
		   ->set('status', 1)
		   ->where('id = ' . $id)
		   ->execute();
	}

	//Set user in group as member
	public function setUser($id)
	{
		$qb = $this->db->createQueryBuilder();
		$qb->update($this->table)
		   ->set('status', 0)
 	   	   ->where('id = ' . $id)
		   ->execute();
	}

	//Set user in group as member
	public function setGuardian($id)
	{
		$qb = $this->db->createQueryBuilder();
		$qb->update($this->table)
		   ->set('status', 0)
 	   	   ->where('id = ' . $id)
		   ->execute();
	}

	// Get all user in group by group id
	public function findAll($groupId)
    {
        $qb = $this->db->createQueryBuilder();

        $this->query = $qb->select('u.id', 'u.name', 'u.username', 'u.image', 'u.email', 'u.created_at', 'u.address', 'u.gender', 'u.phone', 'ug.status' )
         	 ->from('users', 'u')
        	 ->join('u', $this->table, 'ug', 'u.id = ug.user_id')
        	 ->where('ug.group_id = :id')
        	 ->setParameter(':id', $groupId);

        return $this;
    }

	//Get all users are not registered in group
	public function notMember($groupId)
	{
		$qb = $this->db->createQueryBuilder();

		$query1 = $qb->select('user_id')
					 ->from($this->table)
					 ->where('group_id =' . $groupId)
					 ->execute();

		$qb1 = $this->db->createQueryBuilder();

		$this->query = $qb1->select('u.*')
			 ->from($this->table, 'ug')
	 		 ->join('ug', 'users', 'u', $qb1->expr()->notIn('u.id', $query1))
			 ->where('deleted = 0')
			 ->andWhere('u.status = 0')
			 ->groupBy('u.id');

		return $this;
	}

	//Get user by user id & group id
	public function getUser($groupId, $userId)
	{
		$qb = $this->db->createQueryBuilder();
		$parameters = [
			':user_id' => $userId,
			':group_id' => $groupId
		];
		$qb->select('users.*')
		   ->from('users', 'users')
		   ->join('users', $this->table, 'ug', 'ug.user_id = users.id')
		   ->where('ug.user_id = :user_id')
		   ->andWhere('ug.group_id = :group_id')
		   ->setParameters($parameters);

		$result = $qb->execute();
		return $result->fetchAll();
	}

	//Get member by group id
	public function getGroupMember($groupId)
	{
		$qb = $this->db->createQueryBuilder();
		$parameters = [
			':group_id' => $groupId
		];
		$qb->select('users.*','ug.status as user_status')
		   ->from('users', 'users')
		   ->join('users', $this->table, 'ug', 'ug.user_id = users.id')
		   ->where('ug.group_id = :group_id')
		   ->andWhere('ug.status = 0')
		   ->setParameters($parameters);

		$result = $qb->execute();
		return $result->fetchAll();
	}

	//Get user by user id & group id
	public function getGroupPic($groupId)
	{
		$qb = $this->db->createQueryBuilder();
		$parameters = [
			':group_id' => $groupId
		];
		$qb->select('users.*')
		   ->from('users', 'users')
		   ->join('users', $this->table, 'ug', 'ug.user_id = users.id')
		   ->where('ug.group_id = :group_id')
		   ->andWhere('ug.status = 1')
		   ->setParameters($parameters);

		$result = $qb->execute();
		return $result->fetchAll();
	}

	// Get all groups by user id
	public function findAllGroup($userId)
	{
		$qb = $this->db->createQueryBuilder();

		$qb->select('groups.*', 'user_group.status')
			 ->from($this->table, 'user_group')
			 ->join('user_group', 'groups', 'groups', ' user_group.group_id = groups.id')
			 ->where('user_group.user_id = :id')
			 ->setParameter(':id', $userId);

			 $result = $qb->execute();
			return $result->fetchAll();
	}

	public function generalGroup($userId)
	{
		$qb = $this->db->createQueryBuilder();
		$qb->select('g.*')
			 ->from($this->table, 'ug')
			 ->join('ug', 'groups', 'g', 'g.id = ug.group_id')
			 ->where('ug.user_id = :id')
			 ->andWhere('ug.status = 0 or ug.status = 1')
			 ->andWhere('g.deleted = 0')
			 ->setParameter(':id', $userId)
			 ->orderBy('g.name', 'asc');
			 $result = $qb->execute();
			return $result->fetchAll();
	}

	public function picGroup($userId)
	{
		$qb = $this->db->createQueryBuilder();

		$this->query = $qb->select('groups.*', 'user_group.status')
			 ->from($this->table, 'user_group')
			 ->join('user_group', 'groups', 'groups', ' user_group.group_id = groups.id')
			 ->where('user_group.user_id = :id')
			 ->setParameter(':id', $userId);

			 // $result = $qb->execute();
			return $this;
	}

	public function findPic($groupId)
	{
		$qb = $this->db->createQueryBuilder();

		$qb->select('users.*')
			 ->from('users', 'users')
			 ->join('users', $this->table, 'user_group', 'users.id = user_group.user_id')
			 ->where('user_group.group_id = :id')
			 ->andWhere('user_group.status = 1')
			 ->setParameter(':id', $groupId);

			 $result = $qb->execute();
			return $result->fetchAll();
	}

	public function findMember($groupId)
	{
		$qb = $this->db->createQueryBuilder();

		$qb->select('users.*')
			 ->from('users', 'users')
			 ->join('users', $this->table, 'user_group', 'users.id = user_group.user_id')
			 ->where('user_group.group_id = :id')
			 ->andWhere('user_group.status = 0')
			 ->setParameter(':id', $groupId);

			 $result = $qb->execute();
			return $result->fetchAll();
	}

	public function findAllMember($groupId)
	{
		$qb = $this->db->createQueryBuilder();

		$qb->select('users.*', 'ug.status as status_member')
			 ->from('users', 'users')
			 ->join('users', $this->table, 'ug', 'users.id = ug.user_id')
			 ->where('ug.group_id = :id')
			 ->setParameter(':id', $groupId);

			 $result = $qb->execute();
			return $result->fetchAll();
	}

	public function getAllGroup()
	{
		$qb = $this->db->createQueryBuilder();

		$this->query = $qb->select('id', 'name', 'description', 'image', 'creator')
							->from($this->table)
							->where('deleted = 0');

		return $this;
	}
}

?>
