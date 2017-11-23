<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('username');
            $table->string('email')->unique();
            $table->string('password')->nullable()->default(null);
            $table->string('forenames');
            $table->string('surname');
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_student')->default(true);
            $table->boolean('returned_rtw')->default(false);
            $table->boolean('rtw_notified')->default(false);
            $table->date('rtw_start')->nullable();
            $table->date('rtw_end')->nullable();
            $table->boolean('has_contract')->default(false);
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('hide_blurb')->default(false);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
