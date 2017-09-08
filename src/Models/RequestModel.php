<?php
namespace App\Models;
/**
 *
 */
class RequestModel extends BaseModel
{
    protected $table = 'requests';
    protected $column = ['user_id', 'guard_id', 'group_id', 'category'];
    public function requestUserToGroup(array $data)
    {
        $data = [
            'user_id'   =>  $data['user_id'],
            'group_id'  =>  $data['group_id'],
            'category'  =>  0,
        ];
        $this->createData($data);
        return $this->db->lastInsertId();
    }
    public function requestGuardToUser(array $data)
    {
        $data = [
            'user_id'   =>  $data['user_id'],
            'guard_id'  =>  $data['guard_id'],
            'category'  =>  1,
        ];
        $this->createData($data);
        return $this->db->lastInsertId();
    }
    public function requestUserToGuard(array $data)
    {
        $data = [
            'user_id'   =>  $data['user_id'],
            'guard_id'  =>  $data['guard_id'],
            'category'  =>  2,
        ];
        $this->createData($data);
        return $this->db->lastInsertId();
    }
    public function findRequest($column1, $val1, $column2, $val2)
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

    public function findUserRequest($guardId)
    {
        $qb = $this->db->createQueryBuilder();
        $this->query = $qb->select('u.username as user', 'req.*')
        ->from($this->table, 'req')
        ->join('req', 'users', 'u', 'u.id = req.user_id')
        ->where('req.guard_id = :id')
        ->andWhere('req.category = 2')
        ->setParameter(':id', $guardId)
        ->orderBy('req.updated_at', 'desc');

        return $this;
    }

    public function findGuardRequest($userId)
	{
		$qb = $this->db->createQueryBuilder();
		$this->query = $qb->select('u.username as guard', 'req.*')
			 ->from($this->table, 'req')
			 ->join('req', 'users', 'u', 'u.id = req.guard_id')
             ->where('req.user_id = :id')
			 ->andWhere('req.category = 1')
			 ->setParameter(':id', $userId)
             ->orderBy('req.updated_at', 'desc');

		return $this;
	}

    public function findGroupRequest($groupId)
	{
		$qb = $this->db->createQueryBuilder();
		$this->query = $qb->select('g.name as grup', 'u.username as user', 'req.*')
			 ->from($this->table, 'req')
             ->join('req', 'groups', 'g', 'g.id = req.group_id')
             ->leftJoin('req', 'users', 'u', 'u.id = req.user_id')
             ->where('req.group_id = :group_id')
			 ->andWhere('req.category = 0')
             ->setParameter(':group_id', $groupId)
             ->orderBy('req.updated_at', 'desc');

		return $this;
	}

    public function findAllGroupRequest($userId)
	{
        $qb = $this->db->createQueryBuilder();
        $query1 = $qb->select('group_id')
        ->from('user_group')
        ->where('user_id =' . $userId)
        ->andWhere('status = 1')
        ->execute();

		$qb1 = $this->db->createQueryBuilder();
		$this->query = $qb1->select('g.name as group_name', 'u.username as user', 'req.*')
			 ->from($this->table, 'req')
             ->join('req', 'user_group', 'ug', $qb1->expr()->in('req.group_id',$query1))
             ->leftJoin('req', 'groups', 'g', 'g.id = req.group_id')
             ->leftJoin('req', 'users', 'u', 'u.id = req.user_id')
			 ->andWhere('req.category = 0')
             ->orderBy('req.updated_at', 'desc');

		return $this;
	}

}
