<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBattleResourceGuerrillaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('battle_resource_guerrilla', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('guerrilla_id')->unsigned();
            $table->foreign('guerrilla_id')
                ->references('id')
                ->on('guerrillas')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('battle_resource_id')->unsigned();
            $table->foreign('battle_resource_id')
                ->references('id')
                ->on('battle_resources')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->double('value');

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
        Schema::dropIfExists('battle_resource_guerrilla');
    }
}
