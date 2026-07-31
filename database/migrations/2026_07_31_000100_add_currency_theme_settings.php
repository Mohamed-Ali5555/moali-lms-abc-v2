<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $defaults = [
            'currency_code'     => 'EGP',
            'currency_symbol'   => 'جنيه',
            'currency_position' => 'right-space',
        ];

        foreach ($defaults as $type => $description) {
            $exists = DB::table('theme_settings')->where('type', $type)->exists();

            if (! $exists) {
                DB::table('theme_settings')->insert([
                    'type'        => $type,
                    'description' => $description,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('theme_settings')
            ->whereIn('type', ['currency_code', 'currency_symbol', 'currency_position'])
            ->delete();
    }
};
