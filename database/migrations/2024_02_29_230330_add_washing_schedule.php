<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('washing_clusters', function (Blueprint $table) {
            $table->id();
            $table->text('name'); #Like: EJC washing machines in the basement or Washing machines in Bibó
            $table->unsignedBigInteger('number_of_machines');
            $table->unsignedBigInteger('user_maximum_open_reservations');
            $table->unsignedBigInteger('open_days_for_reservations');
            $table->unsignedBigInteger('slot_size');
            $table->timestamps();
        });

        Schema::create('washing_reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('cluster_id');
            $table->timestamp('starts_on');
            $table->timestamp('ends_on');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('cluster_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('washing_clusters');
        Schema::drop('washing_reservations');
    }
};
