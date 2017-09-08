<?php

namespace App\Models;

class UnreportedItem extends BaseModel
{
    protected $table = 'unreported_item';
    protected $column = ['id', 'item_id', 'user_id'];

    public function create($data)
    {
        $data = [
            'item_id' => $data['item_id'],
            'user_id' => $data['user_id'],
            'date'    => $data['date'],
        ];

        $this->createData($data);

        return $this->db->lastInsertId();
    }

}
