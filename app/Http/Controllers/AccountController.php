<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAccount;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\Package;
use App\Models\UnavailableDate;
use App\Models\Appointment;
use App\Models\ActivityLog;
use Illuminate\Support\Carbon;

class AccountController extends Controller
{
    // Create a new account from the admin User Account Registry
    public function adminUserAccountsStore(Request $request)
    {
        $validated = $request->validate([
            'role'            => 'required|in:patient,staff,admin',
            'first_name'      => 'required|string|max:255',
            'middle_name'     => 'nullable|string|max:255',
            'last_name'       => 'required|string|max:255',
            'date_of_birth'   => 'required|date',
            'sex'             => 'required|in:male,female',
            'email'           => 'required|email|unique:useraccount,email',
            'phone_number'    => 'required|string|max:20',
            'Umunicipality'   => 'required|string|max:255',
            'Ubarangay'       => 'required|string|max:255',
            'Ustreet_house'   => 'required|string|max:255',
            'contact_person'  => 'required|string|max:255',
            'contact_number'  => 'required|string|max:20',
            'password'        => 'required|string|min:6|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        UserAccount::create($validated);

        return redirect()->back()->with('success', 'New account created successfully.');
    }
    // ------------------------------
    // Show Login / Register Page
    // ------------------------------
    public function showAuth()
    {
        // If already logged in → redirect to correct dashboard
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }

        return view('login'); // your login/register page
    }

    // ------------------------------
    // Login
    // ------------------------------
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $existing = UserAccount::where('email', $credentials['email'])->first();

        if ($existing && is_null($existing->password)) {
            return back()->with('error', 'This account uses Google/Facebook sign-in. Please use that button instead.');
        }

        if (Auth::attempt($credentials)) {
            return $this->redirectByRole(Auth::user());
        }

        return back()->with('error', 'Invalid login credentials');
    }

    // ------------------------------
    // Logout
    // ------------------------------
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ✅ Redirect to landing page instead of login
        return redirect()->route('welcome');
    }

    // ------------------------------
    // Register
    // ------------------------------
    public function register(Request $request)
    {
        $request->validate([
            'role' => 'required|in:patient,staff,admin',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'sex' => 'required|in:male,female',
            'phone_number' => 'required|string|max:20',
            'email' => 'required|email|unique:useraccount,email',
            'Umunicipality' => 'required|string|max:255',
            'Ubarangay' => 'required|string|max:255',
            'Ustreet_house' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = UserAccount::create([
            'role' => $request->role,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'sex' => $request->sex,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
            'Umunicipality' => $request->Umunicipality,
            'Ubarangay' => $request->Ubarangay,
            'Ustreet_house' => $request->Ustreet_house,
            'contact_person' => $request->contact_person,
            'contact_number' => $request->contact_number,
            'password' => Hash::make($request->password),
        ]);

        // Auto login after register
        Auth::login($user);

        return $this->redirectByRole($user)
            ->with('success', 'Account created and logged in successfully.');
    }

    // ------------------------------------------------------------
    // Social Login (Google / Facebook) — direct sign-in/sign-up
    // ------------------------------------------------------------

    public function redirectToProvider(string $provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404);
        }

        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(string $provider)
    {
        if (!in_array($provider, ['google', 'facebook'])) {
            abort(404);
        }

        $socialUser = Socialite::driver($provider)->user();
        $email = $socialUser->getEmail();

        if (!$email) {
            // Facebook can withhold email if the user declines to share it.
            // We require an email to create/match a patient record.
            return redirect()->route('welcome')
                ->with('error', 'Your account did not share an email. Please try again or register manually.');
        }

        // already linked to this provider?
        $user = UserAccount::where('oauth_provider', $provider)
            ->where('oauth_uid', $socialUser->getId())
            ->first();

        if (!$user) {
            // existing manual/email account with the same address → link it
            $user = UserAccount::where('email', $email)->first();

            if ($user) {
                $user->update([
                    'oauth_provider' => $provider,
                    'oauth_uid' => $socialUser->getId(),
                ]);
            } else {
                [$firstName, $middleName, $lastName] = $this->splitName($socialUser->getName() ?? $email);

                // role is always hardcoded to 'patient' here — never derived
                // from anything the client sends, since role gates access
                // (admin/staff accounts are not created via social login)
                $user = UserAccount::create([
                    'role' => 'patient',
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'oauth_provider' => $provider,
                    'oauth_uid' => $socialUser->getId(),
                ]);
            }
        }

        Auth::login($user);

        if (!$this->profileIsComplete($user)) {
            return redirect()->route('profile.complete')
                ->with('success', 'Signed in! Please finish setting up your profile.');
        }

        return $this->redirectByRole($user);
    }

    private function splitName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName)) ?: [''];
        $first = $parts[0] ?? '';
        $last = count($parts) > 1 ? array_pop($parts) : '';
        $middle = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : '';
        return [$first, $middle, $last];
    }

    public function profileIsComplete(UserAccount $user): bool
    {
        return !empty($user->date_of_birth)
            && !empty($user->sex)
            && !empty($user->phone_number)
            && !empty($user->Umunicipality)
            && !empty($user->Ubarangay)
            && !empty($user->Ustreet_house)
            && !empty($user->contact_person)
            && !empty($user->contact_number);
    }

    // ------------------------------------------------------------
    // Complete Profile (for accounts created via Google/Facebook)
    // ------------------------------------------------------------

    public function showCompleteProfile()
    {
        $user = Auth::user();

        if ($this->profileIsComplete($user)) {
            return $this->redirectByRole($user);
        }

        return view('CompleteProfile', compact('user'));
    }

    public function completeProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'date_of_birth' => 'required|date',
            'sex' => 'required|in:male,female',
            'phone_number' => 'required|string|max:20',
            'Umunicipality' => 'required|string|max:255',
            'Ubarangay' => 'required|string|max:255',
            'Ustreet_house' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
        ]);

        $user->update($validated);

        return $this->redirectByRole($user)
            ->with('success', 'Profile completed successfully.');
    }

    // ------------------------------
    // Role-based Redirect (NEW 🔥)
    // ------------------------------
    private function redirectByRole($user)
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

            // ------------------------------
        // Admin: User Account Registry (list)
        // ------------------------------
        public function adminUserAccountsIndex()
        {
            $users = UserAccount::orderBy('last_name')->orderBy('first_name')->get();

            return view('ASuseraccounts', compact('users'));
        }

        // ------------------------------
        // Admin: User Account Registry (edit)
        // ------------------------------
        public function adminUserAccountsUpdate(Request $request, $id)
        {
            $user = UserAccount::findOrFail($id);

            $validated = $request->validate([
                'first_name'     => 'required|string|max:255',
                'middle_name'    => 'nullable|string|max:255',
                'last_name'      => 'required|string|max:255',
                'email'          => 'required|email|unique:useraccount,email,' . $user->id,
                'phone_number'   => 'required|string|max:20',
                'Umunicipality'  => 'required|string|max:255',
                'Ubarangay'      => 'required|string|max:255',
                'Ustreet_house'  => 'required|string|max:255',
                'role'           => 'required|in:patient,staff,admin',
                'contact_person' => 'required|string|max:255',
                'contact_number' => 'required|string|max:20',
            ]);

            $user->update($validated);

            return redirect()->back()->with('success', 'User account updated successfully.');
        }
    // ------------------------------
    // Dashboard
    // ------------------------------
        public function dashboard()
    {
        // ---------- KPI CARDS ----------
        $totalPatients = UserAccount::where('role', 'patient')->count();

        $pendingAppointments = Appointment::where('status', 'pending')->count();

        // "completed" = admin has reviewed/proceeded to lab, waiting on result upload
        $pendingLabResults = Appointment::where('status', 'completed')
            ->whereDoesntHave('result')
            ->count();

        $releasedLabResults = Appointment::where('status', 'released')->count();

        // ---------- APPOINTMENT STATUS BREAKDOWN (doughnut) ----------
        $knownStatuses = ['pending', 'approved', 'rescheduled', 'completed', 'cancelled', 'released'];
        $statusCounts = Appointment::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $appointmentStatusLabels = collect($knownStatuses)->map(fn ($s) => ucfirst($s));
        $appointmentStatusData = collect($knownStatuses)->map(fn ($s) => $statusCounts[$s] ?? 0);

        // ---------- LAB RESULTS: PENDING VS RELEASED (doughnut) ----------
        $labResultLabels = ['Pending', 'Released'];
        $labResultData = [$pendingLabResults, $releasedLabResults];

        // ---------- PATIENT GROWTH (line, last 6 months) ----------
        $months = collect(range(5, 0))->map(function ($i) {
            return Carbon::now()->subMonths($i)->format('Y-m');
        });

        $patientsByMonth = UserAccount::where('role', 'patient')
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as total")
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $patientGrowthLabels = $months->map(fn ($m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'));
        $patientGrowthData = $months->map(fn ($m) => $patientsByMonth[$m] ?? 0);

        // ---------- PATIENTS BY AREA (bar) ----------
        $patientsByArea = UserAccount::where('role', 'patient')
            ->whereNotNull('Umunicipality')
            ->selectRaw('Umunicipality as municipality, COUNT(*) as total')
            ->groupBy('Umunicipality')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // ---------- RECENT ACTIVITY FEED ----------
        $recentActivity = ActivityLog::orderByDesc('created_at')->limit(8)->get();

        return view('admindashboard', compact(
            'totalPatients',
            'pendingAppointments',
            'pendingLabResults',
            'releasedLabResults',
            'appointmentStatusLabels',
            'appointmentStatusData',
            'labResultLabels',
            'labResultData',
            'patientGrowthLabels',
            'patientGrowthData',
            'patientsByArea',
            'recentActivity'
        ));
    }

            public function staffDashboard()
        {
            $today       = Carbon::today();
            $startOfWeek = $today->copy()->startOfWeek(Carbon::MONDAY);
            $endOfWeek   = $today->copy()->endOfWeek(Carbon::SUNDAY);

            // ---------- KPI CARDS ----------
            $todaysSchedule = Appointment::where('appointment_date', $today->toDateString())
                ->where('status', 'approved')
                ->count();

            $pendingRequests = Appointment::where('status', 'pending')->count();

            $completedThisWeek = Appointment::whereBetween('appointment_date', [
                    $startOfWeek->toDateString(), $endOfWeek->toDateString(),
                ])
                ->where('status', 'completed')
                ->count();

            $upcomingApproved = Appointment::where('status', 'approved')
                ->where('appointment_date', '>=', $today->toDateString())
                ->count();

            // ---------- APPOINTMENT STATUS BREAKDOWN (doughnut) ----------
            $knownStatuses = ['pending', 'approved', 'rescheduled', 'completed', 'cancelled', 'released'];
            $statusCounts = Appointment::selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $appointmentStatusLabels = collect($knownStatuses)->map(fn ($s) => ucfirst($s));
            $appointmentStatusData = collect($knownStatuses)->map(fn ($s) => $statusCounts[$s] ?? 0);

            // ---------- THIS WEEK'S SCHEDULE BY DAY (bar) ----------
            $weekDays = collect(range(0, 6))->map(fn ($i) => $startOfWeek->copy()->addDays($i));
            $scheduleCounts = Appointment::whereBetween('appointment_date', [
                    $startOfWeek->toDateString(), $endOfWeek->toDateString(),
                ])
                ->selectRaw('appointment_date, COUNT(*) as total')
                ->groupBy('appointment_date')
                ->pluck('total', 'appointment_date');

            $weekLabels = $weekDays->map(fn ($d) => $d->format('D'));
            $weekData   = $weekDays->map(fn ($d) => $scheduleCounts[$d->toDateString()] ?? 0);

            // ---------- HOME VS CLINIC SPLIT (doughnut) ----------
            // Adjust these values if appointment_type is stored differently.
            $homeCount   = Appointment::where('appointment_type', 'Home Service')->count();
            $clinicCount = Appointment::where('appointment_type', 'Online Booking')->count();

            // ---------- RECENT ACTIVITY (staff actions only) ----------
            $recentActivity = ActivityLog::where('user_role', 'staff')
                ->orderByDesc('created_at')
                ->limit(8)
                ->get();

            return view('staffdashboard', compact(
                'todaysSchedule',
                'pendingRequests',
                'completedThisWeek',
                'upcomingApproved',
                'appointmentStatusLabels',
                'appointmentStatusData',
                'weekLabels',
                'weekData',
                'homeCount',
                'clinicCount',
                'recentActivity'
            ));
        }

    // Delete account profile
    public function adminUserAccountsDestroy($id)
    {
        // Don't let an admin delete their own current profile session
        if (Auth::id() == $id) {
            return redirect()->back()->with('error', 'You are not allowed to delete your current administrator session.');
        }

        $user = UserAccount::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User account permanently scrubbed from system registry.');
    }

    // ------------------------------------------------------------
// Patient Self-Service Account Settings
// ------------------------------------------------------------

public function patientAccountSettingShow()
{
    $user = Auth::user();

    if (!$user->isPatient()) {
        abort(403, 'Unauthorized.');
    }

    return view('patient-profile.PSaccountsetting', compact('user'));
}

public function patientAccountSettingUpdate(Request $request)
{
    $user = Auth::user();

    if (!$user->isPatient()) {
        abort(403, 'Unauthorized.');
    }

    // NOTE: 'role' and 'id' are intentionally NOT in this validation list.
    // A patient must never be able to send them, even if they tamper with the form.
    $validated = $request->validate([
        'first_name'     => 'required|string|max:255',
        'middle_name'    => 'nullable|string|max:255',
        'last_name'      => 'required|string|max:255',
        'date_of_birth'  => 'required|date',
        'sex'            => 'required|in:male,female',
        'phone_number'   => 'required|string|max:20',
        'email'          => 'required|email|unique:useraccount,email,' . $user->id,
        'Umunicipality'  => 'required|string|max:255',
        'Ubarangay'      => 'required|string|max:255',
        'Ustreet_house'  => 'required|string|max:255',
        'contact_person' => 'required|string|max:255',
        'contact_number' => 'required|string|max:20',
    ]);

    // If the account is OAuth-linked, don't let them silently change the
    // login email out from under the linked provider.
    if ($user->oauth_provider && $validated['email'] !== $user->email) {
        return back()->with('error', 'Email is managed by your ' . ucfirst($user->oauth_provider) . ' sign-in and can\'t be changed here.');
    }

    // TODO: hook into your audit log here, e.g.:
    // ActivityLog::record($user, 'patient_self_update', $user->getOriginal(), $validated);

    $user->update($validated);

    return back()->with('success', 'Your profile has been updated successfully.');
}

public function patientPasswordUpdate(Request $request)
{
    $user = Auth::user();

    if (!$user->isPatient()) {
        abort(403, 'Unauthorized.');
    }

    if (is_null($user->password)) {
        return back()->with('error', 'This account uses Google/Facebook sign-in and has no password to change.');
    }

    $validated = $request->validate([
        'current_password'      => 'required|string',
        'new_password'          => 'required|string|min:6|confirmed',
    ]);

    if (!Hash::check($validated['current_password'], $user->password)) {
        return back()->with('error', 'Current password is incorrect.');
    }

    $user->update([
        'password' => Hash::make($validated['new_password']),
    ]);

    return back()->with('success', 'Password changed successfully.');
}
}