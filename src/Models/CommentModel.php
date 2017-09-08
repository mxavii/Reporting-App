<?php

namespace App\Models;

class CommentModel extends BaseModel
{
	protected $table = 'comments';
	protected $column = ['comment', 'creator', 'item_id'];

	function add(array $data)
	{
		$data = [
			'comment' 	=> 	$data['comment'],
			'creator'	=>	$data['creator'],
			'item_id'	=>	$data['item_id'],
		];
		$this->createData($data);

		return $this->db->lastInsertId();
	}

	public function getAllComment()
    {
        $qb = $this->db->createQueryBuilder();
        $this->query = $qb->select('*')
            			  ->from($this->table);

        return $this;
    }

	public function getComment($id)
	{
		$qb = $this->db->createQueryBuilder();
		$this->query = $qb->select('c.*', 'u.id', 'u.username', 'u.image')
						  ->from($this->table, 'c')
						  ->where('item_id = :id')
						  ->setParameter(':id', $id)
						  ->join('c', 'users', 'u', 'u.id = c.creator')
						  ->orderBy('c.updated_at', 'desc')
						  ->execute();

		return $this->query->fetchAll();
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

}
?>
