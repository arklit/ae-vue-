<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCategoryProductSubCategoryTable extends Migration
{
    public function up()
    {
        Schema::create('product_category_product_sub_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_category_id');
            $table->unsignedBigInteger('product_sub_category_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_category_product_sub_category');
    }
}
