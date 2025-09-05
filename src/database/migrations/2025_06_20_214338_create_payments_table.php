<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            /* $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnUpdate()->restrictOnDelete(); */
            $table->foreignId('purchase_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();

            $table->integer('amount');
            $table->string('method', 20);
            $table->string('currency', 10)->default('jpy');
            $table->string('status', 32)->default('pending');

            $table->string('checkout_session_id', 255)->unique()->nullable();
            $table->string('payment_intent_id', 255)->unique()->nullable();
           
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();
            /* $table->index(['user_id', 'item_id']); */
            $table->index(['status']);
            $table->index(['method']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}

