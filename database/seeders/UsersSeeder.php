<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Hash;
class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
           DB::table('users')->insert([
                [
                    'name'     =>'alaa admin',
                    'email'    => 'admin@gmail.com',
                    'phone'    => '01140404211',
                    'status'   => '1',
                    'category' => 1,
                    'role'     => 'admin',
                    'national_id' =>"30201902182199",
                    'goverment' => 'cairo',
                    'parent_phone'=>'01004538292',
                    'photo'    => 'uploads/category-thumbnail/الترم-الاول-1761983303.png',

                    'email_verified_at' => now(),
                    'password' =>  Hash::make('01230123'),
                ],
                [
                    'name'     =>'mohamed student',
                    'email'    => 'student@gmail.com',
                    'phone'    => '01026833710',
                    'status'   => '1',
                    'category' => 1, // default you can edit it
                    'role'     => 'student',
                    'national_id' =>"30201902182189",
                    'goverment' => 'cairo',
                    'parent_phone'=>'01026833711',
                    'photo'    => 'uploads/category-thumbnail/الترم-الاول-1761983303.png',

                    'email_verified_at' => now(),
                    'password' =>  Hash::make('01230123'),
                ]

            ]);
    }
}
