<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('series', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('kit_id')->unique();
            $table->string('name');
            $table->string('english_name')->nullable();
            $table->string('alternate_name')->nullable();
            $table->string('slug');
            $table->string('poster')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->longText('description')->nullable();
            $table->string('status')->nullable();
            $table->string('type', 20)->nullable();
            $table->string('genres')->nullable();
            $table->smallInteger('total_eps')->nullable();
            $table->string('youtube_trailer_id')->nullable();
            $table->integer('popularity')->nullable();
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
        Schema::dropIfExists('series');
    }
}
