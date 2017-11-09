<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssaultReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assault_reports', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('target_id')->unsigned();
            $table->foreign('target_id')
                ->references('id')
                ->on('guerrillas')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->integer('attacker_id')->unsigned();
            $table->foreign('attacker_id')
                ->references('id')
                ->on('guerrillas')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->string('attacker_result_url');
            $table->string('target_result_url');
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
        Schema::dropIfExists('assault_report');
    }
}
