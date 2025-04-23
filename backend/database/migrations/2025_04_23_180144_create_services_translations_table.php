<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTranslationsTable extends Migration
{
    public function up()
    {
        Schema::create('services_translations', function (Blueprint $table) {
            $table->id('translation_id');
            $table->unsignedBigInteger('service_id');
            $table->string('language_code', 5);
            $table->text('translated_description');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('services_translations');
    }
}
