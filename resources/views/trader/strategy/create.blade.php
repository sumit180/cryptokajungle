<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Strategy - Crypto Ka Jungle</title>
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
                        <h1>Create New Strategy</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Crypto Ka Jungle Strategy Parameters</h3>
                            </div>
                            <form action="{{ route('trader.strategy.store') }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    @if($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="form-group">
                                        <label for="name">Strategy Name</label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="e.g., Bitcoin Strategy" value="{{ old('name') }}" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="crypto_coin">Crypto Coin *</label>
                                        <select name="crypto_coin" id="crypto_coin" class="form-control @error('crypto_coin') is-invalid @enderror" required>
                                            <option value="">Select Crypto Coin</option>
                                            <option value="BTC" {{ old('crypto_coin') == 'BTC' ? 'selected' : '' }}>Bitcoin (BTC)</option>
                                            <option value="ETH" {{ old('crypto_coin') == 'ETH' ? 'selected' : '' }}>Ethereum (ETH)</option>
                                            <option value="BNB" {{ old('crypto_coin') == 'BNB' ? 'selected' : '' }}>Binance Coin (BNB)</option>
                                            <option value="XRP" {{ old('crypto_coin') == 'XRP' ? 'selected' : '' }}>Ripple (XRP)</option>
                                            <option value="ADA" {{ old('crypto_coin') == 'ADA' ? 'selected' : '' }}>Cardano (ADA)</option>
                                            <option value="SOL" {{ old('crypto_coin') == 'SOL' ? 'selected' : '' }}>Solana (SOL)</option>
                                            <option value="DOGE" {{ old('crypto_coin') == 'DOGE' ? 'selected' : '' }}>Dogecoin (DOGE)</option>
                                            <option value="DOT" {{ old('crypto_coin') == 'DOT' ? 'selected' : '' }}>Polkadot (DOT)</option>
                                            <option value="MATIC" {{ old('crypto_coin') == 'MATIC' ? 'selected' : '' }}>Polygon (MATIC)</option>
                                            <option value="SHIB" {{ old('crypto_coin') == 'SHIB' ? 'selected' : '' }}>Shiba Inu (SHIB)</option>
                                            <option value="AVAX" {{ old('crypto_coin') == 'AVAX' ? 'selected' : '' }}>Avalanche (AVAX)</option>
                                            <option value="LTC" {{ old('crypto_coin') == 'LTC' ? 'selected' : '' }}>Litecoin (LTC)</option>
                                            <option value="TRX" {{ old('crypto_coin') == 'TRX' ? 'selected' : '' }}>TRON (TRX)</option>
                                            <option value="UNI" {{ old('crypto_coin') == 'UNI' ? 'selected' : '' }}>Uniswap (UNI)</option>
                                        </select>
                                        <small class="text-muted">Select the cryptocurrency for this strategy</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="initial_investment">Initial Investment (₹)</label>
                                        <input type="number" step="0.01" name="initial_investment" class="form-control @error('initial_investment') is-invalid @enderror" placeholder="10000" value="{{ old('initial_investment') }}" required>
                                        <small class="text-muted">Lump sum amount to buy initially</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="initial_price">Current Price (₹) *</label>
                                        <div class="input-group">
                                            <input type="number" step="0.00000001" name="initial_price" id="initial_price" class="form-control @error('initial_price') is-invalid @enderror" placeholder="Fetching..." value="{{ old('initial_price') }}" required readonly>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-info" id="refreshPrice">
                                                    <i class="fas fa-sync-alt"></i> Refresh
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted" id="priceInfo">Live price will be fetched automatically</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="monthly_sip_amount">Monthly SIP Amount (₹)</label>
                                        <input type="number" step="0.01" name="monthly_sip_amount" class="form-control @error('monthly_sip_amount') is-invalid @enderror" placeholder="1000" value="{{ old('monthly_sip_amount') }}" required>
                                        <small class="text-muted">Amount to invest every 1st of month</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="profit_target_amount">Profit Target Amount (₹)</label>
                                        <input type="number" step="0.01" name="profit_target_amount" class="form-control @error('profit_target_amount') is-invalid @enderror" placeholder="1000" value="{{ old('profit_target_amount') }}" required>
                                        <small class="text-muted">Sell when profit reaches this amount</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="buy_dip_percentage">Buy on Dip % Drop</label>
                                        <input type="number" step="0.01" name="buy_dip_percentage" class="form-control @error('buy_dip_percentage') is-invalid @enderror" placeholder="10" value="{{ old('buy_dip_percentage') }}" required>
                                        <small class="text-muted">Buy more when price drops by this %</small>
                                    </div>

                                    <div class="form-group">
                                        <label for="buy_dip_amount">Buy on Dip Amount (₹)</label>
                                        <input type="number" step="0.01" name="buy_dip_amount" class="form-control @error('buy_dip_amount') is-invalid @enderror" placeholder="1000" value="{{ old('buy_dip_amount') }}" required>
                                        <small class="text-muted">Amount to buy for each dip percentage</small>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Create Strategy</button>
                                    <a href="{{ route('trader.dashboard') }}" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<script>
$(document).ready(function() {
    // Crypto price fetching
    function fetchCryptoPrice() {
        const coin = $('#crypto_coin').val();
        if (!coin) {
            $('#initial_price').val('').attr('placeholder', 'Select a coin first');
            $('#priceInfo').text('Select a cryptocurrency to fetch price');
            return;
        }

        $('#initial_price').attr('placeholder', 'Fetching price...');
        $('#priceInfo').html('<i class="fas fa-spinner fa-spin"></i> Fetching live price...');
        $('#refreshPrice').prop('disabled', true);

        // Fetch from CoinGecko API (free, no API key needed)
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

        const coinId = coinMap[coin];
        
        $.ajax({
            url: `https://api.coingecko.com/api/v3/simple/price?ids=${coinId}&vs_currencies=inr`,
            method: 'GET',
            success: function(data) {
                if (data[coinId] && data[coinId].inr) {
                    const price = data[coinId].inr;
                    $('#initial_price').val(price).removeAttr('readonly');
                    $('#priceInfo').html(`<i class="fas fa-check-circle text-success"></i> Live price fetched: ₹${price.toLocaleString('en-IN')}`);
                } else {
                    $('#initial_price').val('').removeAttr('readonly').attr('placeholder', 'Enter manually');
                    $('#priceInfo').html('<i class="fas fa-exclamation-triangle text-warning"></i> Could not fetch price, please enter manually');
                }
            },
            error: function() {
                $('#initial_price').val('').removeAttr('readonly').attr('placeholder', 'Enter manually');
                $('#priceInfo').html('<i class="fas fa-exclamation-triangle text-danger"></i> Failed to fetch price, please enter manually');
            },
            complete: function() {
                $('#refreshPrice').prop('disabled', false);
            }
        });
    }

    // Fetch price when coin is selected
    $('#crypto_coin').on('change', fetchCryptoPrice);

    // Refresh button
    $('#refreshPrice').on('click', fetchCryptoPrice);

    // Fetch price on page load if coin is already selected
    if ($('#crypto_coin').val()) {
        fetchCryptoPrice();
    }
});
</script>
</body>
</html>
