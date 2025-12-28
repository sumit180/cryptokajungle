<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Strategy Details - Crypto Ka Jungle</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary">
        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column">
                    <li class="nav-item">
                        <a href="{{ route('trader.dashboard') }}" class="nav-link">
                            <i class="nav-icon fas fa-th"></i> <p>Dashboard</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{ $dashboardData['strategy']->name }} ({{ $dashboardData['strategy']->crypto_coin }})</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                @endif

                <!-- Dashboard Cards -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-info"><i class="fas fa-coins"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Units Held</span>
                                <span class="info-box-number">{{ number_format($dashboardData['strategy']->units_held, 8) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-warning"><i class="fas fa-money-bill-wave"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Invested</span>
                                <span class="info-box-number">₹{{ number_format($dashboardData['strategy']->total_invested_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-success"><i class="fas fa-chart-line"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Current Value</span>
                                <span class="info-box-number" id="currentValueDisplay">₹{{ number_format($dashboardData['currentValue'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon {{ $dashboardData['unrealizedProfit'] >= 0 ? 'bg-success' : 'bg-danger' }}" id="pnlIcon"><i class="fas fa-profit"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">P/L Amount</span>
                                <span class="info-box-number" id="pnlAmountDisplay">₹{{ number_format($dashboardData['unrealizedProfit'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price and Progress -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Price Information</h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-sm btn-success" id="refreshLivePrice">
                                        <i class="fas fa-sync-alt"></i> Get Live Price
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <p>
                                    <strong>Current Price:</strong> 
                                    <span id="currentPriceDisplay">₹{{ number_format($dashboardData['currentPrice'], 2) }}</span>
                                    <span id="priceLoader" style="display:none;"><i class="fas fa-spinner fa-spin"></i></span>
                                </p>
                                <p><strong>Average Cost:</strong> ₹{{ number_format($dashboardData['strategy']->current_average_price, 2) }}</p>
                                <p>
                                    <strong>P/L %:</strong> 
                                    <span id="pnlPercentageDisplay" class="badge badge-{{ $dashboardData['profitPercentage'] >= 0 ? 'success' : 'danger' }}">
                                        {{ number_format($dashboardData['profitPercentage'], 2) }}%
                                    </span>
                                </p>
                                <p><strong>Drop from Initial:</strong> <span id="dropFromInitialDisplay">{{ number_format($dashboardData['dropFromInitial'], 2) }}%</span></p>
                                <hr>
                                <div id="priceAlerts"></div>
                                <p class="mb-1">
                                    <strong>Next Buy Dip Price:</strong> 
                                    <span class="badge badge-info" id="nextDipPrice">₹0.00</span>
                                    <small class="text-muted">({{ $strategy->buy_dip_percentage }}% drop from initial)</small>
                                </p>
                                <p class="mb-1">
                                    <strong>Target Profit Price:</strong> 
                                    <span class="badge badge-success" id="targetPrice">₹0.00</span>
                                    <small class="text-muted">(Profit target: ₹{{ number_format($strategy->profit_target_amount, 2) }})</small>
                                </p>
                                <input type="hidden" id="cryptoCoin" value="{{ $strategy->crypto_coin }}">
                                <input type="hidden" id="strategyId" value="{{ $strategy->id }}">
                                <input type="hidden" id="unitsHeld" value="{{ $strategy->units_held }}">
                                <input type="hidden" id="totalInvested" value="{{ $strategy->total_invested_amount }}">
                                <input type="hidden" id="avgPrice" value="{{ $strategy->current_average_price }}">
                                <input type="hidden" id="initialPrice" value="{{ $strategy->initial_price }}">
                                <input type="hidden" id="dipPercentage" value="{{ $strategy->buy_dip_percentage }}">
                                <input type="hidden" id="dipAmount" value="{{ $strategy->buy_dip_amount }}">
                                <input type="hidden" id="profitTarget" value="{{ $strategy->profit_target_amount }}">
                                <input type="hidden" id="dipEligibilityUrl" value="{{ route('trader.strategy.dip-eligibility', $strategy->id) }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card card-warning">
                            <div class="card-header">
                                <h3 class="card-title">Profit Target Progress</h3>
                            </div>
                            <div class="card-body">
                                <div class="progress-group">
                                    <span class="progress-text">Profit Target: ₹{{ number_format($dashboardData['strategy']->profit_target_amount, 2) }}</span>
                                    <span class="progress-number">{{ number_format(min($dashboardData['profitTargetProgress'], 100), 0) }}%</span>
                                    <div class="progress sm">
                                        <div class="progress-bar bg-warning" style="width: {{ min($dashboardData['profitTargetProgress'], 100) }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Actions</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('trader.strategy.journal', $strategy->id) }}" method="GET" class="d-inline">
                                    <button type="submit" class="btn btn-info"><i class="fas fa-book"></i> View Full Journal</button>
                                </form>
                                
                                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#sipModal">
                                    <i class="fas fa-calendar-plus"></i> Execute Monthly SIP
                                </button>

                                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#dipBuyModal">
                                    <i class="fas fa-shopping-cart"></i> Buy on Dip
                                </button>

                                <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#profitBookingModal">
                                    <i class="fas fa-hand-holding-usd"></i> Book Profit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Journal Entries -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Recent Transactions</h3>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Action</th>
                                            <th>Units</th>
                                            <th>Price</th>
                                            <th>Amount</th>
                                            <th>Post Avg</th>
                                            <th>Units After</th>
                                            <th>Invested After</th>
                                            <th>P/L</th>
                                            <th>Reason</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($journalEntries as $entry)
                                            <tr>
                                                <td>{{ $entry->created_at->format('M d, Y H:i') }}</td>
                                                <td><span class="badge badge-{{ in_array($entry->action_type, ['buy','dip_buy','sip','sip_reminder']) ? 'primary' : 'success' }}">{{ ucfirst($entry->action_type) }}</span></td>
                                                <td>{{ number_format($entry->units_bought_sold, 8) }}</td>
                                                <td>₹{{ number_format($entry->price_per_unit, 8) }}</td>
                                                <td>₹{{ number_format($entry->total_amount, 2) }}</td>
                                                <td>₹{{ number_format($entry->current_average_price, 8) }}</td>
                                                <td>{{ number_format($entry->total_units_after_action, 8) }}</td>
                                                <td>₹{{ number_format($entry->total_invested_after_action, 2) }}</td>
                                                <td>₹{{ number_format($entry->profit_loss_amount, 2) }} ({{ number_format($entry->profit_loss_percentage, 2) }}%)</td>
                                                <td>{{ $entry->reason }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">No transactions yet</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- SIP Investment Modal -->
<div class="modal fade" id="sipModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('trader.strategy.sip-reminder', $strategy->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-success">
                    <h5 class="modal-title">Execute Monthly SIP Investment</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>SIP Amount:</strong> ₹{{ number_format($strategy->monthly_sip_amount, 2) }}
                    </div>
                    <div class="form-group">
                        <label>Current {{ $strategy->crypto_coin }} Price *</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="current_price" id="sipCurrentPrice" class="form-control" 
                                   value="{{ $dashboardData['currentPrice'] }}" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-info" id="fetchSipPrice">
                                    <i class="fas fa-sync-alt"></i> Get Live Price
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Enter the current market price to execute this SIP</small>
                    </div>
                    <input type="hidden" id="sipAmount" value="{{ number_format($strategy->monthly_sip_amount, 2, '.', '') }}">
                    <div class="alert alert-secondary" id="sipPreview" style="display:none;">
                        <strong>Expected Post-SIP Update:</strong><br>
                        <span id="sipPreviewText"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Execute SIP Investment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Buy Dip Modal -->
<div class="modal fade" id="dipBuyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('trader.strategy.transaction', $strategy->id) }}" method="POST">
                @csrf
                <input type="hidden" name="action_type" value="dip_buy">
                <div class="modal-header bg-info">
                    <h5 class="modal-title">🎯 Buy on Dip Opportunity!</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Dip Buy Settings:</strong><br>
                        Initial Price: ₹{{ number_format($strategy->initial_price, 2) }}<br>
                        Buy on Dip: {{ $strategy->buy_dip_percentage }}% drop<br>
                        Amount per Dip: ₹{{ number_format($strategy->buy_dip_amount, 2) }}
                    </div>
                    
                    <div class="form-group">
                        <label>Current Market Price (₹) *</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="price" id="dipBuyPrice" class="form-control" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-info" id="fetchDipBuyPrice">
                                    <i class="fas fa-sync-alt"></i> Get Live Price
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning" id="dipEligibility" style="display:none;">
                        <strong>Eligibility Check:</strong><br>
                        <span id="dipEligibilityText"></span>
                    </div>
                    
                    <div class="form-group">
                        <label>Amount to Invest (₹)</label>
                        <input type="number" step="0.01" name="amount" id="dipBuyAmount" class="form-control" value="{{ $strategy->buy_dip_amount }}" readonly>
                    </div>
                    <div class="form-group">
                        <label>Units to Buy (Auto-calculated)</label>
                        <input type="number" step="0.00000001" name="units" id="dipBuyUnits" class="form-control" readonly>
                    </div>
                    
                    <div class="alert alert-success" id="dipCalculation" style="display:none;">
                        <strong>Purchase Details:</strong><br>
                        <span id="dipCalculationText"></span>
                    </div>
                    
                    <div class="form-group">
                        <label>Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Manual dip buy"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info" id="submitDipBuy" disabled>
                        <i class="fas fa-shopping-cart"></i> Execute Buy on Dip
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Profit Booking Modal -->
<div class="modal fade" id="profitBookingModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('trader.strategy.transaction', $strategy->id) }}" method="POST">
                @csrf
                <input type="hidden" name="action_type" value="profit_booking">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">💰 Book Profit</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Current Holdings:</strong><br>
                        Units: {{ number_format($strategy->units_held, 8) }} {{ $strategy->crypto_coin }}<br>
                        Avg Price: ₹{{ number_format($strategy->current_average_price, 2) }}<br>
                        Total Invested: ₹{{ number_format($strategy->total_invested_amount, 2) }}
                    </div>
                    
                    <div class="form-group">
                        <label>Current Market Price (₹) *</label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="price" id="profitBookingPrice" class="form-control" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-info" id="fetchProfitPrice">
                                    <i class="fas fa-sync-alt"></i> Get Live Price
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Current Profit to Book (₹) *</label>
                        <input type="number" step="0.01" id="profitAmountToBook" class="form-control" 
                               value="{{ number_format($dashboardData['unrealizedProfit'], 2, '.', '') }}" 
                               @if($dashboardData['unrealizedProfit'] < $strategy->profit_target_amount) disabled @endif 
                               required>
                        <small class="text-muted">
                            @if($dashboardData['unrealizedProfit'] < $strategy->profit_target_amount)
                                ⚠️ Current profit (₹{{ number_format($dashboardData['unrealizedProfit'], 2) }}) is less than profit target (₹{{ number_format($strategy->profit_target_amount, 2) }})
                            @else
                                Enter your current profit amount (₹{{ number_format($dashboardData['unrealizedProfit'], 2) }})
                            @endif
                        </small>
                    </div>

                    <div class="form-group">
                        <label>Units to Sell (Auto-calculated)</label>
                        <input type="number" step="0.00000001" name="units" id="profitBookingUnits" class="form-control" required readonly>
                        <small class="text-muted">Calculated based on profit target</small>
                    </div>

                    <div class="form-group">
                        <label>You Will Receive (₹)</label>
                        <input type="number" step="0.01" id="sellAmount" class="form-control" readonly>
                        <small class="text-muted">Total amount from selling units at current price</small>
                    </div>

                    <div class="alert alert-success" id="profitBreakdown" style="display:none;">
                        <strong>Profit Breakdown:</strong><br>
                        <span id="breakdownText"></span>
                    </div>

                    <div class="form-group">
                        <label>Notes (Optional)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Profit booking at target"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning" id="submitProfitBooking" 
                            @if($dashboardData['unrealizedProfit'] < $strategy->profit_target_amount) disabled @endif>
                        <i class="fas fa-check"></i> Book Profit Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
$(document).ready(function() {
    const coinMap = {
        'BTC': 'bitcoin',
        'ETH': 'ethereum',
        'BNB': 'binancecoin',
        'XRP': 'ripple',
        'ADA': 'cardano',
        'SOL': 'solana',
        'DOGE': 'dogecoin',
        'DOT': 'polkadot',
        'MATIC': 'matic-network',
        'SHIB': 'shiba-inu',
        'AVAX': 'avalanche-2',
        'LTC': 'litecoin',
        'TRX': 'tron',
        'UNI': 'uniswap'
    };

    let dipPriceThreshold = 0;
    let targetProfitPrice = 0;

    function calculateTargetPrices(livePrice) {
        const initialPrice = parseFloat($('#initialPrice').val());
        const dipPercentage = parseFloat($('#dipPercentage').val());
        const profitTarget = parseFloat($('#profitTarget').val());
        const totalInvested = parseFloat($('#totalInvested').val());
        const unitsHeld = parseFloat($('#unitsHeld').val());

        // Use live price when provided; else fall back to current displayed price
        const effectivePrice = typeof livePrice === 'number' && !isNaN(livePrice)
            ? livePrice
            : parseFloat($('#currentPriceDisplay').text().replace('₹', '').replace(/,/g, '')) || initialPrice;

        // Determine next dip threshold relative to the initial price
        fetchDipEligibility(effectivePrice, function(res) {
            dipPriceThreshold = res.nextDipPriceThreshold || 0;

            // Target profit price based on current holdings
            targetProfitPrice = unitsHeld > 0 ? (totalInvested + profitTarget) / unitsHeld : 0;

            // Update display
            $('#nextDipPrice').text('₹' + (dipPriceThreshold || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
            $('#targetPrice').text('₹' + targetProfitPrice.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        });
    }

    function checkPriceAlerts(livePrice) {
        const dipAmount = parseFloat($('#dipAmount').val());
        $('#priceAlerts').empty();
        fetchDipEligibility(livePrice, function(res) {
            const totalDipAmount = res.totalDipAmount || dipAmount;
            if (livePrice <= (res.nextDipPriceThreshold || 0)) {
                $('#priceAlerts').html(`
                    <div class="alert alert-info alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong><i class="fas fa-arrow-down"></i> Buy Dip Alert!</strong><br>
                        Price reached ₹${livePrice.toLocaleString('en-IN', {minimumFractionDigits: 2})}. 
                        <a href="#" id="triggerDipBuy" class="alert-link">Click here to buy ₹${totalDipAmount.toLocaleString('en-IN', {minimumFractionDigits: 2})}</a>
                    </div>
                `);
            }

            if (livePrice >= targetProfitPrice) {
                $('#priceAlerts').append(`
                    <div class="alert alert-success alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong><i class="fas fa-arrow-up"></i> Target Reached!</strong><br>
                        Price reached ₹${livePrice.toLocaleString('en-IN', {minimumFractionDigits: 2})}. Consider booking profits!
                    </div>
                `);
            }
        });
    }

    function fetchLivePrice(callback) {
        const coin = $('#cryptoCoin').val();
        const coinId = coinMap[coin];
        
        if (!coinId) {
            alert('Cryptocurrency not supported for live price fetch');
            return;
        }

        $('#priceLoader').show();
        $('#refreshLivePrice').prop('disabled', true);

        $.ajax({
            url: `https://api.coingecko.com/api/v3/simple/price?ids=${coinId}&vs_currencies=inr`,
            method: 'GET',
            dataType: 'json',
            timeout: 10000,
            success: function(data) {
                console.log('CoinGecko API Response:', data);
                if (data[coinId] && data[coinId].inr) {
                    const livePrice = data[coinId].inr;
                    updatePriceAndCalculations(livePrice);
                    calculateTargetPrices(livePrice);
                    checkPriceAlerts(livePrice);
                    if (callback) callback(livePrice);
                } else {
                    console.error('Invalid response structure:', data);
                    alert('Failed to fetch live price. Invalid response from CoinGecko API.');
                }
            },
            error: function(xhr, status, error) {
                console.error('CoinGecko API Error:', {
                    status: status,
                    error: error,
                    response: xhr.responseText,
                    coin: coin,
                    coinId: coinId
                });
                
                let errorMsg = 'Failed to fetch live price. ';
                if (status === 'timeout') {
                    errorMsg += 'Request timed out. Please try again.';
                } else if (xhr.status === 429) {
                    errorMsg += 'Rate limit reached. Please wait a moment and try again.';
                } else if (xhr.status === 0) {
                    errorMsg += 'Network error or CORS issue. Check your internet connection.';
                } else {
                    errorMsg += `Error: ${error}`;
                }
                alert(errorMsg);
            },
            complete: function() {
                $('#priceLoader').hide();
                $('#refreshLivePrice').prop('disabled', false);
            }
        });
    }

    function updatePriceAndCalculations(livePrice) {
        const unitsHeld = parseFloat($('#unitsHeld').val());
        const totalInvested = parseFloat($('#totalInvested').val());
        const avgPrice = parseFloat($('#avgPrice').val());
        const initialPrice = parseFloat($('#initialPrice').val());

        // Calculate current value
        const currentValue = unitsHeld * livePrice;

        // Calculate P/L
        const pnlAmount = currentValue - totalInvested;
        const pnlPercentage = totalInvested > 0 ? (pnlAmount / totalInvested) * 100 : 0;

        // Calculate drop from initial
        const dropFromInitial = ((initialPrice - livePrice) / initialPrice) * 100;

        // Update displays
        $('#currentPriceDisplay').text('₹' + livePrice.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#currentValueDisplay').text('₹' + currentValue.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#pnlAmountDisplay').text('₹' + pnlAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        
        // Update P/L percentage badge
        const pnlBadgeClass = pnlPercentage >= 0 ? 'badge-success' : 'badge-danger';
        $('#pnlPercentageDisplay').removeClass('badge-success badge-danger').addClass(pnlBadgeClass)
            .text(pnlPercentage.toFixed(2) + '%');

        // Update P/L icon color
        const pnlIconClass = pnlAmount >= 0 ? 'bg-success' : 'bg-danger';
        $('#pnlIcon').removeClass('bg-success bg-danger').addClass(pnlIconClass);

        // Update drop from initial
        $('#dropFromInitialDisplay').text(dropFromInitial.toFixed(2) + '%');
    }

    // Refresh live price button
    $('#refreshLivePrice').on('click', function() {
        fetchLivePrice();
    });

    // Fetch price for SIP modal
    $('#fetchSipPrice').on('click', function() {
        const btn = $(this);
        btn.prop('disabled', true);
        
        fetchLivePrice(function(price) {
            $('#sipCurrentPrice').val(price);
            calculateSipPreview();
            btn.prop('disabled', false);
        });
    });

    // Trigger dip buy modal
    $(document).on('click', '#triggerDipBuy', function(e) {
        e.preventDefault();
        const currentPrice = parseFloat($('#currentPriceDisplay').text().replace('₹', '').replace(',', ''));
        const dipAmount = parseFloat($('#dipAmount').val());
        const units = dipAmount / currentPrice;

        $('#dipBuyPrice').val(currentPrice);
        $('#dipBuyAmount').val(dipAmount);
        $('#dipBuyUnits').val(units.toFixed(8));
        
        $('#buyDipModal').modal('show');
    });

    // Fetch price for profit booking modal
    $('#fetchProfitPrice').on('click', function() {
        const btn = $(this);
        btn.prop('disabled', true);
        
        fetchLivePrice(function(price) {
            $('#profitBookingPrice').val(price);
            calculateProfitBooking();
            btn.prop('disabled', false);
        });
    });

    // When profit booking modal is opened, auto-fill current price and calculate current profit
    $('#profitBookingModal').on('show.bs.modal', function() {
        const currentPrice = parseFloat($('#currentPriceDisplay').text().replace('₹', '').replace(/,/g, ''));
        const unitsHeld = parseFloat($('#unitsHeld').val());
        const totalInvested = parseFloat($('#totalInvested').val());
        
        if (currentPrice > 0) {
            $('#profitBookingPrice').val(currentPrice);
            
            // Calculate current profit
            const currentValue = unitsHeld * currentPrice;
            const currentProfit = currentValue - totalInvested;
            
            // Set current profit in the field
            $('#profitAmountToBook').val(currentProfit.toFixed(2));
            
            calculateProfitBooking();
        }
    });

    // Calculate profit booking when price or amount changes
    $('#profitBookingPrice, #profitAmountToBook').on('input', function() {
        calculateProfitBooking();
    });

    function calculateProfitBooking() {
        const currentPrice = parseFloat($('#profitBookingPrice').val());
        const currentProfit = parseFloat($('#profitAmountToBook').val());
        const avgPrice = parseFloat($('#avgPrice').val());
        const unitsHeld = parseFloat($('#unitsHeld').val());
        const totalInvested = parseFloat($('#totalInvested').val());
        const profitTarget = parseFloat($('#profitTarget').val());

        if (currentPrice > 0 && currentProfit > 0 && avgPrice > 0) {
            // Check if current profit is less than profit target
            if (currentProfit < profitTarget) {
                $('#profitBookingUnits').val('0');
                $('#sellAmount').val('0');
                $('#profitBreakdown').show().find('#breakdownText').html(
                    `⚠️ Current profit (₹${currentProfit.toFixed(2)}) is less than your profit target (₹${profitTarget.toFixed(2)}).<br>` +
                    `You need at least ₹${profitTarget.toFixed(2)} profit to book.`
                );
                $('#profitAmountToBook').prop('disabled', true);
                $('#submitProfitBooking').prop('disabled', true);
                return;
            } else {
                $('#profitAmountToBook').prop('disabled', false);
            }
            
            if (currentPrice > avgPrice) {
                // Calculate how many units to sell to get the current profit amount
                // We want to sell crypto worth = current profit amount
                // So: units to sell = current profit / current price
                const unitsToSell = currentProfit / currentPrice;
                
                // Check if we have enough units
                if (unitsToSell > unitsHeld) {
                    const maxSellValue = unitsHeld * currentPrice;
                    const maxCostBasis = unitsHeld * avgPrice;
                    const maxProfit = maxSellValue - maxCostBasis;
                    
                    $('#profitBookingUnits').val(unitsHeld.toFixed(8));
                    $('#sellAmount').val(maxSellValue.toFixed(2));
                    $('#profitBreakdown').show().find('#breakdownText').html(
                        `⚠️ Not enough units! You only have ${unitsHeld.toFixed(8)} units.<br>` +
                        `Max value you can sell: ₹${maxSellValue.toFixed(2)}<br>` +
                        `Max profit possible: ₹${maxProfit.toFixed(2)}`
                    );
                    $('#submitProfitBooking').prop('disabled', true);
                } else {
                    // Sell amount = units × current price (this equals current profit)
                    const sellAmount = unitsToSell * currentPrice;
                    
                    // Cost basis for these units
                    const costBasis = unitsToSell * avgPrice;
                    
                    // Actual profit from this sale
                    const actualProfit = sellAmount - costBasis;

                    $('#profitBookingUnits').val(unitsToSell.toFixed(8));
                    $('#sellAmount').val(sellAmount.toFixed(2));
                    
                    $('#profitBreakdown').show().find('#breakdownText').html(
                        `Selling ${unitsToSell.toFixed(8)} units @ ₹${currentPrice.toFixed(2)}<br>` +
                        `You will receive: ₹${sellAmount.toFixed(2)}<br>` +
                        `Cost basis: ₹${costBasis.toFixed(2)} (@ avg ₹${avgPrice.toFixed(2)})<br>` +
                        `<strong>Actual Profit: ₹${actualProfit.toFixed(2)}</strong>`
                    );
                    $('#submitProfitBooking').prop('disabled', false);
                }
            } else {
                $('#profitBookingUnits').val('0');
                $('#sellAmount').val('0');
                $('#profitBreakdown').show().find('#breakdownText').html(
                    '⚠️ Current price (₹' + currentPrice.toFixed(2) + ') is not higher than average cost (₹' + avgPrice.toFixed(2) + '). No profit to book.'
                );
                $('#submitProfitBooking').prop('disabled', true);
            }
        } else {
            $('#profitBreakdown').hide();
        }
    }

    // Calculate dip buy eligibility and units
    function calculateDipBuy() {
        const currentPrice = parseFloat($('#dipBuyPrice').val());
        const initialPrice = parseFloat($('#initialPrice').val());
        const dipPercentage = parseFloat($('#dipPercentage').val());
        const dipAmount = parseFloat($('#dipAmount').val());

        if (currentPrice > 0 && initialPrice > 0) {
            fetchDipEligibility(currentPrice, function(res) {
                if (res.eligible) {
                    const totalDipAmount = res.totalDipAmount || (dipAmount * res.numberOfDips);
                    const unitsToBuy = res.suggestedUnits || (totalDipAmount / currentPrice);

                    $('#dipBuyAmount').val(totalDipAmount.toFixed(2));
                    $('#dipBuyUnits').val(unitsToBuy.toFixed(8));

                    $('#dipEligibility').removeClass('alert-warning').addClass('alert-success');
                    $('#dipEligibilityText').html(
                        `✅ <strong>ELIGIBLE FOR DIP BUY!</strong><br>` +
                        `Current Price: ₹${currentPrice.toFixed(2)}<br>` +
                        `Initial Price: ₹${initialPrice.toFixed(2)}<br>` +
                        `Drop: ${res.dropPercentage.toFixed(2)}% (Target: ${dipPercentage}% per dip)<br>` +
                        `Number of Dips: ${res.numberOfDips}<br>` +
                        `Amount per Dip: ₹${dipAmount.toFixed(2)}<br>` +
                        `<strong>Total Amount to Invest: ₹${totalDipAmount.toFixed(2)}</strong>`
                    );
                    $('#dipEligibility').show();

                    $('#dipCalculationText').html(
                        `Buying ${unitsToBuy.toFixed(8)} units @ ₹${currentPrice.toFixed(2)}<br>` +
                        `Total Investment: ₹${totalDipAmount.toFixed(2)} (${res.numberOfDips} dips × ₹${dipAmount.toFixed(2)})<br>` +
                        `This will add to your holdings`
                    );
                    $('#dipCalculation').show();

                    $('#submitDipBuy').prop('disabled', false);
                } else {
                    const priceNeedsToDrop = initialPrice * (1 - (dipPercentage / 100));
                    const additionalDropNeeded = currentPrice - priceNeedsToDrop;

                    $('#dipBuyUnits').val('0');

                    $('#dipEligibility').removeClass('alert-success').addClass('alert-warning');
                    $('#dipEligibilityText').html(
                        `⚠️ <strong>NOT ELIGIBLE YET</strong><br>` +
                        `Current Price: ₹${currentPrice.toFixed(2)}<br>` +
                        `Initial Price: ₹${initialPrice.toFixed(2)}<br>` +
                        `Current Drop: ${res.dropPercentage.toFixed(2)}%<br>` +
                        `Required Drop: ${dipPercentage}%<br>` +
                        `Price needs to drop to: ₹${priceNeedsToDrop.toFixed(2)}<br>` +
                        `Additional drop needed: ₹${additionalDropNeeded.toFixed(2)}`
                    );
                    $('#dipEligibility').show();
                    $('#dipCalculation').hide();

                    $('#submitDipBuy').prop('disabled', true);
                }
            });
        } else {
            $('#dipEligibility').hide();
            $('#dipCalculation').hide();
            $('#submitDipBuy').prop('disabled', true);
        }
    }
    
    // Fetch live price for dip buy
    $('#fetchDipBuyPrice').on('click', function() {
        fetchLivePrice(function(livePrice) {
            $('#dipBuyPrice').val(livePrice.toFixed(2));
            calculateDipBuy();
        });
    });
    
    // Calculate on price change
    $('#dipBuyPrice').on('input', function() {
        calculateDipBuy();
    });

    // Initialize
    calculateTargetPrices();

    // SIP preview calculation
    function calculateSipPreview() {
        const sipPrice = parseFloat($('#sipCurrentPrice').val());
        const sipAmount = parseFloat($('#sipAmount').val());
        const unitsHeld = parseFloat($('#unitsHeld').val());
        const totalInvested = parseFloat($('#totalInvested').val());

        if (sipPrice > 0 && sipAmount > 0) {
            const unitsToBuy = sipAmount / sipPrice;
            const newUnits = unitsHeld + unitsToBuy;
            const newInvested = totalInvested + sipAmount;
            const newAvg = newUnits > 0 ? (newInvested / newUnits) : 0;

            $('#sipPreviewText').html(
                `Price: ₹${sipPrice.toFixed(2)} | SIP Amount: ₹${sipAmount.toFixed(2)}<br>` +
                `Units to buy: ${unitsToBuy.toFixed(8)}<br>` +
                `Total units after: ${newUnits.toFixed(8)}<br>` +
                `Total invested after: ₹${newInvested.toFixed(2)}<br>` +
                `<strong>Expected average after SIP: ₹${newAvg.toFixed(8)}</strong>`
            );
            $('#sipPreview').show();
        } else {
            $('#sipPreview').hide();
        }
    }

    // Trigger SIP preview on input
    $('#sipCurrentPrice').on('input', function() {
        calculateSipPreview();
    });

    // Auto-fetch live price on page load
    setTimeout(function() {
        fetchLivePrice();
    }, 1000);

    // Auto-refresh every 60 seconds
    setInterval(function() {
        fetchLivePrice();
    }, 60000);

    // Server-backed dip eligibility fetch
    function fetchDipEligibility(price, callback) {
        const url = $('#dipEligibilityUrl').val();
        $.ajax({
            url: url,
            method: 'GET',
            data: { price: price },
            dataType: 'json',
            timeout: 8000,
            success: function(res) {
                if (typeof callback === 'function') {
                    callback(res);
                }
            },
            error: function() {
                if (typeof callback === 'function') {
                    callback({ eligible: false, numberOfDips: 0, totalDipAmount: 0, dropPercentage: 0, nextDipPriceThreshold: 0 });
                }
            }
        });
    }
});
</script>
</body>
</html>
