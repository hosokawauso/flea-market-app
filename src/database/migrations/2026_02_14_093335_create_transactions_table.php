<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('open'); // open/completed
            $table->timestamp('last_message_at')->nullable();
            $table->timestamp('completed_by_buyer_at')->nullable();
            $table->timestamp('completed_by_seller_at')->nullable();
            $table->unsignedTinyInteger('buyer_rating')->nullable();  // 購入者→出品者
            $table->timestamp('buyer_rated_at')->nullable();
            $table->unsignedTinyInteger('seller_rating')->nullable(); // 出品者→購入者
            $table->timestamp('seller_rated_at')->nullable();
            $table->timestamps();

            $table->unique('purchase_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
