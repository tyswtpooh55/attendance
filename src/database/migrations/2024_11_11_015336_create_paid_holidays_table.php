<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaidHolidaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paid_holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->date('date');
            $table->enum('type', ['full', 'morning', 'afternoon']);
            $table->text('reason')
                ->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])
                ->default('pending');
            //pending(申請中)
            //approved(承認)
            //rejected(拒否)
            $table->decimal('count', 5,1)
                ->default(1);
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
        Schema::dropIfExists('paid_holidays');
    }
}
