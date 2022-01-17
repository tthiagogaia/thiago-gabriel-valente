<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->boolean('checked')->default(false);
            $table->text('description');
            $table->text('interest')->nullable();
            $table->timestampTz('date_of_birth')->nullable();
            $table->string('email')->unique();
            $table->string('account')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
