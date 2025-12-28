<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Strategy extends Model
{
    use HasFactory;

    protected $fillable = [
        'trader_id',
        'name',
        'crypto_coin',
        'initial_investment',
        'initial_price',
        'monthly_sip_amount',
        'profit_target_amount',
        'buy_dip_percentage',
        'buy_dip_amount',
        'current_average_price',
        'current_holding_amount',
        'total_invested_amount',
        'units_held',
        'status',
    ];

    protected $casts = [
        'initial_investment' => 'decimal:2',
        'initial_price' => 'decimal:8',
        'monthly_sip_amount' => 'decimal:2',
        'profit_target_amount' => 'decimal:2',
        'buy_dip_percentage' => 'decimal:2',
        'buy_dip_amount' => 'decimal:2',
        'current_average_price' => 'decimal:8',
        'current_holding_amount' => 'decimal:2',
        'total_invested_amount' => 'decimal:2',
        'units_held' => 'decimal:8',
    ];

    /**
     * Trader that owns this strategy
     */
    public function trader()
    {
        return $this->belongsTo(Trader::class);
    }

    /**
     * Journal entries for this strategy
     */
    public function journalEntries()
    {
        return $this->hasMany(StrategyJournal::class);
    }

    /**
     * Calculate current profit/loss based on current price
     */
    public function calculateCurrentPnL($currentPrice)
    {
        $currentValue = $this->units_held * $currentPrice;
        return $currentValue - $this->total_invested_amount;
    }

    /**
     * Get current profit percentage
     */
    public function getCurrentPnLPercentage($currentPrice)
    {
        if ($this->total_invested_amount == 0) {
            return 0;
        }
        $pnl = $this->calculateCurrentPnL($currentPrice);
        return ($pnl / $this->total_invested_amount) * 100;
    }
}
