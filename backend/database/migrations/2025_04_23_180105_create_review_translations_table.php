<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewTranslationsTable extends Migration
{
    public function up()
    {
        Schema::create('review_translations', function (Blueprint $table) {
            $table->id('translation_id');
            $table->unsignedBigInteger('review_id');
            $table->string('language_code', 5);
            $table->text('translated_review');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('review_translations');
    }
}
