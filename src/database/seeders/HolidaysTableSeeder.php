<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HolidaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('holidays')->insert([
            //年度によって変化しない祝日
            [
                'year' => 2024,
                'month' => 11,
                'day' => 14,
                'name' => '休日',
                'type' => 'full',
            ]
        ]);
    }
}
