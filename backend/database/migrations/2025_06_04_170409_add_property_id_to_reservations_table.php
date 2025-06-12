<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Ya no hace falta agregar property_id, la columna ya existe.
        // $table->unsignedBigInteger('property_id')->after('user_id');
        // $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
    }

    public function down()
    {
        // $table->dropForeign(['property_id']);
        // $table->dropColumn('property_id');
    }
};
