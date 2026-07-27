<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class languagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
 {
       DB::table('languages')->insert([
                [
                    'id'=>3,
                    'name' => 'English',
                    'direction' =>'ltr',
                ],
                  [
                    'id'=>4,
                    'name' => 'العربية',
                    'direction' =>'rtl',
                ]

            ]);
    }
}
