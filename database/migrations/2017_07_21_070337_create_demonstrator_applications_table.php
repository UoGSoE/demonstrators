<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemonstratorApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demonstrator_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('request_id');
            $table->boolean('is_accepted')->default(false);
            $table->boolean('student_confirms')->default(false);
            $table->boolean('is_new')->default(true);
            $table->boolean('student_responded')->default(false);
            $table->boolean('academic_notified')->default(false);
            $table->boolean('academic_seen')->default(false);
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
        Schema::dropIfExists('demonstrator_applications');
    }
}
