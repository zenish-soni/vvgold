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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('user_type_id');
            $table->char('name',255)->nullable();
            $table->char('email',255)->nullable();
            $table->char('company_name',255)->nullable();
            $table->char('image',100)->default(DEFAULT_IMG_NAME);
            $table->unsignedBigInteger('phone_number')->unique();
            $table->char('state')->nullable();
            $table->char('city')->nullable();
            $table->char('address')->nullable();
            $table->char('business_photo')->nullable();
            $table->char('second_business_photo')->nullable();
            $table->char('password',255)->nullable();
            $table->char('api_token',255)->nullable();
            $table->text('device_token')->nullable();
            $table->unsignedTinyInteger('is_approved')->default(1)->comment("1=Pending,2=Approved,3=Rejected");
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_type_id')->references('id')->on('lu_user_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
