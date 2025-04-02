<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMeetingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('status');
            $table->integer('service_id')
                  ->constrained('services')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->integer('client_id')
                  ->constrained('clients')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');
            $table->timestamp('date_meeting');
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
        Schema::dropIfExists('meetings');
    }
}
