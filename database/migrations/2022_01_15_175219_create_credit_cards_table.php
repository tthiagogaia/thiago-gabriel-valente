<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditCardsTable extends Migration
{
    public function up()
    {
        Schema::create('credit_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained();
            $table->string('number');
            $table->string('type');
            $table->string('name');
            $table->string('expiration_date');
            $table->timestamps();

            $table->unique(['user_id', 'number']);
            $table->index('number');
        });
    }

    public function down()
    {
        Schema::dropIfExists('credit_cards');
    }
}
