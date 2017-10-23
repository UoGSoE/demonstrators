<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDemonstratorRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demonstrator_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('course_id');
            $table->unsignedInteger('staff_id');
            $table->string('type')->nullable();
            $table->float('hours_needed');
            $table->float('hours_training')->nullable();
            $table->unsignedInteger('demonstrators_needed');
            $table->boolean('semester_1')->default(false);
            $table->boolean('semester_2')->default(false);
            $table->boolean('semester_3')->default(false);
            $table->text('skills')->nullable();
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
        Schema::dropIfExists('demonstrator_requests');
    }
}
