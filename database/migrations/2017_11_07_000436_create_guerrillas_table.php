<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuerrillasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guerrillas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 50);
            $table->string('email', 100);
            $table->enum('guerrilla_type', ['China', 'USMC', 'MEC']);
            $table->integer('money');
            $table->integer('people');
            $table->integer('oil');
            $table->integer('tank');
            $table->integer('assault');
            $table->integer('engineer');
            $table->integer('bunker');
            $table->double('attack_rate');
            $table->double('defense_rate');
            $table->integer('ranking_score');
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
        Schema::dropIfExists('guerrillas');
    }
}
