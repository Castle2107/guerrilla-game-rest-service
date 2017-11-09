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
            $table->double('attack_rate');
            $table->double('defense_rate');
            $table->double('ranking_score');

            $table->integer('player_id')->unsigned();
            $table->foreign('player_id')
                ->references('id')
                ->on('players')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->string('player_username', 50);
            $table->foreign('player_username')
                ->references('username')
                ->on('players')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('guerrilla_type_id')->unsigned();
            $table->foreign('guerrilla_type_id')
                ->references('id')
                ->on('guerrilla_types')
                ->onUpdate('cascade')
                ->onDelete('cascade');

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
