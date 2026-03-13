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
        Schema::create('logs', function (Blueprint $table) {
            $table->string('id', 55);
            $table->string('user_id', 55)->nullable();
            $table->string('company_id', 55)->nullable();
            $table->string('model', 255)->nullable();
            $table->string('model_id', 55)->nullable();
            $table->string('action', 100)->nullable();
            $table->text('before_action', 100)->nullable();
            $table->text('after_action', 100)->nullable();
            $table->string('module')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_delete')->default(false);
            $table->timestamps();
            $table->softDeletes();
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs');
    }
};
