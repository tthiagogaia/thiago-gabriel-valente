<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileImportsTable extends Migration
{
    public function up()
    {
        Schema::create('file_imports', function (Blueprint $table) {
            $table->id();
            $table->string('job_batch_id');
            $table->text('file_path');
            $table->timestamps();

            $table->foreign('job_batch_id')
                ->references('id')
                ->on('job_batches')
                ->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('file_imports');
    }
}
