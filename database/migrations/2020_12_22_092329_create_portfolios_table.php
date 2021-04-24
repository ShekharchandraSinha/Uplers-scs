<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortfoliosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfolios', function (Blueprint $table) {
            $table->id();
            $table->string('profile_photo')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('mobile')->unique();
            $table->bigInteger('user_id')->nullable();
            $table->string('slug')->unique();
            $table->integer('template_id')->nullable();
            $table->string('designation')->nullable();
            $table->string('skill_level')->nullable();
            $table->longText('section_data')->nullable();
            $table->longText('layout')->nullable();
            $table->boolean('active')->default(true);
            $table->boolean('confirmed')->default(false);
            $table->bigInteger('confirmed_by')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('portfolios');
    }
}
