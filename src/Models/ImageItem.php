<?php

namespace App\Models;

class ImageItem extends BaseModel
{
    protected $table = 'image_item';

    public function create($data)
    {
        $data = [
            'image'   => $data['image'],
            'item_id' => $data['item_id']
        ];

        $this->createData($data);
        return $this->db->lastInsertId();
    }

    public function findAllImage($itemId)
    {
        $qb = $this->db->createQueryBuilder();

        $qb->select('*')
           ->from($this->table)
           ->where('item_id = :item_id')
           ->setParameter(':item_id', $itemId);

           $result = $qb->execute();

           return $result->fetchAll();
    }

    public function deleteImageItem($itemId)
    {
        $images = $this->findAllImage($itemId);

        if($images)
           return $result->fetchAll();
    }

}
