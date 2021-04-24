<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreviewPortfoliosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preview_portfolios', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('temp_version_id');
            $table->bigInteger('portfolio_id');
            $table->string('profile_photo')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('name');
            $table->string('email');
            $table->string('mobile');
            $table->string('slug');
            $table->integer('template_id')->nullable();
            $table->string('designation')->nullable();
            $table->string('skill_level')->nullable();
            $table->longText('section_data')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('confirmed')->default(false);
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
        Schema::dropIfExists('preview_portfolios');
    }
}
