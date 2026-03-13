<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('user_name');
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('user_avatar')->nullable();
            $table->string('email')->unique()->nullable();
            $table->integer('type')->default(1)->comment('1: tài khoản người dùng, 2: tài khoản thiết bị (màn hình)');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('token')->nullable();
            $table->string('password');
            $table->tinyInteger('is_delete')->default(0);
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();

            $table->integer('role_id')->nullable();
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
