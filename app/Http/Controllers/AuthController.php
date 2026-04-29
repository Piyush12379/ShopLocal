<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Show the login page
    public function showLogin()
    {
        // If already logged in, redirect to their dashboard
        if (auth()->check()) {
            return $this->redirectByRole(auth()->user());
        }
        return view('auth.login');
    }

    // Show the register page
    public function showRegister()
    {
        if (auth()->check()) {
            return $this->redirectByRole(auth()->user());
        }
        return view('auth.register');
    }

    // Handle the registration form submission
    public function register(Request $request)
    {
        // Step 1: Validate the form data
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed', // confirmed = must have password_confirmation field
            'role'     => 'required|in:customer,shopkeeper',
        ]);

        // Step 2: Create the user in the database
        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password), // NEVER store plain text passwords
            'role'        => $request->role,
            'is_approved' => $request->role === 'customer', // customers auto-approved, shopkeepers wait
        ]);

        // Step 3: Log them in automatically
        Auth::login($user);

        // Step 4: Send them to the right dashboard
        return $this->redirectByRole($user);
    }

    // Handle the login form submission
    public function login(Request $request)
    {
        // Step 1: Validate
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Step 2: Attempt login
        // attempt() checks email+password and creates a session if correct
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            $request->session()->regenerate(); // Security: regenerate session ID after login
            return $this->redirectByRole(auth()->user());
        }

        // Step 3: If login failed, go back with error message
        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    // Log the user out
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    // Helper: send user to the right page based on their role
    private function redirectByRole(User $user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isShopkeeper()) {
            // If not approved yet, show waiting page
            if (!$user->isApproved()) {
                return redirect()->route('vendor.pending');
            }
            return redirect()->route('vendor.dashboard');
        }

        // Default: customer goes to the shop
        return redirect()->route('home');
    }
}