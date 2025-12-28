<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StrategyJournal extends Model
{
    use HasFactory;

    protected $fillable = [
        'trader_id',
        'strategy_id',
        'action_type', // 'buy', 'sell', 'sip_reminder', 'dip_buy', 'profit_booking'
        'crypto_coin',
        'units_bought_sold',
        'price_per_unit',
        'total_amount',
        'reason',
        'notes',
        'current_average_price',
        'total_units_after_action',
        'total_invested_after_action',
        'profit_loss_amount',
        'profit_loss_percentage',
    ];

    protected $casts = [
        'units_bought_sold' => 'decimal:8',
        'price_per_unit' => 'decimal:8',
        'total_amount' => 'decimal:2',
        'current_average_price' => 'decimal:8',
        'total_units_after_action' => 'decimal:8',
        'total_invested_after_action' => 'decimal:2',
        'profit_loss_amount' => 'decimal:2',
        'profit_loss_percentage' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Trader that owns this journal entry
     */
    public function trader()
    {
        return $this->belongsTo(Trader::class);
    }

    /**
     * Strategy this journal entry belongs to
     */
    public function strategy()
    {
        return $this->belongsTo(Strategy::class);
    }
}
