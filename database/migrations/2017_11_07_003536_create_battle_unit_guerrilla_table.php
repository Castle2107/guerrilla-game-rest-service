<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBattleUnitGuerrillaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('battle_unit_guerrilla', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('guerrilla_id')->unsigned();
            $table->foreign('guerrilla_id')
                ->references('id')
                ->on('guerrillas')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('battle_unit_id')->unsigned();
            $table->foreign('battle_unit_id')
                ->references('id')
                ->on('battle_units')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('total');
            
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
        Schema::dropIfExists('battle_unit_guerrilla');
    }
}
