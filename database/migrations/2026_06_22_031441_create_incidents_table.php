<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id('ticketid'); // ID is the primary key
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('unreceived');
            $table->string('department')->default('');
            $table->string('location');
            $table->text('description')->default('');
            $table->time('ttr')->default('00:00:00');
            $table->float('rating')->nullable();
            $table->string('ratingdetails')->nullable();
            $table->string('compensationtype')->nullable();
            $table->float('compensationvalue')->nullable();
            $table->text('compensationdetails')->nullable();
            $table->boolean('compensationapproval')->default(false);
            $table->timestamps();
            $table->string('image_path')->nullable();//image
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
