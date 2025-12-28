<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DeltaExchangeService
{
    protected string $baseUrl = 'https://api.india.delta.exchange';

    /**
     * Get ticker data for specific symbols or all symbols
     */
    public function getTickers(?array $symbols = null): array
    {
        try {
            $cacheKey = 'delta_tickers_' . md5(json_encode($symbols));
            
            return Cache::remember($cacheKey, 30, function () use ($symbols) {
                $url = $this->baseUrl . '/v2/tickers';
                
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ])
                ->withoutVerifying()
                ->timeout(10)
                ->get($url);
                
                if ($response->successful()) {
                    $data = $response->json();
                    $allTickers = $data['result'] ?? [];
                    
                    // If specific symbols requested, filter and sort by requested order
                    if ($symbols && !empty($symbols)) {
                        $filtered = [];
                        $symbolMap = [];
                        
                        // Create a map of available tickers by symbol
                        foreach ($allTickers as $ticker) {
                            if (isset($ticker['symbol'])) {
                                $symbolMap[$ticker['symbol']] = $ticker;
                            }
                        }
                        
                        // Return tickers in the order of requested symbols
                        foreach ($symbols as $symbol) {
                            if (isset($symbolMap[$symbol])) {
                                $filtered[] = $symbolMap[$symbol];
                            }
                        }
                        
                        return $filtered;
                    }
                    
                    return $allTickers;
                }
                
                \Log::warning('Delta Exchange API returned status: ' . $response->status());
                return [];
            });
        } catch (\Exception $e) {
            \Log::error('Delta Exchange API Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get ticker data for a specific symbol
     */
    public function getTickerBySymbol(string $symbol): ?array
    {
        try {
            $cacheKey = 'delta_ticker_' . $symbol;
            
            return Cache::remember($cacheKey, 30, function () use ($symbol) {
                $url = $this->baseUrl . '/v2/tickers/' . $symbol;
                
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ])
                ->withoutVerifying()
                ->timeout(10)
                ->get($url);
                
                if ($response->successful()) {
                    $data = $response->json();
                    return $data['result'] ?? null;
                }
                
                \Log::warning('Delta Exchange API returned status: ' . $response->status());
                return null;
            });
        } catch (\Exception $e) {
            \Log::error('Delta Exchange API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get popular crypto symbols
     */
    public function getPopularSymbols(): array
    {
        return [
            'BTCUSD', 'ETHUSD', 'BNBUSD', 'SOLUSD', 'ADAUSD',
            'XRPUSD', 'DOGEUSD', 'MATICUSD', 'DOTUSD', 'LTCUSD',
            'AVAXUSD', 'SHIBUSD', 'TRXUSD', 'UNIUSD', 'LINKUSD'
        ];
    }

    /**
     * Format price change percentage
     */
    public function formatPriceChange(float $change): string
    {
        return number_format($change, 2) . '%';
    }

    /**
     * Format price
     */
    public function formatPrice(string|float $price): string
    {
        $price = (float) $price;
        
        if ($price >= 1000) {
            return '₹' . number_format($price, 2);
        } elseif ($price >= 1) {
            return '₹' . number_format($price, 4);
        } else {
            return '₹' . number_format($price, 6);
        }
    }
}
