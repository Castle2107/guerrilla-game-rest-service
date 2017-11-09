<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGuerrillaTypeResourceIncrementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('guerrilla_type_resource_increment', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('guerrilla_type_id')->unsigned();
            $table->foreign('guerrilla_type_id')
                ->references('id')
                ->on('guerrilla_types')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('battle_resource_id')->unsigned();
            $table->foreign('battle_resource_id')
                ->references('id')
                ->on('battle_resources')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('increment_value');
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
        Schema::dropIfExists('guerrilla_type_resource_increment');
    }
}
