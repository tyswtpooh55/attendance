<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employees')->insert([
            [
                'last_name' => '山田',
                'first_name' => '太郎',
                'hire_date' => '2004-05-12',
                'status' => 'active',
                'salary' => '200000',
                'birthday' => '1998-02-24',
                'notes' => '山田太郎',
            ],
            [
                'last_name' => '鈴木',
                'first_name' => '一郎',
                'hire_date' => '2004-05-12',
                'status' => 'active',
                'salary' => '200000',
                'birthday' => '1998-02-24',
                'notes' => '鈴木一郎',
            ],
            [
                'last_name' => '佐藤',
                'first_name' => '花子',
                'hire_date' => '2004-05-12',
                'status' => 'active',
                'salary' => '200000',
                'birthday' => '1998-02-24',
                'notes' => '',
            ],
            [
                'last_name' => '高橋',
                'first_name' => '次郎',
                'hire_date' => '2004-05-12',
                'status' => 'active',
                'salary' => '200000',
                'birthday' => '1998-02-24',
                'notes' => '',
            ],
            [
                'last_name' => '田中',
                'first_name' => '三郎',
                'hire_date' => '2004-05-12',
                'status' => 'active',
                'salary' => '200000',
                'birthday' => '1998-02-24',
                'notes' => '',
            ],
            [
                'last_name' => '伊藤',
                'first_name' => '四郎',
                'hire_date' => '2004-05-12',
                'status' => 'active',
                'salary' => '200000',
                'birthday' => '1998-02-24',
                'notes' => '',
            ],
            [
                'last_name' => '山本',
                'first_name' => '五郎',
                'hire_date' => '2004-05-12',
                'status' => 'active',
                'salary' => '200000',
                'birthday' => '1998-02-24',
                'notes' => '',
            ],
            [
                'last_name' => '中村',
                'first_name' => '六郎',
                'hire_date' => '2004-05-12',
                'status' => 'active',
                'salary' => '200000',
                'birthday' => '1998-02-24',
                'notes' => '',
            ],
        ]);
    }
}
