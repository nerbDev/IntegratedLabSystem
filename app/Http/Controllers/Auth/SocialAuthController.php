<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        $scopes = $provider === 'facebook' ? ['email'] : [];

        return Socialite::driver($provider)
            ->stateless()
            ->scopes($scopes)
            ->redirect();
    }

        public function callback(string $provider): RedirectResponse
        {
            try {
                $socialUser = Socialite::driver($provider)->stateless()->user();
            } catch (\Throwable $e) {
                // Log the exact error to storage/logs/laravel.log
                \Illuminate\Support\Facades\Log::error("{$provider} Login Error: " . $e->getMessage());

                return redirect()->route('login.register')
                    ->with('login_error', 'Authentication failed or was cancelled. Please try again.');
            }

        $email = $socialUser->getEmail();

        if (!$email) {
            // Facebook can withhold email if the user declines to share it
            return redirect()->route('login.register')
                ->with('login_error', 'Your account did not share an email address. Please register manually.');
        }

        // Already linked to this provider?
        $user = UserAccount::where('oauth_provider', $provider)
            ->where('oauth_uid', $socialUser->getId())
            ->first();

        if (!$user) {
            // Existing manual account with same email? Link it
            $user = UserAccount::where('email', $email)->first();

            if ($user) {
                $user->update([
                    'oauth_provider' => $provider,
                    'oauth_uid'      => $socialUser->getId(),
                ]);
            } else {
                // Split name safely with fallbacks
                $rawName = $socialUser->getName() ?? $socialUser->getNickname() ?? 'Patient User';
                [$firstName, $middleName, $lastName] = $this->splitName($rawName);

                $user = UserAccount::create([
                    'role'           => 'patient', // Enforce patient role
                    'first_name'     => $firstName ?: 'Patient',
                    'middle_name'    => $middleName,
                    'last_name'      => $lastName ?: 'User',
                    'email'          => $email,
                    'oauth_provider' => $provider,
                    'oauth_uid'      => $socialUser->getId(),
                    'password'       => null,
                ]);
            }
        }

        auth()->login($user);
        request()->session()->regenerate();

        return static::profileIsComplete($user)
            ? $this->redirectByRole($user)
            : redirect()->route('profile.complete');
    }

    // ------------------------------------------------------------
    // Complete Profile (for accounts created via Google/Facebook)
    // ------------------------------------------------------------

    public function showCompleteProfile()
    {
        $user = auth()->user();

        if (static::profileIsComplete($user)) {
            return $this->redirectByRole($user);
        }

        return view('CompleteProfile', compact('user'));
    }

    public function completeProfile(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'date_of_birth'  => 'required|date',
            'sex'            => 'required|in:male,female',
            'phone_number'   => 'required|string|max:20',
            'Umunicipality'  => 'required|string|max:255',
            'Ubarangay'      => 'required|string|max:255',
            'Ustreet_house'  => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
        ]);

        $user->update($validated);

        return $this->redirectByRole($user)
            ->with('success', 'Profile completed successfully.');
    }

    private function redirectByRole($user): RedirectResponse
    {
        if ($user->role === 'admin') {
            return redirect()->route('admindashboard');
        }

        if ($user->role === 'staff') {
            return redirect()->route('staffdashboard');
        }

        if ($user->role === 'patient') {
            return redirect()->route('patientdashboard');
        }

        abort(403, 'Unauthorized role');
    }

    private function splitName(string $fullName): array
    {
        $cleanName = trim($fullName);
        if (empty($cleanName)) {
            return ['Patient', null, 'User'];
        }

        $parts = preg_split('/\s+/', $cleanName);
        $first = array_shift($parts) ?: 'Patient';
        $last = count($parts) > 0 ? array_pop($parts) : 'User';
        $middle = count($parts) > 0 ? implode(' ', $parts) : null;

        return [$first, $middle, $last];
    }

    public static function profileIsComplete(?UserAccount $user): bool
    {
        if (!$user) {
            return false;
        }

        return !empty($user->date_of_birth)
            && !empty($user->sex)
            && !empty($user->Umunicipality)
            && !empty($user->Ubarangay)
            && !empty($user->Ustreet_house)
            && !empty($user->contact_person)
            && !empty($user->contact_number)
            && !empty($user->phone_number);
    }
}