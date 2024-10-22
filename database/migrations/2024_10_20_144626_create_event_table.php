<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event', function (Blueprint $table) {
            $table->id(); // Auto-incrementing primary key
            $table->string('name', 255);
            $table->binary('poster'); // For the mediumblob
            $table->string('description', 500);
            $table->string('location', 255);
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('club_id')->constrained('club'); // Foreign key to the club table
            $table->timestamps(); // Optional: adds created_at and updated_at fields
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event');
    }
}

