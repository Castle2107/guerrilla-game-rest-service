<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBattleUnitResourceCostTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('battle_unit_resource_cost', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('battle_unit_id')->unsigned();
            $table->foreign('battle_unit_id')
                ->references('id')
                ->on('battle_units')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('battle_resource_id')->unsigned();
            $table->foreign('battle_resource_id')
                ->references('id')
                ->on('battle_resources')
                ->onUpdate('cascade')
                ->onDelete('cascade');


            $table->integer('cost');
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
        Schema::dropIfExists('battle_unit_resource_cost');
    }
}
