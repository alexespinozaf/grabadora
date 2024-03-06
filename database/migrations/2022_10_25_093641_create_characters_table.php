<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharactersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->enum('genre', ['male', 'female', 'neutral']);
            $table->enum('rol_order', ['r1', 'r2', 'r3']);
            $table->timestamps();
        });
        Schema::create('activity_character', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_id')->nullable();
            $table->foreign('activity_id')->references('id')->on('activities')->onDelete('cascade');
            $table->foreignId('character_id')->nullable();
            $table->foreign('character_id')->references('id')->on('characters')->onDelete('cascade');
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
        Schema::dropIfExists('characters');
        Schema::drop('activity_character');
    }
}
