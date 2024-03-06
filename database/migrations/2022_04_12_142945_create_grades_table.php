<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGradesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->decimal('grade', 3, 2);
            $table->integer('group_id')->nullable();
            $table -> enum('state',['published','local'])->default('local');
            $table->string('comment')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('resourcelink_id')->nullable();
            $table->foreign('resourcelink_id')->references('id')->on('resource_links')->onDelete('cascade');
            $table->foreignId('recording_id')->nullable();
            $table->foreign('recording_id')->references('id')->on('recordings')->onDelete('cascade');
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
        Schema::dropIfExists('grades');
    }
}
