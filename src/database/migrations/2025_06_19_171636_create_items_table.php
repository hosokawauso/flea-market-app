<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('item_name');
            $table->string('brand_name')->nullable();
            $table->integer('price');
            $table->text('description');
            $table->string('item_img');
            $table->tinyInteger('condition')->comment('1:良好, 2:目立った傷や汚れなし, 3:やや傷や汚れあり, 4:状態が悪い');
            $table->boolean('is_sold')->default(false);
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
        Schema::dropIfExists('items');
    }
}
