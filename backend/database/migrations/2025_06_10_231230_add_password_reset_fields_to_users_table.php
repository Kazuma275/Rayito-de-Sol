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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'reset_password_token')) {
                $table->string('reset_password_token')->nullable()->after('remember_token');
            }
            if (!Schema::hasColumn('users', 'reset_password_expires_at')) {
                $table->timestamp('reset_password_expires_at')->nullable()->after('reset_password_token');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'reset_password_token')) {
                $table->dropColumn('reset_password_token');
            }
            if (Schema::hasColumn('users', 'reset_password_expires_at')) {
                $table->dropColumn('reset_password_expires_at');
            }
        });
    }
};
