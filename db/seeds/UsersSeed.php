<?php

use Phinx\Seed\AbstractSeed;

class UsersSeed extends AbstractSeed
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
            'name'     =>  'Administrator',
            'email'    =>  'reportingmit@gmail.com',
            'username' =>  'admin',
            'password' =>  password_hash('admin123', PASSWORD_DEFAULT),
            'gender'   =>  'Laki-laki',
            'address'  =>  'DKI Jakarta',
            'phone'    =>  '+6281234567890',
            'image'    =>  'admin.jpg',
            'status'   =>  1,
        ];

        $data[] = [
            'name'     =>  'Budiman',
            'email'    =>  'budiman@reporting.net23.net',
            'username' =>  'budiman',
            'password' =>  password_hash('budi123', PASSWORD_DEFAULT),
            'gender'   =>  'Laki-laki',
            'address'  =>  'Bandung, Jawa Barat',
            'phone'    =>  '+6281234567891',
            'status'    =>  2,
        ];

        $data[] = [
            'name'      =>  'Caca Larasati',
            'email'     =>  'caca@null.net23.net',
            'username'  =>  'laras',
            'password'  =>  password_hash('laras123', PASSWORD_DEFAULT),
            'gender'    =>  'Perempuan',
            'address'   =>  'Cirebon, Jawa Barat',
            'phone'     =>  '+6281234567819',
            'status'    =>  2,
        ];

        $data[] = [
            'name'      =>  'Dede Nurdandi',
            'email'     =>  'dandi@reporting.net23.net',
            'username'  =>  'dandi',
            'password'  =>  password_hash('dandi123', PASSWORD_DEFAULT),
            'gender'    =>  'Laki-laki',
            'address'   =>  'Depok, Jawa Barat',
            'phone'     =>  '+6281234567814',
            'status'    =>  2,
        ];

        $data[] = [
            'name'      =>  'Ekawati',
            'email'     =>  'eka@null.net23.net',
            'username'  =>  'ekawati',
            'password'  =>  password_hash('eka123', PASSWORD_DEFAULT),
            'gender'    =>  'Perempuan',
            'address'   =>  'Semarang, Jawa Tengah',
            'phone'     =>  '+6281234567814',
            'status'    =>  2,
        ];

        $data[] = [
            'name'      =>  'Fahmi',
            'email'     =>  'fahmi@null.net23.net',
            'username'  =>  'fahmi',
            'password'  =>  password_hash('fahmi123', PASSWORD_DEFAULT),
            'gender'    =>  'Laki-laki',
            'address'   =>  'Pangandaran, Jawa Barat',
            'phone'     =>  '+6281234567888',
            'status'    =>  2,
        ];


        $this->insert('users', $data);
    }
}
