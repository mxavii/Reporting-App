<?php

use Phinx\Seed\AbstractSeed;

class GroupsSeed extends AbstractSeed
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
            'name'         =>  '7E',
            'description'  =>  'Grup khusus siswa Kelas 7E',
            'creator'      =>  '1'
        ];

        $data[] = [
            'name'         =>  '8E',
            'description'  =>  'Grup khusus siswa Kelas 8E',
            'creator'      =>  '1'
        ];

        $data[] = [
            'name'         =>  '9A',
            'description'  =>  'Grup khusus siswa Kelas 9A',
            'creator'      =>  '1'
        ];

        $this->insert('groups', $data);
    }
}
