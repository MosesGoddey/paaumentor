<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name'  => ['required', 'string', 'max:100'],
            'last_name'   => ['required', 'string', 'max:100'],
            'email'       => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'student_id'  => ['required', 'string', 'max:20', 'unique:' . User::class],
            'role'        => ['required', 'in:mentee,mentor,alumni'],
            'department'  => ['required', 'string', 'max:150'],
            'level'       => ['required', 'string', 'max:20'],
            'password'    => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $mentorStatus = in_array($request->role, ['mentor', 'alumni']) ? 'pending' : null;

        $user = User::create([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'email'         => $request->email,
            'student_id'    => $request->student_id,
            'role'          => $request->role,
            'department'    => $request->department,
            'level'         => $request->level,
            'password'      => Hash::make($request->password),
            'is_active'     => true,
            'mentor_status' => $mentorStatus,
        ]);

        event(new Registered($user));

        // Send welcome email — silently skip if mail is not configured
        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Throwable) {}

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
