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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('lu_category_id');
            $table->unsignedInteger('lu_sub_category_id')->nullable();
            $table->unsignedInteger('sub_sub_category_id')->nullable();
            $table->unsignedInteger('lu_size_id');
            $table->char('name');
            $table->char('code');
            $table->char('image');
            $table->char('thumb_image');
            $table->json('search_term_ids');
            $table->string('description')->nullable();
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->unsignedInteger('weight');
            $table->unsignedTinyInteger('is_popular')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('lu_size_id')->references('id')->on('lu_sizes')->onDelete('cascade');
            $table->foreign('lu_category_id')->references('id')->on('lu_categories')->onDelete('cascade');
            $table->foreign('lu_sub_category_id')->references('id')->on('lu_sub_categories')->onDelete('cascade');
            $table->foreign('sub_sub_category_id')->references('id')->on('sub_sub_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
