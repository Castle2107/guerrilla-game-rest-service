<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBattleUnitDamageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('battle_unit_damage', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('battle_unit_attacker_id')->unsigned();
            $table->foreign('battle_unit_attacker_id')
                ->references('id')
                ->on('battle_units')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('battle_unit_target_id')->unsigned();
            $table->foreign('battle_unit_target_id')
                ->references('id')
                ->on('battle_units')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('damage_value');
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
        Schema::dropIfExists('battle_unit_damage');
    }
}
