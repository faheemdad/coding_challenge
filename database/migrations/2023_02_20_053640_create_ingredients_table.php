<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngredientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('qty')->unsigned();
            $table->enum('unit', ['kg']);
            $table->double('initial_stock_level', 10, 2)->comment('in_gram');
            $table->double('current_stock_level', 10, 2)->comment('in_gram');
            $table->enum('is_low_stock_notified', ['1','0'])->default('0');
            $table->dateTime('is_low_stock_notified_date_time')->nullable();
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
        Schema::dropIfExists('ingredients');
    }
}
