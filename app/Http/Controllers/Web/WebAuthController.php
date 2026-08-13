<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Company;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Account is suspended or inactive.']);
            }

            if ($user->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->isCompany()) {
                return redirect()->intended('/company/dashboard');
            } else {
                return redirect()->intended('/dashboard');
            }
        }

        return back()->withErrors(['email' => 'Invalid email or password credentials.']);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|in:student,company',
            // Student optional fields
            'roll_no' => 'nullable|string|max:100',
            'program' => 'nullable|string|max:100',
            // Company optional fields
            'company_name' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => $request->password,
            'role' => $request->role,
            'is_active' => true,
        ]);

        if ($user->isStudent()) {
            Student::create([
                'user_id' => $user->id,
                'roll_no' => $request->roll_no,
                'program' => $request->program,
            ]);
        } elseif ($user->isCompany()) {
            Company::create([
                'user_id' => $user->id,
                'name' => $request->company_name ?? $user->name,
                'website' => $request->website,
                'is_claimed' => true,
                'is_external' => false,
                'sector' => 'private',
            ]);
        }

        Auth::login($user);

        if ($user->isCompany()) {
            return redirect('/company/dashboard')->with('success', 'Company account registered successfully!');
        }

        return redirect('/dashboard')->with('success', 'Student account registered successfully!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logged out successfully.');
    }
}
