<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OpenloadMirrors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('openload_mirrors', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('openload_file_id');
            $table->integer('mirror_id');
            $table->integer('episode_id');
            $table->integer('quality');
            $table->tinyInteger('subbed');
            $table->boolean('status')->default(0);
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
        //
    }
}
