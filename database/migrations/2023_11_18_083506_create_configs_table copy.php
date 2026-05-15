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
        if (Schema::hasTable('configs')) {
            return;
        }

        Schema::create('configs', function (Blueprint $table) {
            $table->string('id', 55)->unique();
            // $table->integer('max_individual_ticket')->nullable()->comment('Tối đa vé cá nhân');
            // $table->integer('max_group_ticket')->nullable()->comment('Tối đa vé nhóm');
            $table->text('content')->nullable()->comment('content');
            $table->boolean('is_delete')->default(0);
            $table->integer('max_device_sale')->nullable();
            $table->json('banners')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('configs');
    }
};
