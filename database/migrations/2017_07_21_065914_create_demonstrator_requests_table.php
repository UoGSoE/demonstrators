<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->date('start_date')->nullable();
            $table->float('hours_needed');
            $table->float('hours_training')->nullable();
            $table->unsignedInteger('demonstrators_needed');
            $table->boolean('semester_1')->default(false);
            $table->boolean('semester_2')->default(false);
            $table->boolean('semester_3')->default(false);
            $table->text('skills')->nullable();
            $table->boolean('reminder_sent')->default(false);
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
