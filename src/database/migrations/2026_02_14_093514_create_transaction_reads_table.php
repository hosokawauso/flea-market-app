<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionReadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained()->cascadedOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadedOnDelete();
            $table->foreignId('last_read_message_id')->nullable()->constrained('transaction_messages')->nullOnDelete();
            $table->timestamps();

            $table->unique(['transaction_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_reads');
    }
}
