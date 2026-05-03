<?php

namespace App\Http\Controllers;

use App\Models\{User, Skill};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Storage};

class ProfileController extends Controller
{
    public function show(User $user)
    {
        $user->load(['hasSkills', 'ratings.rater', 'certificates.learningPath']);
        return view('profile.show', compact('user'));
    }

    public function edit()
    {
        $user   = Auth::user();
        $skills = Skill::orderBy('name')->get();
        return view('profile.edit', compact('user', 'skills'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'first_name'   => 'required|string|max:60',
            'last_name'    => 'required|string|max:60',
            'bio'          => 'nullable|string|max:500',
            'phone'        => 'nullable|string|max:20',
            'availability' => 'nullable|string|max:100',
            'avatar'       => 'nullable|image|max:2048',
            'skill_ids'    => 'nullable|array',
            'skill_ids.*'  => 'exists:skills,id',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) Storage::disk('public')->delete($user->avatar);
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update(\Arr::except($data, ['skill_ids', 'avatar']) + ['avatar' => $data['avatar'] ?? $user->avatar]);

        if ($request->has('skill_ids')) {
            $user->skills()->sync(collect($request->skill_ids)->mapWithKeys(fn($id) => [$id => ['type' => 'has']]));
        }

        return back()->with('success', 'Profile updated!');
    }
}
