<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('strategy_journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trader_id')->constrained('traders')->cascadeOnDelete();
            $table->foreignId('strategy_id')->constrained('strategies')->cascadeOnDelete();
            $table->string('action_type'); // 'buy', 'sell', 'sip', 'dip_buy', 'profit_booking'
            $table->string('crypto_coin');
            $table->decimal('units_bought_sold', 20, 8);
            $table->decimal('price_per_unit', 16, 8);
            $table->decimal('total_amount', 15, 2);
            $table->string('reason')->nullable(); // e.g., "Price dropped 10%", "Monthly SIP"
            $table->text('notes')->nullable();
            $table->decimal('current_average_price', 16, 8)->nullable();
            $table->decimal('total_units_after_action', 20, 8)->nullable();
            $table->decimal('total_invested_after_action', 15, 2)->nullable();
            $table->decimal('profit_loss_amount', 15, 2)->nullable();
            $table->decimal('profit_loss_percentage', 8, 4)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('strategy_journals');
    }
};
