<?php

use App\Services\DeltaExchangeService;
use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component {
    public array $allTickers = [];
    public array $filteredTickers = [];
    public bool $loading = true;
    public bool $error = false;
    public string $errorMessage = '';
    public string $search = '';
    public int $currentPage = 1;
    public int $perPage = 10;
    public int $totalPages = 1;
    public string $currency = 'INR'; // 'INR' or 'USD'
    public float $exchangeRate = 85; // Current USD to INR rate

    public function mount(DeltaExchangeService $deltaExchange): void
    {
        $this->loadTickers($deltaExchange);
    }

    public function updatedSearch(): void
    {
        $this->currentPage = 1;
        $this->filterTickers();
    }

    public function toggleCurrency(): void
    {
        $this->currency = $this->currency === 'INR' ? 'USD' : 'INR';
    }

    public function goToPage(int $page): void
    {
        if ($page >= 1 && $page <= $this->totalPages) {
            $this->currentPage = $page;
            $this->filterTickers();
        }
    }

    public function nextPage(): void
    {
        if ($this->currentPage < $this->totalPages) {
            $this->currentPage++;
            $this->filterTickers();
        }
    }

    public function previousPage(): void
    {
        if ($this->currentPage > 1) {
            $this->currentPage--;
            $this->filterTickers();
        }
    }

    public function handleRefresh(): void
    {
        $this->loading = true;
        $this->currentPage = 1;
        
        try {
            $deltaExchange = app(DeltaExchangeService::class);
            $this->loadTickers($deltaExchange);
            $this->filterTickers();
        } catch (\Exception $e) {
            $this->error = true;
            $this->errorMessage = 'Failed to refresh: ' . $e->getMessage();
            \Log::error('Refresh error: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    #[On('refresh-crypto-tickers')]
    public function autoRefresh(): void
    {
        $this->handleRefresh();
    }

    public function refresh(DeltaExchangeService $deltaExchange): void
    {
        $this->handleRefresh();
    }

    protected function loadTickers(DeltaExchangeService $deltaExchange): void
    {
        try {
            // Get ALL tickers and popular symbols
            $allData = $deltaExchange->getTickers(null); // Get all tickers
            $popularSymbols = $deltaExchange->getPopularSymbols();
            
            if (empty($allData)) {
                $this->error = true;
                $this->errorMessage = 'No data received from API. Please try again later.';
                $this->loading = false;
                return;
            }
            
            // Sort tickers: popular ones first, then others
            $sortedData = [];
            $allDataMap = [];
            
            // Create a map of all tickers
            foreach ($allData as $ticker) {
                $allDataMap[$ticker['symbol'] ?? ''] = $ticker;
            }
            
            // Add popular symbols first
            foreach ($popularSymbols as $symbol) {
                if (isset($allDataMap[$symbol])) {
                    $sortedData[] = $allDataMap[$symbol];
                    unset($allDataMap[$symbol]);
                }
            }
            
            // Add remaining tickers
            $sortedData = array_merge($sortedData, array_values($allDataMap));
            
            $this->allTickers = collect($sortedData)->map(function ($ticker) use ($deltaExchange) {
                $markPrice = (float) ($ticker['mark_price'] ?? 0);
                $open = (float) ($ticker['open'] ?? $markPrice);
                $priceChange = $open > 0 ? (($markPrice - $open) / $open * 100) : 0;
                
                return [
                    'symbol' => $ticker['symbol'] ?? 'N/A',
                    'name' => str_replace('USD', '', $ticker['symbol'] ?? 'N/A'),
                    'raw_price' => $markPrice,
                    'price' => $deltaExchange->formatPrice($markPrice),
                    'change' => $priceChange,
                    'change_formatted' => $deltaExchange->formatPriceChange($priceChange),
                    'high' => $deltaExchange->formatPrice($ticker['high'] ?? 0),
                    'raw_high' => (float) ($ticker['high'] ?? 0),
                    'low' => $deltaExchange->formatPrice($ticker['low'] ?? 0),
                    'raw_low' => (float) ($ticker['low'] ?? 0),
                    'volume' => number_format($ticker['volume'] ?? 0),
                    'open' => $deltaExchange->formatPrice($open),
                    'raw_open' => $open,
                ];
            })->toArray();
            
            $this->filterTickers();
            $this->error = false;
        } catch (\Exception $e) {
            $this->error = true;
            $this->errorMessage = 'Failed to load market data. ' . $e->getMessage();
            \Log::error('Failed to load crypto tickers: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    protected function convertPrice(float $priceUsd): float
    {
        return $this->currency === 'INR' ? $priceUsd * $this->exchangeRate : $priceUsd;
    }

    protected function formatDisplayPrice(float $priceUsd): string
    {
        $convertedPrice = $this->convertPrice($priceUsd);
        $symbol = $this->currency === 'INR' ? '₹' : '$';
        
        if ($convertedPrice >= 1) {
            return $symbol . number_format($convertedPrice, 2);
        } else {
            return $symbol . number_format($convertedPrice, 6);
        }
    }

    protected function filterTickers(): void
    {
        $filtered = collect($this->allTickers);
        
        if (!empty($this->search)) {
            $searchLower = strtolower($this->search);
            $filtered = $filtered->filter(function ($ticker) use ($searchLower) {
                return str_contains(strtolower($ticker['name']), $searchLower) ||
                       str_contains(strtolower($ticker['symbol']), $searchLower);
            });
        }
        
        $this->totalPages = (int) ceil($filtered->count() / $this->perPage);
        if ($this->currentPage > $this->totalPages && $this->totalPages > 0) {
            $this->currentPage = $this->totalPages;
        }
        
        $offset = ($this->currentPage - 1) * $this->perPage;
        $this->filteredTickers = $filtered->slice($offset, $this->perPage)->values()->toArray();
    }

    public function getPaginatedTickers(): array
    {
        return $this->filteredTickers;
    }
}; ?>

<div class="w-full max-w-7xl mx-auto px-4">
    <style>
        .crypto-ticker-wrapper {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        .crypto-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 24px;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }
        .crypto-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
            border-color: #10b981;
        }
        .price-positive {
            color: #10b981;
        }
        .price-negative {
            color: #ef4444;
        }
        .badge-positive {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
        }
        .badge-negative {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #991b1b;
        }
        .refresh-btn {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .refresh-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
        }
        .spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #10b981;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .error-card {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            border: 2px solid #fca5a5;
            border-radius: 16px;
            padding: 32px;
            text-align: center;
        }
        .crypto-name {
            font-size: 24px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 8px;
        }
        .crypto-price {
            font-size: 32px;
            font-weight: 900;
            margin: 12px 0;
            letter-spacing: -0.5px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }
        .stat-item {
            text-align: left;
        }
        .stat-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .stat-value {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin-top: 4px;
        }
    </style>

    <div class="crypto-ticker-wrapper">
        @if($loading)
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 60px 20px;">
                <div class="spinner"></div>
                <p style="margin-top: 20px; color: #64748b; font-size: 16px; font-weight: 500;">Loading live crypto prices...</p>
            </div>
        @elseif($error)
            <div class="error-card">
                <div style="width: 64px; height: 64px; background: linear-gradient(135deg, #fee2e2, #fca5a5); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 32px; color: #dc2626;"></i>
                </div>
                <h3 style="font-size: 24px; font-weight: 700; color: #991b1b; margin-bottom: 12px;">Unable to Load Market Data</h3>
                <p style="color: #7f1d1d; font-size: 14px; margin-bottom: 24px;">{{ $errorMessage }}</p>
                <button wire:click="refresh" class="refresh-btn">
                    <i class="fas fa-sync-alt"></i>
                    Try Again
                </button>
            </div>
        @else
            <!-- Header -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
                <div>
                    <h2 style="font-size: 32px; font-weight: 900; color: #1e293b; margin-bottom: 8px; display: flex; align-items: center; gap: 12px;">
                        <span style="width: 12px; height: 12px; background: #10b981; border-radius: 50%; display: inline-block; animation: pulse 2s ease-in-out infinite;"></span>
                        Live Crypto Prices
                    </h2>
                    <p style="color: #64748b; font-size: 14px;">Real-time market data from Delta Exchange India • Showing {{ count($filteredTickers) }} of {{ count($allTickers) }} cryptos</p>
                </div>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <button 
                        wire:click="toggleCurrency"
                        style="padding: 10px 20px; background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; gap: 8px;"
                        onmouseover="this.style.transform='scale(1.05)'"
                        onmouseout="this.style.transform='scale(1)'"
                    >
                        <i class="fas fa-exchange-alt"></i>
                        {{ $currency === 'INR' ? '$ USD' : '₹ INR' }}
                    </button>
                    <button wire:click="handleRefresh" wire:loading.attr="disabled" class="refresh-btn">
                        <span wire:loading.remove>
                            <i class="fas fa-sync-alt"></i>
                            Refresh Prices
                        </span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin"></i>
                            Refreshing...
                        </span>
                    </button>
                </div>
            </div>

            <!-- Search Bar -->
            <div style="margin-bottom: 32px;">
                <div style="position: relative; max-width: 500px;">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search cryptocurrency (e.g., BTC, Ethereum)..."
                        style="width: 100%; padding: 16px 48px 16px 48px; border: 2px solid #e2e8f0; border-radius: 12px; font-size: 15px; transition: all 0.3s ease; outline: none;"
                        onfocus="this.style.borderColor='#10b981'; this.style.boxShadow='0 0 0 3px rgba(16, 185, 129, 0.1)'"
                        onblur="this.style.borderColor='#e2e8f0'; this.style.boxShadow='none'"
                    />
                    <i class="fas fa-search" style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 16px;"></i>
                    @if($search)
                        <button 
                            wire:click="$set('search', '')"
                            style="position: absolute; right: 18px; top: 50%; transform: translateY(-50%); background: #fee2e2; color: #991b1b; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
                            onmouseover="this.style.background='#fecaca'"
                            onmouseout="this.style.background='#fee2e2'"
                        >
                            <i class="fas fa-times" style="font-size: 12px;"></i>
                        </button>
                    @endif
                </div>
            </div>

            @if(count($filteredTickers) > 0)
                <!-- Crypto Cards Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px; margin-bottom: 32px;">
                    @foreach($filteredTickers as $ticker)
                        <div class="crypto-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                                <div>
                                    <div class="crypto-name">{{ $ticker['name'] }}</div>
                                    <div style="font-size: 11px; color: #94a3b8; font-weight: 600;">{{ $ticker['symbol'] }}</div>
                                </div>
                                <span style="padding: 6px 12px; border-radius: 20px; font-size: 13px; font-weight: 700; {{ $ticker['change'] >= 0 ? 'background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #065f46;' : 'background: linear-gradient(135deg, #fee2e2, #fecaca); color: #991b1b;' }}">
                                    {{ $ticker['change'] >= 0 ? '↑' : '↓' }} {{ $ticker['change_formatted'] }}
                                </span>
                            </div>
                            
                            <div class="crypto-price {{ $ticker['change'] >= 0 ? 'price-positive' : 'price-negative' }}">
                                {{ $this->formatDisplayPrice($ticker['raw_price']) }}
                            </div>

                            <div class="stats-grid">
                                <div class="stat-item">
                                    <div class="stat-label">24h High</div>
                                    <div class="stat-value">{{ $this->formatDisplayPrice($ticker['raw_high']) }}</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">24h Low</div>
                                    <div class="stat-value">{{ $this->formatDisplayPrice($ticker['raw_low']) }}</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">Open Price</div>
                                    <div class="stat-value">{{ $this->formatDisplayPrice($ticker['raw_open']) }}</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">Volume</div>
                                    <div class="stat-value">{{ $ticker['volume'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($totalPages > 1)
                    <div style="display: flex; justify-content: center; align-items: center; gap: 12px; margin-bottom: 32px; flex-wrap: wrap;">
                        <button 
                            wire:click="previousPage"
                            @if($currentPage == 1) disabled @endif
                            style="padding: 10px 20px; background: {{ $currentPage == 1 ? '#f1f5f9' : 'linear-gradient(135deg, #10b981, #059669)' }}; color: {{ $currentPage == 1 ? '#94a3b8' : 'white' }}; border: none; border-radius: 8px; font-weight: 600; cursor: {{ $currentPage == 1 ? 'not-allowed' : 'pointer' }}; transition: all 0.3s;"
                            @if($currentPage != 1)
                                onmouseover="this.style.transform='scale(1.05)'"
                                onmouseout="this.style.transform='scale(1)'"
                            @endif
                        >
                            <i class="fas fa-chevron-left"></i> Previous
                        </button>

                        <div style="display: flex; gap: 8px; align-items: center;">
                            @php
                                $start = max(1, $currentPage - 2);
                                $end = min($totalPages, $currentPage + 2);
                            @endphp

                            @if($start > 1)
                                <button 
                                    wire:click="goToPage(1)"
                                    style="padding: 10px 16px; background: white; color: #1e293b; border: 2px solid #e2e8f0; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                                    onmouseover="this.style.borderColor='#10b981'; this.style.color='#10b981'"
                                    onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#1e293b'"
                                >
                                    1
                                </button>
                                @if($start > 2)
                                    <span style="color: #94a3b8; font-weight: 600;">...</span>
                                @endif
                            @endif

                            @for($i = $start; $i <= $end; $i++)
                                <button 
                                    wire:click="goToPage({{ $i }})"
                                    style="padding: 10px 16px; background: {{ $i == $currentPage ? 'linear-gradient(135deg, #10b981, #059669)' : 'white' }}; color: {{ $i == $currentPage ? 'white' : '#1e293b' }}; border: 2px solid {{ $i == $currentPage ? '#10b981' : '#e2e8f0' }}; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                                    @if($i != $currentPage)
                                        onmouseover="this.style.borderColor='#10b981'; this.style.color='#10b981'"
                                        onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#1e293b'"
                                    @endif
                                >
                                    {{ $i }}
                                </button>
                            @endfor

                            @if($end < $totalPages)
                                @if($end < $totalPages - 1)
                                    <span style="color: #94a3b8; font-weight: 600;">...</span>
                                @endif
                                <button 
                                    wire:click="goToPage({{ $totalPages }})"
                                    style="padding: 10px 16px; background: white; color: #1e293b; border: 2px solid #e2e8f0; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                                    onmouseover="this.style.borderColor='#10b981'; this.style.color='#10b981'"
                                    onmouseout="this.style.borderColor='#e2e8f0'; this.style.color='#1e293b'"
                                >
                                    {{ $totalPages }}
                                </button>
                            @endif
                        </div>

                        <button 
                            wire:click="nextPage"
                            @if($currentPage == $totalPages) disabled @endif
                            style="padding: 10px 20px; background: {{ $currentPage == $totalPages ? '#f1f5f9' : 'linear-gradient(135deg, #10b981, #059669)' }}; color: {{ $currentPage == $totalPages ? '#94a3b8' : 'white' }}; border: none; border-radius: 8px; font-weight: 600; cursor: {{ $currentPage == $totalPages ? 'not-allowed' : 'pointer' }}; transition: all 0.3s;"
                            @if($currentPage != $totalPages)
                                onmouseover="this.style.transform='scale(1.05)'"
                                onmouseout="this.style.transform='scale(1)'"
                            @endif
                        >
                            Next <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                @endif
            @else
                <div style="text-align: center; padding: 60px 20px; background: linear-gradient(135deg, #f8fafc, #e2e8f0); border-radius: 16px; margin-bottom: 32px;">
                    <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #e2e8f0, #cbd5e1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                        <i class="fas fa-search" style="font-size: 36px; color: #64748b;"></i>
                    </div>
                    <h3 style="font-size: 24px; font-weight: 700; color: #1e293b; margin-bottom: 12px;">No Results Found</h3>
                    <p style="color: #64748b; font-size: 16px; margin-bottom: 24px;">
                        No cryptocurrencies match your search "{{ $search }}"
                    </p>
                    <button 
                        wire:click="$set('search', '')"
                        style="padding: 12px 24px; background: linear-gradient(135deg, #10b981, #059669); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; transition: all 0.3s;"
                        onmouseover="this.style.transform='scale(1.05)'"
                        onmouseout="this.style.transform='scale(1)'"
                    >
                        <i class="fas fa-times"></i> Clear Search
                    </button>
                </div>
            @endif

            <!-- Footer -->
            <div style="text-align: center; padding: 24px; background: linear-gradient(135deg, #f8fafc, #e2e8f0); border-radius: 12px;">
                <p style="color: #64748b; font-size: 14px; margin-bottom: 8px;">
                    <strong style="color: #1e293b;">Powered by</strong> 
                    <a href="https://www.delta.exchange/" target="_blank" style="color: #10b981; font-weight: 700; text-decoration: none; transition: color 0.3s;" onmouseover="this.style.color='#059669'" onmouseout="this.style.color='#10b981'">
                        Delta Exchange India
                    </a>
                </p>
                <p style="color: #94a3b8; font-size: 12px;">
                    <i class="fas fa-clock"></i> Data updates every 30 seconds • Last updated: {{ now()->format('h:i A') }}
                </p>
            </div>
        @endif
    </div>

    <style>
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>

    <script>
        document.addEventListener('livewire:init', function () {
            // Auto-refresh crypto prices every 60 seconds (1 minute)
            setInterval(function () {
                Livewire.dispatch('refresh-crypto-tickers');
            }, 60000);
        });
    </script>
</div>
