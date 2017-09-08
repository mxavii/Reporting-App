<?php

namespace App\Models;

class ReportedItem extends BaseModel
{
    protected $table = 'reported_item';
    protected $column = ['id', 'item_id', 'user_id'];
    protected $joinTable = 'groups';

    public function create($data)
    {
        $data = [
            'item_id' => $data['item_id'],
            'user_id' => $data['user_id'],
        ];

        $this->createData($data);

        return $this->db->lastInsertId();
    }

    public function update($data, $id)
    {
        $date = date('Y-m-d H:i:s');
        $data = [
            'name'        => $data['name'],
            'description' => $data['description'],
            'reported_at' => $data['reported_at'],
            'group_id'    => $data['group_id'],
            'user_id'     => $data['user_id'],
            'image'       => $data['image'],
        ];

        $this->updateData($data, $id);

    }

    public function getAllItem()
    {
        $qb = $this->db->createQueryBuilder();

        $qb->select('gr.name as groups', 'it.*')
           ->from($this->table, 'it')
           ->join('it', $this->joinTable, 'gr', 'gr.id = it.group_id')
           ->where('it.deleted = 0');

           $result = $qb->execute();

           return $result->fetchAll();
    }

    public function getAllDeleted()
    {
        $qb = $this->db->createQueryBuilder();

        $qb->select('gr.name as groups', 'it.*')
           ->from($this->table, 'it')
           ->join('it', $this->joinTable, 'gr', 'gr.id = it.group_id')
           ->where('it.deleted = 1');

           $result = $qb->execute();

           return $result->fetchAll();
    }

    public function getUserItem($userId, $groupId)
    {
        $qb = $this->db->createQueryBuilder();

        $qb->select('*')
           ->from($this->table)
           ->where('deleted = 0')
           ->andWhere('user_id = '. $userId .'&&'. 'group_id = '. $groupId)
           ->orWhere('group_id = '. $groupId);

       $result = $qb->execute();

       return $result->fetchAll();
    }

}
