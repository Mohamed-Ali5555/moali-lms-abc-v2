<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       DB::table('categories')->insert([
                [

                    'title'        =>'الصف الاول الثانوي',
                    'parent_id'    => 0,
                    'slug'         => slugify('الصف الاول الثانوي'),
                    'status'       => 1,
                    'thumbnail'    => 'uploads/category-thumbnail/الترم-الاول-1761983303.png'
                ],
                  [

                    'title'      =>'الصف الثالث الثانوي',
                    'parent_id'  => 0,
                    'slug'       => slugify('الصف الثالث الثانوي'),
                    'status'     => 1,
                    'thumbnail'  => 'uploads/category-thumbnail/الترم-الاول-1761983303.png'
                ]

            ]);
    }
}
