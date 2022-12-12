<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lu_sub_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('lu_category_id');
            $table->char('name');
            $table->char('image');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('lu_category_id')->references('id')->on('lu_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sizes');
    }
};
