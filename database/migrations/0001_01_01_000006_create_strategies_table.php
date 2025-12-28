<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('strategies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trader_id')->constrained('traders')->cascadeOnDelete();
            $table->string('name'); // e.g., "Crypto Ka Jungle"
            $table->string('crypto_coin'); // e.g., "BTC", "ETH"
            $table->decimal('initial_investment', 15, 2); // 10000.00
            $table->decimal('initial_price', 16, 8); // Current price when started
            $table->decimal('monthly_sip_amount', 15, 2); // 1000.00
            $table->decimal('profit_target_amount', 15, 2); // 1000.00
            $table->decimal('buy_dip_percentage', 5, 2); // 10.00%
            $table->decimal('buy_dip_amount', 15, 2); // 1000.00
            $table->decimal('current_average_price', 16, 8);
            $table->decimal('current_holding_amount', 15, 2); // Current market value
            $table->decimal('total_invested_amount', 15, 2);
            $table->decimal('units_held', 20, 8);
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('strategies');
    }
};
