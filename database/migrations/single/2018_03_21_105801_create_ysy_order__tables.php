<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateYsyOrderTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('ysy_order', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', '50');
            $table->boolean('sex')->default(0);
            $table->string('ucid', '50');
            $table->string('job', '50');
            $table->string('area', '50');
            $table->string('recommend', '50');
            $table->string('class_id', '50');
            $table->string('subject_id', '50');
            $table->string('daily_id', '50');
            $table->double('order_price');
            $table->timestamp('pay_time');
            $table->boolean('pay_status');
            $table->text('order_info');
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
        //
    }
}
