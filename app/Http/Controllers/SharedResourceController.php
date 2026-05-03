<?php

namespace App\Http\Controllers;

use App\Models\{SharedResource, Mentorship};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Storage};

class SharedResourceController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $mentorshipIds = Mentorship::where('mentor_id', $user->id)
                                   ->orWhere('mentee_id', $user->id)
                                   ->pluck('id');

        $groupIds = $user->studyGroups()->pluck('study_groups.id');

        $resources = SharedResource::with('uploader', 'studyGroup', 'mentorship')
            ->where(function ($q) use ($user, $mentorshipIds, $groupIds) {
                $q->where('is_public', true)
                  ->orWhere('uploader_id', $user->id)
                  ->orWhereIn('mentorship_id', $mentorshipIds)
                  ->orWhereIn('study_group_id', $groupIds);
            })
            ->latest()
            ->get();

        return view('resources.index', compact('user', 'resources'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
            'file'        => 'required|file|max:51200',
            'is_public'   => 'nullable|boolean',
        ]);

        $file = $request->file('file');
        $path = $file->store('resources', 'public');

        SharedResource::create([
            'title'       => $request->title,
            'description' => $request->description,
            'file_path'   => $path,
            'file_name'   => $file->getClientOriginalName(),
            'file_size'   => $file->getSize(),
            'mime_type'   => $file->getMimeType(),
            'uploader_id' => Auth::id(),
            'is_public'   => $request->boolean('is_public', true),
        ]);

        return back()->with('success', 'Resource uploaded successfully.');
    }

    public function destroy(SharedResource $resource)
    {
        abort_unless($resource->uploader_id === Auth::id() || Auth::user()->isAdmin(), 403);
        Storage::disk('public')->delete($resource->file_path);
        $resource->delete();
        return back()->with('success', 'Resource deleted.');
    }
}
