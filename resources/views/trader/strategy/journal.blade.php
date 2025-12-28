<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Strategy Journal - Crypto Ka Jungle</title>
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
                    <li class="nav-item">
                        <a href="{{ route('trader.strategy.show', $strategy->id) }}" class="nav-link">
                            <i class="nav-icon fas fa-arrow-left"></i> <p>Back to Strategy</p>
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
                        <h1>Transaction Journal - {{ $strategy->name }} ({{ $strategy->crypto_coin }})</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">All Transactions</h3>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-hover table-bordered">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th style="width: 140px;">Date</th>
                                            <th style="width: 100px;">Action</th>
                                            <th style="width: 120px;">Units</th>
                                            <th style="width: 130px;">Price/Unit</th>
                                            <th style="width: 120px;">Amount</th>
                                            <th style="width: 120px;">Avg Price After</th>
                                            <th style="width: 120px;">Total Units After</th>
                                            <th style="width: 140px;">Total Invested After</th>
                                            <th style="width: 100px;">P/L Amount</th>
                                            <th style="width: 90px;">P/L %</th>
                                            <th>Reason / Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($journalEntries as $entry)
                                            <tr>
                                                <td>{{ $entry->created_at->format('M d, Y H:i') }}</td>
                                                <td>
                                                    @switch($entry->action_type)
                                                        @case('buy')
                                                            <span class="badge badge-primary">BUY</span>
                                                            @break
                                                        @case('sell')
                                                            <span class="badge badge-success">SELL</span>
                                                            @break
                                                        @case('dip_buy')
                                                            <span class="badge badge-info">DIP BUY</span>
                                                            @break
                                                        @case('profit_booking')
                                                            <span class="badge badge-warning">PROFIT</span>
                                                            @break
                                                        @case('sip')
                                                            <span class="badge badge-success">SIP</span>
                                                            @break
                                                        @case('sip_reminder')
                                                            <span class="badge badge-secondary">REMINDER</span>
                                                            @break
                                                    @endswitch
                                                </td>
                                                <td>{{ number_format($entry->units_bought_sold, 8) }}</td>
                                                <td>₹{{ number_format($entry->price_per_unit, 8) }}</td>
                                                <td>₹{{ number_format($entry->total_amount, 2) }}</td>
                                                <td>₹{{ number_format($entry->current_average_price, 8) }}</td>
                                                <td>{{ number_format($entry->total_units_after_action, 8) }}</td>
                                                <td>₹{{ number_format($entry->total_invested_after_action, 2) }}</td>
                                                <td class="{{ $entry->profit_loss_amount >= 0 ? 'text-success' : 'text-danger' }}">
                                                    ₹{{ number_format($entry->profit_loss_amount, 2) }}
                                                </td>
                                                <td class="{{ $entry->profit_loss_percentage >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ number_format($entry->profit_loss_percentage, 2) }}%
                                                </td>
                                                <td>
                                                    <small>{{ $entry->reason }}</small>
                                                    @if($entry->notes)
                                                        <br><small class="text-muted">{{ $entry->notes }}</small>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center">No transactions recorded yet</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer">
                                {{ $journalEntries->links() }}
                            </div>
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
</body>
</html>
