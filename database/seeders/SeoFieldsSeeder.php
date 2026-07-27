<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeoFieldsSeeder extends Seeder
{
    public function run()
    {
        DB::table('seo_fields')->insert([
            [
                'id' => 1,
                'course_id' => null,
                'blog_id' => null,
                'bootcamp_id' => null,
                'route' => 'Home',
                'name_route' => 'home',
                'meta_title' => 'Arkan lms',
                'meta_keywords' => '[{"value":"Arkan lms"}]',
                'meta_description' => 'Arkan lms',
                'meta_robot' => 'xxxxxx',
                'canonical_url' => 'https://daresley.com',
                'custom_url' => 'https://daresley.com',
                'json_ld' => '<script type=\"application/ld+json\"> {   \"@context\": \"http://schema.org\",   \"@type\": \"WebSite\",   \"name\": \"CodeCanyon\",   \"url\": \"https://codecanyon.net\" } </script>',
                'og_title' => 'Arkan lms',
                'og_description' => 'Arkan lms',
                'og_image' => 'uploads/seo-og-images/1-WhatsApp_Image_2025-09-18_at_7.24.05_PM-removebg-preview.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
