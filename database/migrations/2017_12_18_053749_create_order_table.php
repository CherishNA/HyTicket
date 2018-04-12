<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_id');
            $table->string('username');
            $table->string('open_id');
            $table->string('ucid');
            $table->string('area');
            $table->string('recommend_id');
            $table->string('order_mobile');
            $table->timestamp('pay_time');
            $table->integer('pay_status');
            $table->integer('sign_status');
            $table->string('order_info');
            $table->double('order_price');
            $table->string('seatNo');
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
        Schema::dropIfExists('order');
    }
}
