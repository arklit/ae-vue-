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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->comment('Активность')->default(true);
            $table->string('title')->comment('Заголовок');
            $table->string('code')->comment('Код');
            $table->integer('sort')->default(0)->comment('Сортировка');
            $table->integer('category_id')->nullable()->comment('id категории');
            $table->integer('sub_category_id')->nullable()->comment('id подкатегории');
            $table->text('description')->nullable()->comment('Описание');
            $table->json('photos')->nullable()->comment('Фотографии');
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
        Schema::dropIfExists('products');
    }
};
