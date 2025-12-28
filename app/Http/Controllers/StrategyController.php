<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Strategy;
use App\Models\StrategyJournal;
use App\Services\CryptoKaJungleService;

class StrategyController extends Controller
{
    protected $strategySvc;

    public function __construct(CryptoKaJungleService $strategySvc)
    {
        $this->strategySvc = $strategySvc;
    }

    /**
     * Show trader dashboard with all strategies
     */
    public function dashboard()
    {
        $trader = Auth::guard('trader')->user();
        $strategies = $trader->strategies()->get();

        // Calculate PnL for each strategy with live prices
        foreach ($strategies as $strategy) {
            // Get live price
            $livePrice = $this->strategySvc->fetchLivePrice($strategy->crypto_coin);
            $strategy->live_price = $livePrice;
            
            // Calculate unrealized PnL only if live price is available
            if ($livePrice > 0) {
                $currentValue = $strategy->units_held * $livePrice;
                $strategy->unrealized_pnl = $currentValue - $strategy->total_invested_amount;
                $strategy->unrealized_pnl_percentage = $strategy->total_invested_amount > 0 
                    ? ($strategy->unrealized_pnl / $strategy->total_invested_amount) * 100 
                    : 0;
            } else {
                // Use average price if live price is not available
                $currentValue = $strategy->units_held * $strategy->current_average_price;
                $strategy->unrealized_pnl = $currentValue - $strategy->total_invested_amount;
                $strategy->unrealized_pnl_percentage = 0;
            }
            
            // Calculate realized PnL (sum of all profit bookings)
            $strategy->realized_pnl = $strategy->journalEntries()
                ->where('action_type', 'profit_booking')
                ->sum('profit_loss_amount');
        }

        return view('trader.dashboard', compact('strategies'));
    }

    /**
     * Show create strategy form
     */
    public function createForm()
    {
        $trader = Auth::guard('trader')->user();

        // Check if email is verified
        if (!$trader->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')->with('message', 'Please verify your email before creating a strategy.');
        }

        return view('trader.strategy.create');
    }

    /**
     * Store new strategy
     */
    public function store(Request $request)
    {
        $trader = Auth::guard('trader')->user();

        // Check if email is verified
        if (!$trader->hasVerifiedEmail()) {
            return redirect()->route('verification.notice')->with('message', 'Please verify your email before creating a strategy.');
        }
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'crypto_coin' => ['required', 'string', 'max:10'],
            'initial_investment' => ['required', 'numeric', 'min:100'],
            'initial_price' => ['required', 'numeric', 'min:0.00000001'],
            'monthly_sip_amount' => ['required', 'numeric', 'min:10'],
            'profit_target_amount' => ['required', 'numeric', 'min:10'],
            'buy_dip_percentage' => ['required', 'numeric', 'min:1', 'max:100'],
            'buy_dip_amount' => ['required', 'numeric', 'min:10'],
        ]);

        $strategy = Auth::guard('trader')->user()->strategies()->create($validated + [
            'status' => 'active',
            'current_average_price' => $validated['initial_price'],
            'units_held' => 0,
            'total_invested_amount' => 0,
            'current_holding_amount' => 0,
        ]);

        // Initialize strategy with lump sum
        $this->strategySvc->initializeStrategy($strategy, $validated['initial_price']);

        return redirect(route('trader.strategy.show', $strategy->id))->with('success', 'Strategy created successfully');
    }

    /**
     * Show strategy details
     */
    public function show(Strategy $strategy, Request $request)
    {
        $this->authorize('view', $strategy);

        $currentPrice = $request->input('current_price', $strategy->current_average_price);
        
        // If strategy not initialized yet, use initial price as default
        if ($currentPrice == 0 || $currentPrice == null) {
            $currentPrice = $strategy->initial_price;
        }
        
        // Convert to float to avoid division by zero errors
        $currentPrice = (float) $currentPrice;

        // Only check if we should trigger dip buy or profit booking if strategy is initialized and has valid price
        if ($strategy->units_held > 0 && $currentPrice > 0) {
            $this->strategySvc->checkAndExecuteDipBuy($strategy, $currentPrice);
            $this->strategySvc->checkAndExecuteProfitBooking($strategy, $currentPrice);
        }

        $dashboardData = $this->strategySvc->getStrategyDashboard($strategy, $currentPrice);
        $journalEntries = $strategy->journalEntries()->orderBy('created_at', 'desc')->paginate(20);

        return view('trader.strategy.show', compact('dashboardData', 'journalEntries', 'strategy'));
    }

    /**
     * Record manual transaction
     */
    public function recordTransaction(Strategy $strategy, Request $request)
    {
        $this->authorize('update', $strategy);

        $validated = $request->validate([
            'action_type' => ['required', 'in:buy,sell,dip_buy,profit_booking'],
            'units' => ['required', 'numeric', 'min:0.00000001'],
            'price' => ['required', 'numeric', 'min:0.00000001'],
            'notes' => ['nullable', 'string'],
        ]);

        $amount = $validated['units'] * $validated['price'];
        $this->strategySvc->executeTransaction(
            $strategy,
            $validated['action_type'],
            $validated['units'],
            $validated['price'],
            $amount,
            $validated['notes'] ?? ''
        );

        return back()->with('success', 'Transaction recorded successfully');
    }

    /**
     * Execute monthly SIP investment
     */
    public function generateSIPReminder(Strategy $strategy, Request $request)
    {
        $this->authorize('update', $strategy);

        // Validate price input minimally, and fetch live price if missing/invalid
        $inputPrice = $request->input('current_price');
        $currentPrice = is_numeric($inputPrice) && (float) $inputPrice > 0
            ? (float) $inputPrice
            : 0.0;

        if ($currentPrice <= 0) {
            // Try live price first
            $currentPrice = (float) $this->strategySvc->fetchLivePrice($strategy->crypto_coin);
        }

        if ($currentPrice <= 0) {
            // Fallback to average price to ensure progress
            $currentPrice = (float) $strategy->current_average_price;
        }

        $this->strategySvc->executeSIPInvestment($strategy, $currentPrice);

        return back()->with('success', 'SIP investment of ₹' . number_format($strategy->monthly_sip_amount, 2) . ' executed successfully');
    }

    /**
     * Get journal entries
     */
    public function journal(Strategy $strategy)
    {
        $this->authorize('view', $strategy);

        $journalEntries = $strategy->journalEntries()->orderBy('created_at', 'desc')->paginate(50);

        return view('trader.strategy.journal', compact('strategy', 'journalEntries'));
    }

    /**
     * Dip eligibility endpoint
     */
    public function dipEligibility(Strategy $strategy, Request $request)
    {
        $this->authorize('view', $strategy);

        $priceParam = $request->query('price');
        $currentPrice = is_numeric($priceParam) && (float) $priceParam > 0
            ? (float) $priceParam
            : 0.0;

        if ($currentPrice <= 0) {
            $currentPrice = (float) $this->strategySvc->fetchLivePrice($strategy->crypto_coin);
        }

        if ($currentPrice <= 0) {
            $currentPrice = (float) $strategy->current_average_price;
        }

        $result = $this->strategySvc->isDipEligible($strategy, $currentPrice);
        $result['currentPrice'] = $currentPrice;
        $result['suggestedUnits'] = $currentPrice > 0 ? round($result['totalDipAmount'] / $currentPrice, 8) : 0.0;

        return response()->json($result);
    }
}
