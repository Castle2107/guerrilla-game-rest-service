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
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            $table->enum('guerrilla_type', ['China', 'USMC', 'MEC']);
            $table->integer('money')->default(300)->unsigned();
            $table->integer('people')->default(50)->unsigned();
            $table->integer('oil')->default(300)->unsigned();
            $table->integer('tank')->default(0)->unsigned();
            $table->integer('assault')->default(0)->unsigned();
            $table->integer('engineer')->default(0)->unsigned();
            $table->integer('bunker')->default(0)->unsigned();
            $table->integer('ranking_score')->default(0)->unsigned();
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
