<?php

declare(strict_types=1);

use App\Models\Trader;
use App\Models\Strategy;
use App\Services\CryptoKaJungleService;

it('computes average after SIP correctly', function () {
    // Arrange: create trader and strategy
    $trader = Trader::create([
        'name' => 'Test Trader',
        'email' => 'trader@example.com',
        'password' => 'secret123',
    ]);

    $strategy = Strategy::create([
        'trader_id' => $trader->id,
        'name' => 'ETH DCA Strategy',
        'crypto_coin' => 'ETH',
        'initial_investment' => 15000.00,
        'initial_price' => 2500.00, // ₹ per unit
        'monthly_sip_amount' => 1000.00,
        'profit_target_amount' => 2000.00,
        'buy_dip_percentage' => 10.00,
        'buy_dip_amount' => 1000.00,
        'status' => 'active',
        'current_average_price' => 2500.00,
        'units_held' => 0,
        'total_invested_amount' => 0,
        'current_holding_amount' => 0,
    ]);

    $svc = app(CryptoKaJungleService::class);

    // Initialize with lump sum at initial price
    $svc->initializeStrategy($strategy, 2500.00);

    // Capture baseline
    $oldUnits = (float) $strategy->units_held; // 15000 / 2500 = 6.0 units
    $oldInvested = (float) $strategy->total_invested_amount; // 15000

    // Act: execute SIP at current price
    $sipPrice = 2000.00; // ₹ per unit
    $svc->executeSIPInvestment($strategy, $sipPrice);

    // Expected calculations
    $sipAmount = (float) $strategy->monthly_sip_amount; // 1000
    $unitsBought = $sipAmount / $sipPrice; // 0.5 units
    $newUnitsExpected = $oldUnits + $unitsBought; // 6.5 units
    $newInvestedExpected = $oldInvested + $sipAmount; // 16000
    $newAvgExpected = $newInvestedExpected / $newUnitsExpected; // 16000 / 6.5 = 2461.5384615...

    // Refresh model
    $strategy->refresh();

    // Assert with delta for decimals
    $this->assertEqualsWithDelta($newUnitsExpected, (float) $strategy->units_held, 1e-8, 'Units held should update correctly');
    $this->assertEqualsWithDelta($newInvestedExpected, (float) $strategy->total_invested_amount, 1e-8, 'Total invested should update correctly');
    $this->assertEqualsWithDelta($newAvgExpected, (float) $strategy->current_average_price, 1e-8, 'Average price should update correctly');
});

it('computes dip eligibility and thresholds correctly', function () {
    // Arrange
    $trader = Trader::create([
        'name' => 'Dip Tester',
        'email' => 'dip@example.com',
        'password' => 'secret123',
    ]);

    $strategy = Strategy::create([
        'trader_id' => $trader->id,
        'name' => 'ETH Dip Strategy',
        'crypto_coin' => 'ETH',
        'initial_investment' => 15000.00,
        'initial_price' => 1000.00, // set simple baseline
        'monthly_sip_amount' => 1000.00,
        'profit_target_amount' => 2000.00,
        'buy_dip_percentage' => 10.00,
        'buy_dip_amount' => 1000.00,
        'status' => 'active',
        'current_average_price' => 1000.00,
        'units_held' => 0,
        'total_invested_amount' => 0,
        'current_holding_amount' => 0,
    ]);

    $svc = app(CryptoKaJungleService::class);

    // Case 1: 25% drop from initial (1000 -> 750)
    $currentPrice = 750.00;
    $result = $svc->isDipEligible($strategy, $currentPrice);

    // Expected: numberOfDips = floor(25/10) = 2, totalDipAmount = 2 * 1000 = 2000
    // Next threshold = initial * (1 - ((floor(25/10)+1) * 10%)) = 1000 * (1 - 0.30) = 700
    expect($result['eligible'])->toBeTrue();
    expect($result['numberOfDips'])->toBe(2);
    expect($result['totalDipAmount'])->toBe(2000.00);
    $this->assertEqualsWithDelta(25.0, (float) $result['dropPercentage'], 1e-8);
    $this->assertEqualsWithDelta(700.0, (float) $result['nextDipPriceThreshold'], 1e-8);

    // Case 2: below first dip threshold (5% drop, 1000 -> 950): not eligible
    $currentPrice2 = 950.00;
    $result2 = $svc->isDipEligible($strategy, $currentPrice2);

    expect($result2['eligible'])->toBeFalse();
    expect($result2['numberOfDips'])->toBe(0);
    expect($result2['totalDipAmount'])->toBe(0.00);
    $this->assertEqualsWithDelta(900.0, (float) $result2['nextDipPriceThreshold'], 1e-8, 'Next dip price should be 10% below initial');
});
