<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();

        $email = $socialUser->getEmail();

        if (!$email) {
            // Facebook can withhold email if the user declines to share it.
            // For a patient system we require one to create an account.
            return redirect()->route('login')
                ->with('login_error', 'Your account did not share an email. Please try again or register manually.');
        }

        // already linked to this provider?
        $user = UserAccount::where('oauth_provider', $provider)
            ->where('oauth_uid', $socialUser->getId())
            ->first();

        if (!$user) {
            // existing manual account with same email? link it
            $user = UserAccount::where('email', $email)->first();

            if ($user) {
                $user->update([
                    'oauth_provider' => $provider,
                    'oauth_uid' => $socialUser->getId(),
                ]);
            } else {
                [$firstName, $middleName, $lastName] = $this->splitName($socialUser->getName() ?? '');

                $user = UserAccount::create([
                    'role' => 'patient', // never let the client choose this
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'oauth_provider' => $provider,
                    'oauth_uid' => $socialUser->getId(),
                ]);
            }
        }

        auth()->login($user);
        request()->session()->regenerate();

        return $this->profileIsComplete($user)
            ? $this->redirectByRole($user)
            : redirect()->route('profile.complete');
    }

    // ------------------------------------------------------------
    // Complete Profile (for accounts created via Google/Facebook)
    // ------------------------------------------------------------

    public function showCompleteProfile()
    {
        $user = auth()->user();

        if ($this->profileIsComplete($user)) {
            return $this->redirectByRole($user);
        }

        return view('complete-profile', compact('user'));
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

    // Same role-based redirect your AccountController already uses,
    // so both the manual and social login paths land in the same place.
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
        $parts = preg_split('/\s+/', trim($fullName)) ?: [''];
        $first = $parts[0] ?? '';
        $last = count($parts) > 1 ? array_pop($parts) : '';
        $middle = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';
        return [$first, $middle, $last];
    }

    public static function profileIsComplete(UserAccount $user): bool
    {
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