<?php

namespace App\Models;

class UserItem extends BaseModel
{
    protected $table = 'user_item';
    protected $jointTable = 'items';
    protected $column = ['item_id', 'user_id', 'status', 'group_id'];

    public function setItem(array $data, $group)
    {
        $datas =
        [
            "item_id" => $data['item_id'],
            "user_group_id" => $group,
        ];

        $this->createData($datas);
    }

    public function setStatusItem($id)
    {
        $qb = $this->db->createQueryBuilder();

        $qb->update($this->table)
            ->set('status', 1)
            ->where('id = ' . $id)
            ->execute();
    }

    public function findUser($column1, $val1, $column2, $val2)
    {
        $param1 = ':'.$column1;
        $param2 = ':'.$column2;
        $qb = $this->db->createQueryBuilder();
        $qb->select('*')
            ->from($this->table)
            ->setParameter($param1, $val1)
            ->setParameter($param2, $val2)
            ->where($column1 . ' = '. $param1 . '&&' . $column2 . '=' . $param2);
        $result = $qb->execute();
        return $result->fetch();
    }

    public function findAll($column1, $val1)
    {
        $param1 = ':'.$column1;
        $qb = $this->db->createQueryBuilder();
        $qb->select('*')
            ->from($this->table)
            ->setParameter($param1, $val1)
            ->where($column1 . ' = '. $param1);
        $result = $qb->execute();
        return $result->fetchAll();
    }

    public function getItemGroup($userGroupId)
    {
        $qb = $this->db->createQueryBuilder();


        $this->query = $qb->select('it.name', 'it.description', 'it.recurrent',
                                    'it.start_date', 'it.end_date')
        ->from($this->jointTable, 'it')
        ->join('it', $this->table, 'ui', 'ui.item_id = it.id')
        ->where('ui.user_id = :user_id')
        ->andWhere('ui.user_group_id = :user_group_id')
        ->setParameter(':user_group_id', $userGroupId);

        return $this;
    }


    public function getItem($id)
    {
        $qb = $this->db->createQueryBuilder();

        $query1 = $qb->select('item_id')
                    ->from($this->table, 'ui')
                    ->join('ui', 'user_group', 'ug', 'ug.id = ui.user_group_id')
                    ->where('ug.user_id = '.  $id)
                    ->execute();

        $qb1 = $this->db->createQueryBuilder();

		$qb1->select('items.*', 'user_item.status')
			 ->from($this->table, 'user_item')
	 		 ->join('user_item', $jointTable, 'items', $qb1->expr()->in('items.id', $query1))
			 ->where('deleted = 0')
			 ->groupBy('items.id');

             $result = $qb1->execute();
             return $result->fetchAll();
    }

    public function getItemInGroup($userGroupId)
    {
        $qb = $this->db->createQueryBuilder();


        $qb->select('it.name', 'ui.id', 'ui.reported_at', 'it.description',
            'it.recurrent', 'it.start_date', 'it.end_date')
            ->from($this->jointTable, 'it')
            ->join('it', $this->table, 'ui', 'ui.item_id = it.id')
            ->andWhere('ui.user_group_id = :user_group_id')
            ->andWhere('ui.status = 0')
            ->setParameter(':user_group_id', $userGroupId);

        $result = $qb->execute();
        return $result->fetchAll();
    }

    public function getDoneItemInGroup($userGroupId)
    {
        $qb = $this->db->createQueryBuilder();


     $qb->select('it.name', 'ui.id', 'ui.reported_at', 'it.description',
            'it.recurrent', 'it.start_date', 'it.end_date')
        ->from($this->jointTable, 'it')
        ->join('it', $this->table, 'ui', 'ui.item_id = it.id')
        ->andWhere('ui.user_group_id = :user_group_id')
        ->andWhere('ui.status = 1')
        ->setParameter(':user_group_id', $userGroupId);

        $result = $qb->execute();

        return $result->fetchAll();
    }

    public function setStatusItems($id)
    {
        $date = date('Y-m-d H:i:s');
        $data = [
            'status'        => 1,
            'reported_at'   => $date
        ];

        $this->updateData($data, $id);
    }

    public function resetStatusItems($id)
    {
        $date = date('Y-m-d H:i:s');
        $data = [
            'status'        => 0,
            'reported_at'   => null
        ];

        $this->updateData($data, $id);
    }

    public function unselectedItem($userId, $groupId)
    {
        $qb = $this->db->createQueryBuilder();

        $query1 = $qb->select('item_id')
                     ->from($this->table)
                     ->where('user_group_id ='. $userId)
                     ->execute();

        $qb1 = $this->db->createQueryBuilder();

        $this->query = $qb1->select('it.*')
             ->from($this->table, 'ui')
             ->join('ui', $this->jointTable, 'it', $qb1->expr()->notIn('it.id', $query1))
             ->where('it.group_id = :group_id')
             ->andWhere('it.deleted = 0')
             ->setParameter(':group_id', $groupId)
             ->groupBy('it.id');

            $result = $this->query->execute();
            return $result->fetchAll();
    }
}
