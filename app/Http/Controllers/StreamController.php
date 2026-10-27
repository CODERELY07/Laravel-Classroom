<?php

namespace App\Http\Controllers;

use App\Models\ClassMember;
use App\Models\Classess;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StreamController extends Controller
{
    public function show(Request $request, Classess $class)
    {
        if (Gate::denies('view', $class)) {
            return redirect()->route('user.index');
        }

        $user = $request->user();
        $canTeach = Gate::allows('teach', $class);

        $class->load([
            'questions' => fn ($q) => $q->latest(),
            'materials' => fn ($q) => $q->latest(),
            'teachers' => fn ($q) => $q->orderBy('name'),
            'students' => fn ($q) => $q->orderBy('name'),
        ]);

        // Newest first: questions and materials interleaved by creation date.
        $feed = $class->questions->map(fn ($q) => ['type' => 'question', 'item' => $q])
            ->concat($class->materials->map(fn ($m) => ['type' => 'material', 'item' => $m]))
            ->sortByDesc(fn ($entry) => $entry['item']->created_at)
            ->values();

        $grades = null;
        if (! $canTeach && $membership = $class->membershipFor($user)) {
            $grades = $class->questions->load(['answers' => fn ($q) => $q->where('class_member_id', $membership->id)]);
        }

        return view('user.stream', [
            'class' => $class,
            'feed' => $feed,
            'teachers' => $class->teachers,
            'students' => $class->students,
            'canTeach' => $canTeach,
            'grades' => $grades,
        ]);
    }

    public function unenrollStudents(Request $request, Classess $class): JsonResponse
    {
        Gate::authorize('teach', $class);

        $data = $request->validate([
            'unenroll' => 'required|array|min:1',
            'unenroll.*' => 'integer',
        ], ['unenroll.required' => 'Select at least one student to remove.']);

        $removed = $class->members()
                         ->where('role', ClassMember::ROLE_STUDENT)
                         ->whereIn('user_id', $data['unenroll'])
                         ->delete();

        return $this->success("Removed {$removed} student(s) from the class.", redirectUrl: route('stream', $class->id).'#people');
    }

    /** Find people by email who are not already in the class. */
    public function search(Request $request, Classess $class): JsonResponse
    {
        Gate::authorize('teach', $class);

        $term = trim((string) $request->query('data'));

        if (mb_strlen($term) < 3) {
            return response()->json(['data' => []]);
        }

        $escaped = addcslashes($term, '%_\\');

        $users = User::query()
            ->where('email', 'like', "%{$escaped}%")
            ->whereDoesntHave('memberships', fn ($q) => $q->where('class_id', $class->id))
            ->orderBy('email')
            ->limit(10)
            ->get(['id', 'name', 'email', 'role', 'profile']);

        return response()->json([
            'data' => $users->map(fn (User $u) => [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'role' => $u->role,
                'initial' => $u->initial,
                'avatar_url' => $u->avatar_url,
            ]),
        ]);
    }

    public function store(Request $request, Classess $class): JsonResponse
    {
        Gate::authorize('teach', $class);

        $data = $request->validate([
            'added_users' => 'required|array|min:1|max:50',
            'added_users.*' => 'integer|exists:users,id',
        ], ['added_users.required' => 'Pick at least one person to add.']);

        $existing = $class->members()->pluck('user_id');
        $users = User::whereIn('id', $data['added_users'])->whereNotIn('id', $existing)->get();

        foreach ($users as $user) {
            $class->members()->create([
                'user_id' => $user->id,
                'role' => ClassMember::roleFor($user),
                'joined_at' => now(),
            ]);
        }

        return $this->success("Added {$users->count()} person(s) to the class.", redirectUrl: route('stream', $class->id).'#people');
    }
}
