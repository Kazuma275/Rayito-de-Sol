<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->integer('bedrooms');
            $table->integer('capacity');
            $table->decimal('price', 10, 2);
            $table->longText('image')->nullable(); // <-- ¡CAMBIADO!
            $table->text('description');
            $table->json('amenities')->nullable();
            $table->string('status')->default('active');
            // $table->string('statusText')->default('Activo'); // <--- ELIMINAR si no usas este campo
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
        Schema::dropIfExists('properties');
    }
};
