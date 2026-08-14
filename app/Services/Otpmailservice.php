<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OtpMailService
{
    /**
     * Send the OTP code to $email via Brevo's transactional email API.
     *
     * NOTE: If you already have a BrevoService/BrevoMailService class from
     * your appointment/lab-result notifications, swap the Http::withHeaders()
     * call below for that class's send method instead — the goal is one
     * Brevo integration, not two. This is written standalone so it works
     * even if that service isn't easily reusable for a non-Notification context.
     */
    public function sendOtp(string $toEmail, string $toName, string $otp): bool
    {
        $response = Http::withHeaders([
            'api-key' => config('services.brevo.key'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'name' => config('mail.from.name', 'SMH Laboratory'),
                'email' => config('mail.from.address'),
            ],
            'to' => [
                ['email' => $toEmail, 'name' => $toName],
            ],
            'subject' => 'Your Password Reset Code',
            'htmlContent' => view('emails.otp', [
                'otp' => $otp,
                'name' => $toName,
            ])->render(),
        ]);

        if (! $response->successful()) {
            Log::error('Brevo OTP email failed', [
                'email' => $toEmail,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        }

        return true;
    }
}