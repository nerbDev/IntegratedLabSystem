<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserAccount;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
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
    // Dashboard
    // ------------------------------
    public function dashboard()
    {
        return view('admindashboard');
    }

    public function staffDashboard()
    {
        return view('staffdashboard');
    }

    public function patientDashboard()
    {
        return view('patientdashboard');
    }

    // ------------------------------------------------------------
    // Admin User Accounts Management Methods
    // ------------------------------------------------------------

    // Render list of accounts using ASuseraccounts view located in resources/views/
    public function adminUserAccountsIndex()
    {
        // Fetch all accounts from your custom useraccount table model
        $users = UserAccount::orderBy('created_at', 'desc')->get();
        return view('ASuseraccounts', compact('users'));
    }

    // Update account profile administrative values
    public function adminUserAccountsUpdate(Request $request, $id)
    {
        $user = UserAccount::findOrFail($id);

        $validated = $request->validate([
            'first_name'     => 'required|string|max:255',
            'middle_name'    => 'nullable|string|max:255',
            'last_name'      => 'required|string|max:255',
            'role'           => 'required|in:patient,staff,admin',
            'email'          => 'required|email|unique:useraccount,email,' . $id,
            'phone_number'   => 'required|string|max:20',
            'Umunicipality'  => 'required|string|max:255',
            'Ubarangay'      => 'required|string|max:255',
            'Ustreet_house'  => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'contact_number' => 'required|string|max:20',
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'User profile details successfully modified.');
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
}