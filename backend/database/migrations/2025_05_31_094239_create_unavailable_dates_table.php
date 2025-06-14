<?php

// database/migrations/xxxx_xx_xx_create_unavailable_dates_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('unavailable_dates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->date('date');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->unique(['property_id', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('unavailable_dates');
    }
};

?>
