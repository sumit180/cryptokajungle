<?php

namespace App\Policies;

use App\Models\Trader;
use App\Models\Strategy;

class StrategyPolicy
{
    /**
     * Determine if the trader can view the strategy
     */
    public function view(Trader $trader, Strategy $strategy): bool
    {
        return $trader->id === $strategy->trader_id;
    }

    /**
     * Determine if the trader can update the strategy
     */
    public function update(Trader $trader, Strategy $strategy): bool
    {
        return $trader->id === $strategy->trader_id;
    }

    /**
     * Determine if the trader can delete the strategy
     */
    public function delete(Trader $trader, Strategy $strategy): bool
    {
        return $trader->id === $strategy->trader_id;
    }
}
