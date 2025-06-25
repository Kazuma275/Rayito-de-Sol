<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id(); // Usa 'id' estándar de Laravel para la PK
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->decimal('rating', 2, 1); // permite valores con un decimal, ej: 4.5
            $table->text('review');
            $table->string('language', 10)->nullable();
            $table->string('author_name')->nullable();
            $table->string('author_image')->nullable();
            $table->timestamp('review_date')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
