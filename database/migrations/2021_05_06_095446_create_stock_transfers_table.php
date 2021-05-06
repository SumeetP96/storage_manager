<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('transfer_type_id')->constrained('transfer_types')->restrictOnDelete();
            $table->foreignId('from_godown_id')->constrained('godowns')->restrictOnDelete();
            $table->foreignId('to_godown_id')->constrained('godowns')->restrictOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->bigInteger('quantity');
            $table->string('order_no', 20)->nullable();
            $table->string('invoice_no', 20)->nullable();
            $table->string('eway_bill_no', 20)->nullable();
            $table->string('delivery_slip_no', 20)->nullable();
            $table->string('transport_details', 100)->nullable();
            $table->foreignId('agent_id')->nullable()->constrained()->restrictOnDelete();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('stock_transfers');
    }
}
