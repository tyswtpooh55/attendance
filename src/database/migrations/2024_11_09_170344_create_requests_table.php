<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id');
            $table->date('date');
            $table->foreignId('work_id')
                ->nullable();
            $table->time('work_in')
                ->nullable();
            $table->time('work_out')
                ->nullable();
            $table->json('breakings')
                ->nullable();
            $table->text('reason')
                ->nullable();
            $table->enum('action', ['delete', 'add', 'change']);
            //delete(既存の勤務情報の削除→付随する休憩情報の削除)
            //add(新規の勤務情報の追加、付随する新規休憩情報の追加)
            //change(既存の勤務情報変更、既存の勤務情報に付随する休憩情報の削除・変更・追加)
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
    }
}
