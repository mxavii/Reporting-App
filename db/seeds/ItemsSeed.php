<?php

use Phinx\Seed\AbstractSeed;

class ItemsSeed extends AbstractSeed
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
                'name'       =>  'Membaca',
                'description'=>  'Membaca buku pelajaran',
                'group_id'   =>  '1',
                'creator'    =>  '1',
                'start_date' =>  '2017-08-9',
                'end_date'   =>  '2017-08-10',
                'recurrent'  =>  'harian',
            ];

            $data[] = [
                'name'       =>  'Upacara',
                'description'=>  'Upacara bendera Hari Senin',
                'recurrent'  =>  'mingguan',
                'start_date' =>  '2017-08-9',
                'end_date'   =>  '2017-08-16',
                'creator'    =>  '1',
                'group_id'   =>  '1',
            ];

            $data[] = [
                'name'       =>  'Tugas Bulanan',
                'name'       =>  'Tugas akhir bulan',
                'start_date' =>  '2017-08-28',
                'end_date'   =>  '2017-09-28',
                'recurrent'  =>  'bulanan',
                'creator'    =>  '1',
                'group_id'   =>  '1',
            ];

            $this->insert('items', $data);
    }
}
