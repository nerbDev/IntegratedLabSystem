<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetOtp;
use App\Models\UserAccount; // adjust namespace if your model lives elsewhere
use App\Services\OtpMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    protected OtpMailService $otpMailService;

    public function __construct(OtpMailService $otpMailService)
    {
        $this->otpMailService = $otpMailService;
    }

    // GET /forgot-password
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // POST /forgot-password
    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = UserAccount::where('email', $request->email)->first();

        // Don't reveal whether the email exists — same message either way.
        if (! $user) {
            return back()->with('status', 'If that email is registered, a code has been sent.');
        }

        $otp = (string) random_int(100000, 999999);

        PasswordResetOtp::updateOrCreate(
            ['email' => $user->email],
            [
                'otp' => Hash::make($otp),
                'attempts' => 0,
                'expires_at' => now()->addMinutes(10),
                'verified_at' => null,
            ]
        );

        $name = trim($user->first_name . ' ' . $user->last_name);
        $sent = $this->otpMailService->sendOtp($user->email, $name, $otp);

        if (! $sent) {
            return back()->withErrors(['email' => 'Could not send the code right now. Please try again shortly.']);
        }

        // Keep the email in session so the verify/reset steps don't need it re-typed.
        session(['password_reset_email' => $user->email]);

        return redirect()->route('password.verify-otp.form')
            ->with('status', 'A 6-digit code has been sent to your email.');
    }

    // GET /forgot-password/verify-otp
    public function showVerifyForm()
    {
        if (! session('password_reset_email')) {
            return redirect()->route('password.forgot-form');
        }

        return view('auth.verify-otp');
    }

    // POST /forgot-password/verify-otp
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        $email = session('password_reset_email');

        if (! $email) {
            return redirect()->route('password.forgot-form');
        }

        $record = PasswordResetOtp::where('email', $email)->first();

        if (! $record || $record->expires_at->isPast()) {
            throw ValidationException::withMessages([
                'otp' => 'This code has expired. Please request a new one.',
            ]);
        }

        if ($record->attempts >= 5) {
            throw ValidationException::withMessages([
                'otp' => 'Too many attempts. Please request a new code.',
            ]);
        }

        if (! Hash::check($request->otp, $record->otp)) {
            $record->increment('attempts');

            throw ValidationException::withMessages([
                'otp' => 'Incorrect code. Please try again.',
            ]);
        }

        $record->update(['verified_at' => now()]);

        // Short-lived token so the reset-password step can't be reached
        // by guessing the URL without having verified the OTP first.
        session(['password_reset_verified' => true]);

        return redirect()->route('password.reset-form');
    }

    // GET /forgot-password/reset
    public function showResetForm()
    {
        if (! session('password_reset_verified') || ! session('password_reset_email')) {
            return redirect()->route('password.forgot-form');
        }

        return view('auth.reset-password');
    }

    // POST /forgot-password/reset
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $email = session('password_reset_email');

        if (! $email || ! session('password_reset_verified')) {
            return redirect()->route('password.forgot-form');
        }

        $user = UserAccount::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('password.forgot-form')
                ->withErrors(['email' => 'Something went wrong. Please start again.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        // Clean up: invalidate the OTP record and clear session flags.
        PasswordResetOtp::where('email', $email)->delete();
        session()->forget(['password_reset_email', 'password_reset_verified']);

        return redirect()->route('login.register')
            ->with('status', 'Your password has been reset. Please log in.');
    }
}