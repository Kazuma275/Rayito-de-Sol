<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('property_day_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->date('date');
            $table->decimal('price', 8, 2)->default(100);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['property_id', 'date']);
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('property_day_prices');
    }
};
