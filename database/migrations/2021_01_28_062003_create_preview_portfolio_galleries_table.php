<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreviewPortfolioGalleriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preview_portfolio_galleries', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('source_id')->nullable();
            $table->bigInteger('temp_version_id');
            $table->string('image_hash');
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
        Schema::dropIfExists('preview_portfolio_galleries');
    }
}
