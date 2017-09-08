<?php

use Phinx\Seed\AbstractSeed;

class GuardsSeed extends AbstractSeed
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
            'guard_id' =>  '2',
            'user_id'  =>  '3',
        ];

        $data[] = [
            'guard_id' =>  '2',
            'user_id'  =>  '5',
        ];

        $this->insert('guardian', $data);
    }
}
