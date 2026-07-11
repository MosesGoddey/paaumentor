<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        $field = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL)
                 ? 'email' : 'student_id';

        if (!Auth::attempt([$field => $credentials['login'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            return back()->withErrors(['login' => 'Invalid credentials.'])->withInput();
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$user->is_active) {
            Auth::logout();
            return back()->withErrors(['login' => 'Your account has been suspended.']);
        }

        $request->session()->regenerate();
        return redirect()->intended(
            $user->isAdmin()    ? route('admin.dashboard') :
            ($user->isVerifier() ? route('verifier.index')  :
            route('dashboard'))
        );
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name'   => 'required|string|max:60',
            'last_name'    => 'required|string|max:60',
            'email'        => 'required|email|unique:users',
            'student_id'   => 'required|string|unique:users',
            'department'   => 'required|string',
            'level'        => 'required|string',
            'role'         => ['required', Rule::in(['mentee', 'mentor', 'alumni'])],
            'password'     => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'github_url'   => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'bio'          => 'nullable|string|max:1000',
        ]);

        $isMentor = in_array($data['role'], ['mentor', 'alumni']);

        $user = User::create([
            ...$data,
            'is_verified'   => false,
            'mentor_status' => $isMentor ? 'pending' : null,
        ]);

        Auth::login($user);

        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Throwable) {}

        $message = $isMentor
            ? 'Welcome! Your mentor account is pending portfolio verification. You will be notified once approved.'
            : 'Welcome to PAAUMENTOR! Your account is ready.';

        return redirect()->route('dashboard')->with('success', $message);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
