<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('properties', function (Blueprint $table) {
            // Solo agrega la restricción si no existe
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            // Solo elimina la foreign key, no la columna
            $table->dropForeign(['user_id']);
            // Si quieres también eliminar la columna puedes descomentar la siguiente línea
            // $table->dropColumn('user_id');
        });
    }
};
