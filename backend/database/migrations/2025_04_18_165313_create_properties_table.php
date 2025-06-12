<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Propietario
            $table->string('name');
            $table->string('location');
            $table->integer('bedrooms');
            $table->integer('capacity');
            $table->decimal('price', 10, 2);
            $table->longText('image')->nullable();
            $table->text('description');
            $table->json('amenities')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('properties');
    }
}
