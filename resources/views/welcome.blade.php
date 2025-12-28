<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Crypto Ka Jungle - Master Your Crypto Trading Strategy</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @livewireStyles
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --secondary: #3b82f6;
            --dark: #0f172a;
            --dark-light: #1e293b;
            --gray: #64748b;
            --light: #f8fafc;
            --white: #ffffff;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: var(--dark);
            overflow-x: hidden;
        }

        /* Navigation */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--white);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo i {
            color: var(--primary);
            font-size: 2rem;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: var(--white);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: var(--primary);
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-block;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
        }

        .btn-outline {
            border: 2px solid var(--primary);
            color: var(--primary);
            background: transparent;
        }

        .btn-outline:hover {
            background: var(--primary);
            color: var(--white);
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark-light) 100%);
            padding: 8rem 2rem 6rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(16,185,129,0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 900;
            color: var(--white);
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero h1 .highlight {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            font-size: 1.25rem;
            color: var(--gray);
            margin-bottom: 2rem;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .stats {
            display: flex;
            gap: 3rem;
            justify-content: center;
            margin-top: 4rem;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--primary);
            display: block;
        }

        .stat-label {
            color: var(--gray);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Features Section */
        .features {
            padding: 6rem 2rem;
            background: var(--light);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--dark);
            margin-bottom: 1rem;
        }

        .section-header p {
            font-size: 1.125rem;
            color: var(--gray);
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: var(--white);
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }

        .feature-icon i {
            font-size: 1.75rem;
            color: var(--white);
        }

        .feature-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .feature-card p {
            color: var(--gray);
            line-height: 1.7;
        }

        /* How It Works */
        .how-it-works {
            padding: 6rem 2rem;
            background: var(--white);
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-top: 3rem;
        }

        .step {
            text-align: center;
            position: relative;
        }

        .step-number {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            font-weight: 900;
            color: var(--white);
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        }

        .step h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .step p {
            color: var(--gray);
        }

        /* Strategies Section */
        .strategies {
            padding: 6rem 2rem;
            background: var(--dark);
            color: var(--white);
        }

        .strategy-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .strategy-card {
            background: var(--dark-light);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .strategy-card:hover {
            border-color: var(--primary);
            transform: translateY(-5px);
        }

        .strategy-header {
            padding: 2rem;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(59, 130, 246, 0.2));
        }

        .strategy-header h3 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .strategy-header .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            background: var(--primary);
            color: var(--white);
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .strategy-body {
            padding: 2rem;
        }

        .strategy-features {
            list-style: none;
            margin-bottom: 2rem;
        }

        .strategy-features li {
            padding: 0.75rem 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--gray);
        }

        .strategy-features li i {
            color: var(--primary);
        }

        /* Unified Strategy Styles */
        .unified-strategy {
            background: var(--dark-light);
            border-radius: 24px;
            padding: 3rem;
            border: 2px solid rgba(16, 185, 129, 0.3);
        }

        .strategy-overview {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(59, 130, 246, 0.1));
            border-radius: 16px;
        }

        .overview-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--primary);
            color: var(--white);
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            font-weight: 700;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }

        .overview-badge i {
            font-size: 1rem;
        }

        .strategy-overview h3 {
            font-size: 2.5rem;
            font-weight: 900;
            margin-bottom: 1rem;
            color: var(--white);
        }

        .strategy-overview p {
            font-size: 1.125rem;
            color: var(--gray);
            max-width: 800px;
            margin: 0 auto;
        }

        .strategy-components {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .component-card {
            background: var(--dark);
            border-radius: 16px;
            padding: 2rem;
            display: flex;
            gap: 2rem;
            align-items: flex-start;
            border: 1px solid rgba(16, 185, 129, 0.2);
            transition: all 0.3s;
        }

        .component-card:hover {
            border-color: var(--primary);
            transform: translateX(10px);
        }

        .component-icon {
            width: 80px;
            height: 80px;
            min-width: 80px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
        }

        .component-icon i {
            font-size: 2rem;
            color: var(--white);
        }

        .component-content h4 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            color: var(--white);
        }

        .component-content p {
            color: var(--gray);
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .component-features {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .component-features li {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray);
            font-size: 0.875rem;
        }

        .component-features li i {
            color: var(--primary);
            font-size: 0.75rem;
        }

        .strategy-arrow {
            text-align: center;
            color: var(--primary);
            font-size: 2rem;
            margin: -1rem 0;
        }

        .strategy-result {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            padding: 2.5rem;
            border-radius: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .result-content {
            display: flex;
            gap: 1.5rem;
            align-items: center;
            flex: 1;
        }

        .result-content i {
            font-size: 3rem;
            color: var(--white);
        }

        .result-content h4 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 0.5rem;
        }

        .result-content p {
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
        }

        /* CTA Section */
        .cta {
            padding: 6rem 2rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            text-align: center;
            color: var(--white);
        }

        .cta h2 {
            font-size: 3rem;
            font-weight: 900;
            margin-bottom: 1.5rem;
        }

        .cta p {
            font-size: 1.25rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }

        .cta .btn {
            background: var(--white);
            color: var(--primary);
            font-size: 1.125rem;
            padding: 1rem 2.5rem;
        }

        .cta .btn:hover {
            background: var(--light);
        }

        /* Footer */
        .footer {
            background: var(--dark);
            color: var(--white);
            padding: 3rem 2rem 1rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-bottom: 2rem;
        }

        .footer-section h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--primary);
        }

        .footer-section p,
        .footer-section a {
            color: var(--gray);
            text-decoration: none;
            display: block;
            margin-bottom: 0.5rem;
        }

        .footer-section a:hover {
            color: var(--primary);
        }

        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid var(--dark-light);
            color: var(--gray);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .stats {
                flex-direction: column;
                gap: 2rem;
            }

            .nav-links {
                display: none;
            }

            .feature-grid,
            .strategy-grid,
            .steps {
                grid-template-columns: 1fr;
            }

            .cta h2 {
                font-size: 2rem;
            }

            .unified-strategy {
                padding: 1.5rem;
            }

            .strategy-overview h3 {
                font-size: 1.75rem;
            }

            .component-card {
                flex-direction: column;
                text-align: center;
            }

            .component-icon {
                margin: 0 auto;
            }

            .strategy-result {
                flex-direction: column;
                text-align: center;
            }

            .result-content {
                flex-direction: column;
                text-align: center;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.6s ease-out;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <i class="fas fa-chart-line"></i>
                <span>Crypto Ka Jungle</span>
            </div>
            <div class="nav-links">
                <a href="#features">Features</a>
                <a href="#how-it-works">How It Works</a>
                <a href="#strategies">Strategies</a>
                <a href="{{ route('trader.login') }}" class="btn btn-outline">Login</a>
                <a href="{{ route('trader.register') }}" class="btn btn-primary">Get Started</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content animate-fade-in">
            <h1>Navigate the <span class="highlight">Crypto Ka Jungle</span> with Smart Trading Strategies</h1>
            <p>Master crypto spot trading with automated SIP, dip buying, and profit booking. Build wealth systematically in the volatile crypto market.</p>
            <div class="hero-buttons">
                <a href="{{ route('trader.register') }}" class="btn btn-primary" style="font-size: 1.125rem; padding: 1rem 2.5rem;">
                    <i class="fas fa-rocket"></i> Start Trading Now
                </a>
                <a href="#how-it-works" class="btn btn-outline" style="font-size: 1.125rem; padding: 1rem 2.5rem; color: var(--white); border-color: var(--white);">
                    <i class="fas fa-play-circle"></i> Learn How
                </a>
            </div>
            <div class="stats">
                <div class="stat-item">
                    <span class="stat-number">14+</span>
                    <span class="stat-label">Crypto Coins</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Live Tracking</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">100%</span>
                    <span class="stat-label">Automated</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Live Crypto Prices Section -->
    <section style="padding: 4rem 2rem; background: linear-gradient(135deg, #f8fafc, #e2e8f0);">
        <div class="container" style="max-width: 1400px; margin: 0 auto;">
            @livewire('pages.crypto-ticker')
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <div class="section-header">
                <h2>Powerful Features for Smart Traders</h2>
                <p>Everything you need to build and manage your crypto trading strategy</p>
            </div>
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3>Automated SIP Investment</h3>
                    <p>Set up monthly systematic investment plans to buy crypto regularly, averaging out your purchase cost over time.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Smart Dip Buying</h3>
                    <p>Automatically buy more when prices drop by your set percentage. Turn market dips into opportunities.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3>Profit Target Booking</h3>
                    <p>Set your profit targets and book profits automatically when your goals are achieved. Secure your gains.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-sync-alt"></i>
                    </div>
                    <h3>Live Price Tracking</h3>
                    <p>Real-time crypto prices from CoinGecko API. Always know your portfolio value and unrealized profits.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <h3>Transaction Journal</h3>
                    <p>Complete history of all your trades with P/L calculations. Track every buy, sell, and profit booking.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <h3>Multiple Cryptos</h3>
                    <p>Support for BTC, ETH, BNB, XRP, ADA, SOL, DOGE, and 7 more popular cryptocurrencies.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="how-it-works" id="how-it-works">
        <div class="container">
            <div class="section-header">
                <h2>How It Works</h2>
                <p>Start your crypto journey in 4 simple steps</p>
            </div>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Register & Login</h3>
                    <p>Create your free trader account in less than a minute</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>Create Strategy</h3>
                    <p>Choose your crypto, set initial investment, SIP amount, and profit targets</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>Execute Trades</h3>
                    <p>Make lumpsum investments, execute SIPs, buy on dips with live prices</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Book Profits</h3>
                    <p>Achieve your profit targets and book gains. Watch your wealth grow!</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Strategies Section -->
    <section class="strategies" id="strategies">
        <div class="container">
            <div class="section-header">
                <h2>The Complete Crypto Ka Jungle Strategy</h2>
                <p>All three powerful components work together in one comprehensive strategy</p>
            </div>
            
            <div class="unified-strategy">
                <div class="strategy-overview">
                    <div class="overview-badge">
                        <i class="fas fa-crown"></i>
                        <span>Complete Strategy</span>
                    </div>
                    <h3>Crypto Ka Jungle</h3>
                    <p>A systematic approach combining lumpsum investment, automated SIP, smart dip buying, and profit target booking - all working together to maximize your crypto returns.</p>
                </div>

                <div class="strategy-components">
                    <div class="component-card">
                        <div class="component-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="component-content">
                            <h4>1. Lumpsum + Monthly SIP</h4>
                            <p>Start with an initial investment and add money monthly through SIP. This averages your purchase cost and builds your position systematically.</p>
                            <ul class="component-features">
                                <li><i class="fas fa-check"></i> Initial lumpsum investment</li>
                                <li><i class="fas fa-check"></i> Automated monthly SIP additions</li>
                                <li><i class="fas fa-check"></i> Average cost reduction over time</li>
                            </ul>
                        </div>
                    </div>

                    <div class="strategy-arrow">
                        <i class="fas fa-plus"></i>
                    </div>

                    <div class="component-card">
                        <div class="component-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="component-content">
                            <h4>2. Smart Dip Buying</h4>
                            <p>When prices drop by your set percentage (e.g., 10%, 20%), automatically buy more. Turn market crashes into opportunities.</p>
                            <ul class="component-features">
                                <li><i class="fas fa-check"></i> Buy on price drops</li>
                                <li><i class="fas fa-check"></i> Multiple dip purchase alerts</li>
                                <li><i class="fas fa-check"></i> Maximize gains on recovery</li>
                            </ul>
                        </div>
                    </div>

                    <div class="strategy-arrow">
                        <i class="fas fa-plus"></i>
                    </div>

                    <div class="component-card">
                        <div class="component-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <div class="component-content">
                            <h4>3. Profit Target Booking</h4>
                            <p>Set your profit goals and get alerts when achieved. Book profits systematically and secure your gains at the right time.</p>
                            <ul class="component-features">
                                <li><i class="fas fa-check"></i> Set clear profit targets</li>
                                <li><i class="fas fa-check"></i> Auto profit booking alerts</li>
                                <li><i class="fas fa-check"></i> Secure gains & reduce risk</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="strategy-result">
                    <div class="result-content">
                        <i class="fas fa-trophy"></i>
                        <div>
                            <h4>The Result?</h4>
                            <p>A complete, automated crypto trading system that buys systematically, capitalizes on dips, and books profits at targets - all in one strategy!</p>
                        </div>
                    </div>
                    <a href="{{ route('trader.register') }}" class="btn btn-primary" style="font-size: 1.125rem; padding: 1rem 3rem;">
                        <i class="fas fa-rocket"></i> Start Your Journey Now
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Ready to Master Crypto Trading?</h2>
            <p>Join Crypto Ka Jungle today and start building your systematic crypto portfolio</p>
            <a href="{{ route('trader.register') }}" class="btn">
                <i class="fas fa-user-plus"></i> Create Free Account
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3>Crypto Ka Jungle</h3>
                <p>Your smart companion for crypto spot trading. Build wealth systematically with automated strategies.</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <a href="{{ route('trader.login') }}">Login</a>
                <a href="{{ route('trader.register') }}">Register</a>
                <a href="#features">Features</a>
                <a href="#strategies">Strategies</a>
            </div>
            <div class="footer-section">
                <h3>Supported Cryptos</h3>
                <p>BTC, ETH, BNB, XRP, ADA, SOL, DOGE, DOT, MATIC, SHIB, AVAX, LTC, TRX, UNI</p>
            </div>
            <div class="footer-section">
                <h3>Disclaimer</h3>
                <p>Cryptocurrency trading involves risk. This platform is for educational and tracking purposes. Always do your own research.</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Crypto Ka Jungle. All rights reserved. | Design and Developed by <strong>Computer Gyani</strong> | Made with <i class="fas fa-heart" style="color: var(--danger);"></i> for traders.</p>
        </div>
    </footer>
    @livewireScripts
</body>
</html>
