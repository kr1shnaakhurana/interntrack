<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WebAuthController extends Controller
{
    // ─── Shared Login ────────────────────────────────────────────────────────

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (! $user->is_active) {
                Auth::logout();
                return back()->withErrors(['email' => 'Your account has been suspended or deactivated.']);
            }

            if ($user->isAdmin()) {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->isCompany()) {
                return redirect()->intended('/company/dashboard');
            } else {
                return redirect()->intended('/dashboard');
            }
        }

        return back()->withErrors(['email' => 'Invalid email address or password.'])->withInput($request->only('email'));
    }

    // ─── Student / Job Seeker Registration ───────────────────────────────────

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255|unique:users,email',
            'password'              => 'required|string|min:8|confirmed',
            'roll_no'               => 'nullable|string|max:100',
            'program'               => 'nullable|string|max:100',
        ]);

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name'      => $request->name,
                'email'     => strtolower(trim($request->email)),
                'password'  => $request->password,
                'role'      => 'student',
                'is_active' => true,
            ]);

            Student::create([
                'user_id' => $user->id,
                'roll_no' => $request->roll_no,
                'program' => $request->program,
            ]);

            return $user;
        });

        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Welcome to InternTrack! Your student account is ready.');
    }

    // ─── Employer / Company Registration ─────────────────────────────────────

    public function showCompanyRegister()
    {
        return view('auth.company-register');
    }

    public function registerCompany(Request $request)
    {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:users,email',
            'password'     => 'required|string|min:8|confirmed',
            'company_name' => 'required|string|max:255',
            'website'      => 'nullable|url|max:255',
            'industry'     => 'nullable|string|max:150',
            'size'         => 'nullable|string|max:50',
            'location'     => 'nullable|string|max:255',
            'sector'       => 'nullable|string|in:private,government,ngo,other',
        ]);

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'name'      => $request->name,
                'email'     => strtolower(trim($request->email)),
                'password'  => $request->password,
                'role'      => 'company',
                'is_active' => true,
            ]);

            Company::create([
                'user_id'     => $user->id,
                'name'        => $request->company_name,
                'website'     => $request->website,
                'industry'    => $request->industry,
                'size'        => $request->size,
                'location'    => $request->location,
                'sector'      => $request->sector ?? 'private',
                'is_claimed'  => true,
                'is_external' => false,
            ]);

            return $user;
        });

        Auth::login($user);

        return redirect('/company/dashboard')->with('success', 'Company account created! Welcome to InternTrack Employer Portal.');
    }

    // ─── Logout ──────────────────────────────────────────────────────────────

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Logged out successfully.');
    }
}
