<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisciplinesUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disciplines_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discipline_id')
                  ->constrained('disciplines')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->foreignId('therapeute_id')
                  ->constrained('therapeutes')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
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
        Schema::dropIfExists('disciplines_users');
    }
}
