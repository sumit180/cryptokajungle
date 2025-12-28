<?php

namespace App\Services;

use App\Models\Strategy;
use App\Models\StrategyJournal;
use Decimal\Decimal;

class CryptoKaJungleService
{
    /**
     * Initialize strategy with lump sum investment
     */
    public function initializeStrategy($strategy, $currentPrice)
    {
        $units = $strategy->initial_investment / $currentPrice;
        
        $strategy->update([
            'current_average_price' => $currentPrice,
            'current_holding_amount' => $strategy->initial_investment,
            'total_invested_amount' => $strategy->initial_investment,
            'units_held' => $units,
        ]);

        // Log initial purchase
        $this->logTransaction($strategy, 'buy', $units, $currentPrice, $strategy->initial_investment, 'Initial lump sum investment');
    }

    /**
     * Check and execute dip buying strategy
     * If price drops by X%, buy more cryptos
     */
    public function checkAndExecuteDipBuy($strategy, $currentPrice)
    {
        // Safety check
        if ($currentPrice <= 0 || $strategy->initial_price <= 0) {
            return;
        }
        
        $dropPercentage = (($strategy->initial_price - $currentPrice) / $strategy->initial_price) * 100;
        
        if ($dropPercentage > 0 && fmod($dropPercentage, $strategy->buy_dip_percentage) < 1) {
            // Price has dropped in multiples of buy_dip_percentage
            $dips = floor($dropPercentage / $strategy->buy_dip_percentage);
            $amountToBuy = $dips * $strategy->buy_dip_amount;
            
            $unitsToBuy = $amountToBuy / $currentPrice;
            
            $this->executeTransaction($strategy, 'dip_buy', $unitsToBuy, $currentPrice, $amountToBuy, "Price dropped {$dropPercentage}%");
        }
    }

    /**
     * Check profit target and execute profit booking
     * If current profit >= target profit amount, sell
     */
    public function checkAndExecuteProfitBooking($strategy, $currentPrice)
    {
        $currentValue = $strategy->units_held * $currentPrice;
        $unrealizedProfit = $currentValue - $strategy->total_invested_amount;
        
        if ($unrealizedProfit >= $strategy->profit_target_amount) {
            // Calculate units to sell to realize target profit
            $unitsToSell = $strategy->profit_target_amount / $currentPrice;
            $saleAmount = $unitsToSell * $currentPrice;
            
            $this->executeTransaction($strategy, 'profit_booking', $unitsToSell, $currentPrice, $saleAmount, 'Profit target reached');
        }
    }

    /**
     * Execute monthly SIP investment
     */
    public function executeSIPInvestment($strategy, $currentPrice)
    {
        $unitsToBuy = $strategy->monthly_sip_amount / $currentPrice;
        $this->executeTransaction($strategy, 'sip', $unitsToBuy, $currentPrice, $strategy->monthly_sip_amount, "Monthly SIP investment of ₹{$strategy->monthly_sip_amount}");
    }

    /**
     * Execute a transaction and update strategy
     */
    public function executeTransaction($strategy, $actionType, $units, $price, $amount, $reason)
    {
        if ($actionType === 'buy' || $actionType === 'dip_buy' || $actionType === 'sip') {
            // Buying: increase units and update average price
            $newTotalInvested = $strategy->total_invested_amount + $amount;
            $newUnits = $strategy->units_held + $units;
            $newAveragePrice = $newTotalInvested / $newUnits;
            
            $strategy->update([
                'units_held' => $newUnits,
                'total_invested_amount' => $newTotalInvested,
                'current_average_price' => $newAveragePrice,
                'current_holding_amount' => $newUnits * $price,
            ]);
        } elseif ($actionType === 'sell' || $actionType === 'profit_booking') {
            // Selling: decrease units only, total invested stays the same
            $newUnits = $strategy->units_held - $units;
            
            // Total invested amount NEVER changes when booking profit
            // Your original investment is ₹15,000, it stays ₹15,000
            $newTotalInvested = $strategy->total_invested_amount;
            
            // Average price stays the same (your original cost per unit)
            $newAveragePrice = $strategy->current_average_price;
            
            // Calculate profit/loss for this transaction
            $costBasisOfSoldUnits = $units * $strategy->current_average_price;
            $profitLoss = $amount - $costBasisOfSoldUnits;
            
            $strategy->update([
                'units_held' => $newUnits,
                'total_invested_amount' => $newTotalInvested,
                'current_average_price' => $newAveragePrice,
                'current_holding_amount' => $newUnits * $price,
            ]);
            
            // Log the transaction with realized profit
            $this->logTransaction($strategy, $actionType, $units, $price, $amount, $reason, $profitLoss);
            return;
        }

        // Log the transaction
        $this->logTransaction($strategy, $actionType, $units, $price, $amount, $reason);
    }

    /**
     * Log a transaction in strategy journal
     */
    public function logTransaction($strategy, $actionType, $units, $price, $amount, $reason, $realizedProfitLoss = null)
    {
        // For profit booking, use the passed realized profit/loss
        // For other actions, calculate unrealized P&L
        if ($realizedProfitLoss !== null) {
            $profitLoss = $realizedProfitLoss;
            $profitLossPercentage = $amount > 0 ? ($profitLoss / $amount) * 100 : 0;
        } else {
            $currentValue = $strategy->units_held * $price;
            $profitLoss = $currentValue - $strategy->total_invested_amount;
            $profitLossPercentage = $strategy->total_invested_amount > 0 ? ($profitLoss / $strategy->total_invested_amount) * 100 : 0;
        }

        StrategyJournal::create([
            'trader_id' => $strategy->trader_id,
            'strategy_id' => $strategy->id,
            'action_type' => $actionType,
            'crypto_coin' => $strategy->crypto_coin,
            'units_bought_sold' => $units,
            'price_per_unit' => $price,
            'total_amount' => $amount,
            'reason' => $reason,
            'current_average_price' => $strategy->current_average_price,
            'total_units_after_action' => $strategy->units_held,
            'total_invested_after_action' => $strategy->total_invested_amount,
            'profit_loss_amount' => $profitLoss,
            'profit_loss_percentage' => $profitLossPercentage,
        ]);
    }

    /**
     * Get strategy dashboard data
     */
    public function getStrategyDashboard($strategy, $currentPrice)
    {
        $currentValue = $strategy->units_held * $currentPrice;
        $unrealizedProfit = $currentValue - $strategy->total_invested_amount;
        $profitPercentage = $strategy->total_invested_amount > 0 ? ($unrealizedProfit / $strategy->total_invested_amount) * 100 : 0;
        
        $dropFromInitial = (($strategy->initial_price - $currentPrice) / $strategy->initial_price) * 100;
        $nextSIPDate = now()->addMonth()->startOfMonth()->addDay(); // 1st of next month
        
        return [
            'strategy' => $strategy,
            'currentPrice' => $currentPrice,
            'currentValue' => $currentValue,
            'unrealizedProfit' => $unrealizedProfit,
            'profitPercentage' => $profitPercentage,
            'dropFromInitial' => $dropFromInitial,
            'nextSIPDate' => $nextSIPDate,
            'profitTargetProgress' => ($unrealizedProfit / $strategy->profit_target_amount) * 100,
        ];
    }

    /**
     * Fetch live price from CoinGecko API
     */
    public function fetchLivePrice($cryptoCoin)
    {
        try {
            // Map coin symbols to CoinGecko IDs
            $coinMap = [
                'BTC' => 'bitcoin',
                'ETH' => 'ethereum',
                'BNB' => 'binancecoin',
                'XRP' => 'ripple',
                'ADA' => 'cardano',
                'SOL' => 'solana',
                'DOGE' => 'dogecoin',
                'DOT' => 'polkadot',
                'MATIC' => 'matic-network',
                'SHIB' => 'shiba-inu',
                'AVAX' => 'avalanche-2',
                'LTC' => 'litecoin',
                'TRX' => 'tron',
                'UNI' => 'uniswap'
            ];
            
            $coinSymbol = strtoupper($cryptoCoin);
            $coinId = $coinMap[$coinSymbol] ?? strtolower($cryptoCoin);
            
            $url = "https://api.coingecko.com/api/v3/simple/price?ids={$coinId}&vs_currencies=inr";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Accept: application/json',
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                if (isset($data[$coinId]['inr'])) {
                    return (float) $data[$coinId]['inr'];
                }
            }
            
            // Log error for debugging
            if ($httpCode !== 200) {
                \Log::warning("CoinGecko API failed for {$coinId}. HTTP Code: {$httpCode}, Error: {$curlError}");
            }
            
            // Return 0 if API fails
            return 0;
        } catch (\Exception $e) {
            \Log::error('Failed to fetch live price: ' . $e->getMessage());
            return 0;
        }
    }
}
