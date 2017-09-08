<?php

namespace App\Models;

class GroupModel extends BaseModel
{
	protected $table = 'groups';
	protected $column = ['name', 'description', 'image', 'deleted'];

	function add(array $data)
	{
		$data = [
			'name' 			=> 	$data['name'],
			'description'	=>	$data['description'],
			'creator'		=>	$data['creator'],
			// 'image'			=>	$data['image'],
		];
		$this->createData($data);

		return $this->db->lastInsertId();
	}

	public function getInActive()
    {
        $qb = $this->db->createQueryBuilder();
        $qb->select('*')
            ->from($this->table)
            ->where('deleted = 1');
        $query = $qb->execute();
        return $query->fetchAll();
    }

	public function restore($id)
	{
		$qb = $this->db->createQueryBuilder();

		$qb->update($this->table)
		   ->set('deleted', 0)
		   ->where('id = ' . $id)
		   ->execute();
	}

	public function search($val)
    {
        $qb = $this->db->createQueryBuilder();
        $this->query = $qb->select('*')
                 ->from($this->table)
                 ->where('name LIKE :val')
                 ->andWhere('deleted = 0')
                 ->setParameter('val', '%'.$val.'%');

        $result = $this->query->execute();

        return $result->fetchAll();
    }

	public function getUserGroup($userId, $status)
	{
		$qb = $this->db->createQueryBuilder();

		$query1 = $qb->select('group_id')
					 ->from('user_group', 'ug')
					 ->where('user_id =' . $userId)
					 ->join('ug', 'groups', 'g', 'g.id = ug.group_id')
					 ->andWhere('ug.status ='. $status)
					 ->execute();

		$qb1 = $this->db->createQueryBuilder();

		$this->query = $qb1->select('g.name')
			 ->from($this->table, 'g')
			 ->join('g', 'user_group' , 'ug', $qb1->expr()->in('g.id', $query1))
			 ->groupBy('g.id');

			 $result = $this->query->execute();

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

	public function getAllGroupNonActive()
	{
		$qb = $this->db->createQueryBuilder();

		$this->query = $qb->select('id', 'name', 'description', 'image', 'creator')
							->from($this->table)
							->where('deleted = 1');

		return $this;
	}
}
?>
