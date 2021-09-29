<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->increments('id');
            $table->string('full_name',255);
            $table->integer('class')->nullable();
            $table->string('gender',25)->nullable();
            $table->integer('fees')->nullable();
            $table->string('photo')->nullable();
            $table->integer('entry_year')->nullable();
            $table->string('dob',15)->nullable();
            $table->string('phone',30)->unique();
            $table->string('current_address',255)->nullable();
            $table->string('guardian_name',100)->nullable();
            $table->string('guardian_mobile')->nullable();
            $table->string('aadhar_no')->unique();
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
        Schema::dropIfExists('students');
    }
}
