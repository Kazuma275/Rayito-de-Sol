<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration to add 'name' column to 'users' table.
 * This column is optional and will be placed after the 'username' column.
 * Literalmente se me fue la olla al hacer la migración de usuarios y no le puse el nombre bien.
 */
class AddNameToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name', 100)->nullable()->after('username');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
}
