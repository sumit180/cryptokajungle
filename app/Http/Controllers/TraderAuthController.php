<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Trader;

class TraderAuthController extends Controller
{
    /**
     * Show trader login form
     */
    public function showLoginForm()
    {
        return view('trader.login');
    }

    /**
     * Handle trader login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('trader')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('trader.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle trader logout
     */
    public function logout(Request $request)
    {
        Auth::guard('trader')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(route('trader.login'));
    }

    /**
     * Show trader registration form
     */
    public function showRegisterForm()
    {
        return view('trader.register');
    }

    /**
     * Handle trader registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:traders'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $trader = Trader::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        // Send verification email
        $trader->sendEmailVerificationNotification();

        return redirect()->route('verification.notice');
    }

    /**
     * Show email verification notice
     */
    public function verifyNotice()
    {
        $trader = Auth::guard('trader')->user();

        // If already verified, redirect to dashboard
        if ($trader && $trader->hasVerifiedEmail()) {
            return redirect(route('trader.dashboard'));
        }

        return view('trader.verify-email', ['email' => $trader->email ?? null]);
    }

    /**
     * Handle email verification
     */
    public function verify(Request $request)
    {
        $trader = Trader::findOrFail($request->route('id'));
        
        if (!hash_equals((string) $request->route('hash'), sha1($trader->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        if (!$trader->hasVerifiedEmail()) {
            $trader->markEmailAsVerified();
            event(new \Illuminate\Auth\Events\Verified($trader));
        }

        Auth::guard('trader')->login($trader);
        return redirect(route('trader.dashboard'))->with('verified', true);
    }

    /**
     * Send verification email again
     */
    public function resendVerification(Request $request)
    {
        $trader = Auth::guard('trader')->user();
        
        if (!$trader) {
            return redirect()->route('trader.login');
        }
        
        if ($trader->hasVerifiedEmail()) {
            return redirect()->route('trader.dashboard');
        }
        
        $trader->sendEmailVerificationNotification();
        
        return back()->with('resent', true);
    }
}
