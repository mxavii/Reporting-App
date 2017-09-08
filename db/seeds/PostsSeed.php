<?php

use Phinx\Seed\AbstractSeed;

class PostsSeed extends AbstractSeed
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
            'creator'  =>  3,
            'group_id' =>  1,
            'content'  =>  'Lorem ipsum dolor sit amet, consectetur adipiscing elit,
             sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
        ];

        $this->insert('posts', $data);
    }
}
