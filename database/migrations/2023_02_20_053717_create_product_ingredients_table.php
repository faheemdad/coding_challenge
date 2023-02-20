<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductIngredientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_ingredients', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->bigInteger('ingredient_id')->unsigned()->nullable();
            $table->bigInteger('qty')->unsigned()->comment('in_gram');
            $table->enum('unit', ['g']);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('ingredient_id')->references('id')->on('ingredients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_ingredients');
    }
}
