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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('quantity');
            $table->char('category_name')->nullable();
            $table->char('sub_category_name')->nullable();
            $table->char('sub_sub_category_name')->nullable();
            $table->char('size_name')->nullable();
            $table->char('code')->nullable();
            $table->char('image')->nullable();
            $table->char('thumb_image')->nullable();
            $table->json('search_term_ids')->nullable();
            $table->string('description')->nullable();
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->unsignedInteger('weight');
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
        Schema::dropIfExists('order_details');
    }
};
