<?php

namespace App\Models;

class PostModel extends BaseModel
{
	protected $table = 'posts';
	protected $column = ['image', 'content', 'creator', 'group_id'];

	function add(array $data)
	{
		$data = [
			'name' 			=> 	$data['name'],
			'description'	=>	$data['description'],
			'image'			=>	$data['image'],
			'creator'		=>	$data['creator'],
		];
		$this->createData($data);

		return $this->db->lastInsertId();
	}

	public function getInGroup($groupId)
    {

		$qb = $this->db->createQueryBuilder();

		$this->query = $qb->select('u.name', 'p.*')
		   ->from($this->table, 'p')
		   ->where('p.group_id = '. $groupId)
		   ->join('p', 'users', 'u', 'u.id = p.creator')
		   ->orderBy('updated_at', 'DESC');

	   	return $this;
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
}
?>
