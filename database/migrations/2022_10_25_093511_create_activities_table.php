<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('audio');
            $table->string('sub');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('type', ['q&a', 'rolgame', 'simple']);
            $table->foreignId('resourcelink_id')->nullable();
            $table->foreign('resourcelink_id')->references('id')->on('resource_links')->onDelete('cascade');
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
        Schema::dropIfExists('activities');
    }
}
