<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trader Dashboard - Crypto Ka Jungle</title>
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
        <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                    {{ auth()->guard('trader')->user()->name }}
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <form action="{{ route('trader.logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="dropdown-item">Logout</button>
                    </form>
                </div>
            </li>
        </ul>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary">
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <i class="fas fa-user-circle fa-2x text-white"></i>
                </div>
                <div class="info">
                    <a href="#" class="d-block">{{ auth()->guard('trader')->user()->name }}</a>
                </div>
            </div>
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column">
                    <li class="nav-item">
                        <a href="{{ route('trader.dashboard') }}" class="nav-link active">
                            <i class="nav-icon fas fa-th"></i> <p>Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('trader.strategy.create') }}" class="nav-link">
                            <i class="nav-icon fas fa-plus"></i> <p>New Strategy</p>
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
                        <h1>Your Strategies</h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            <div class="container-fluid">
                @if(!auth()->guard('trader')->user()->hasVerifiedEmail())
                    <div class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-exclamation-triangle"></i> Email Not Verified!</h5>
                        <p>Please verify your email address to create and manage strategies.</p>
                        <a href="{{ route('verification.notice') }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-envelope"></i> Verify Email
                        </a>
                        <form action="{{ route('verification.send') }}" method="POST" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-dark">
                                <i class="fas fa-paper-plane"></i> Resend Verification Email
                            </button>
                        </form>
                    </div>
                @endif

                @if(session('verified'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h5><i class="icon fas fa-check"></i> Email Verified!</h5>
                        Your email has been successfully verified. You can now create and manage strategies.
                    </div>
                @endif

                @if($strategies->count())
                    <div class="row">
                        @foreach($strategies as $strategy)
                            <div class="col-md-4">
                                <div class="card card-primary">
                                    <div class="card-header">
                                        <h3 class="card-title">{{ $strategy->name }} ({{ $strategy->crypto_coin }})</h3>
                                    </div>
                                    <div class="card-body">
                                        <p><strong>Status:</strong> <span class="badge badge-{{ $strategy->status === 'active' ? 'success' : 'danger' }}">{{ ucfirst($strategy->status) }}</span></p>
                                        <p><strong>Units Held:</strong> {{ number_format($strategy->units_held, 8) }}</p>
                                        <p><strong>Total Invested:</strong> ₹{{ number_format($strategy->total_invested_amount, 2) }}</p>
                                        <p><strong>Avg Price:</strong> ₹{{ number_format($strategy->current_average_price, 2) }}</p>
                                        <p><strong>Live Price:</strong> ₹{{ number_format($strategy->live_price ?? 0, 2) }}</p>
                                        <hr>
                                        <p><strong>Unrealized P&L:</strong> 
                                            <span class="badge badge-{{ ($strategy->unrealized_pnl ?? 0) >= 0 ? 'success' : 'danger' }}">
                                                ₹{{ number_format($strategy->unrealized_pnl ?? 0, 2) }} 
                                                ({{ number_format($strategy->unrealized_pnl_percentage ?? 0, 2) }}%)
                                            </span>
                                        </p>
                                        <p><strong>Realized P&L:</strong> 
                                            <span class="badge badge-{{ ($strategy->realized_pnl ?? 0) >= 0 ? 'success' : 'danger' }}">
                                                ₹{{ number_format($strategy->realized_pnl ?? 0, 2) }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ route('trader.strategy.show', $strategy->id) }}" class="btn btn-sm btn-info">View Details</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-info">
                        <p>No strategies created yet. <a href="{{ route('trader.strategy.create') }}">Create one now</a></p>
                    </div>
                @endif
            </div>
        </section>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
