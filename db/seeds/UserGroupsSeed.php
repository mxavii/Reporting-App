<?php

use Phinx\Seed\AbstractSeed;

class UserGroupsSeed extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $data[] = [
            'group_id' =>  '1',
            'user_id'  =>  '3',
        ];

        $data[] = [
            'group_id' =>  '1',
            'user_id'  =>  '5',
        ];

        $this->insert('user_group', $data);
    }
}
