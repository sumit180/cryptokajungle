<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email - Crypto Ka Jungle</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .verify-box {
            width: 420px;
            margin: 5% auto;
        }

        .verify-icon {
            font-size: 4rem;
            color: #10b981;
            margin-bottom: 1rem;
        }

        .card-body-custom {
            padding: 2rem;
        }

        .alert-custom {
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .btn-resend {
            width: 100%;
            margin-top: 1rem;
            padding: 0.75rem;
        }

        .verification-info {
            background: #f0fdf4;
            border-left: 4px solid #10b981;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }

        .verification-info p {
            margin: 0;
            color: #065f46;
            font-size: 0.95rem;
        }

        .loader {
            text-align: center;
            margin: 2rem 0;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #10b981;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="hold-transition register-page">
<div class="verify-box">
    <div class="card card-outline card-success">
        <div class="card-header text-center">
            <a href="{{ route('home') }}" class="h1"><b>Crypto</b> Ka Jungle</a>
        </div>
        <div class="card-body card-body-custom">
            <div class="text-center">
                <i class="fas fa-envelope verify-icon"></i>
                <h2 class="mb-3">Verify Your Email</h2>
            </div>

            @if(session('resent'))
                <div class="alert alert-success alert-custom" role="alert">
                    <i class="fas fa-check-circle mr-2"></i>
                    <strong>Success!</strong> A fresh verification link has been sent to your email address.
                </div>
            @endif

            <div class="verification-info">
                <p>
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Verification Required:</strong> Before you can create trading strategies, you need to verify your email address. We've sent a verification link to your email.
                </p>
            </div>

            <p class="text-muted text-center mb-3">
                <small>Email: <strong>{{ Auth::guard('trader')->user()?->email ?? session('email') }}</strong></small>
            </p>

            <div class="alert alert-info" role="alert">
                <h5 class="alert-heading mb-2">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Next Steps
                </h5>
                <ul class="mb-0">
                    <li>Check your email inbox (and spam folder)</li>
                    <li>Click on the verification link</li>
                    <li>You'll be automatically logged in</li>
                    <li>Then you can create your first strategy!</li>
                </ul>
            </div>

            <form action="{{ route('verification.send') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-success btn-resend">
                    <i class="fas fa-redo mr-2"></i>
                    Resend Verification Email
                </button>
            </form>

            @auth('trader')
                <a href="{{ route('trader.dashboard') }}" class="btn btn-outline-primary btn-resend">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            @endauth

            <hr class="my-3">

            <p class="text-center text-muted">
                <small>
                    Wrong email? <a href="{{ route('trader.register') }}">Register again</a> or
                    <a href="{{ route('trader.login') }}">Login</a> with another account
                </small>
            </p>

            <div class="alert alert-warning mt-3" role="alert">
                <small>
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <strong>Important:</strong> Without email verification, you cannot create any trading strategies. This ensures account security.
                </small>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
