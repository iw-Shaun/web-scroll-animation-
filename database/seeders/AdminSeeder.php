<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $email = readline('Enter an email: ');
        $pwd = readline('Enter a password: ');
        DB::table('admins')->insert([
            'email' => $email,
            'password' => bcrypt($pwd),
        ]);
    }
}
