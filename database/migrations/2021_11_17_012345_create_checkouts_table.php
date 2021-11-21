<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckoutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkouts', function (Blueprint $table) {
            $table->id();
//            $table->foreignId('rpt_id')->constrained('rpts')->onDelete('restrict')->onUpdate('cascade');
//            $table->string('pin', 50)->nullable();
//            $table->string('arp', 50)->nullable();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->string('bill_num', 50)->nullable();
            $table->double('amount')->default(0);
            $table->boolean('paid')->default(false);
            $table->dateTime('payment_date')->nullable();
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
        Schema::dropIfExists('checkouts');
    }
}
