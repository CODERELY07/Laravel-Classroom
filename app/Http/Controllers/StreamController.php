<?php

namespace App\Http\Controllers;

use App\Models\ClassMember;
use App\Models\Classess;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

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
            'canEdit' => $canTeach && ! $class->isArchived(),
            'isOwner' => Gate::allows('manage', $class),
            'grades' => $grades,
        ]);
    }

    public function unenrollStudents(Request $request, Classess $class): JsonResponse
    {
        Gate::authorize('teach', $class);
        $this->ensureActive($class);

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

    /** The owner removes a co-teacher. */
    public function removeTeacher(Classess $class, User $teacher): JsonResponse
    {
        Gate::authorize('manage', $class);
        $this->ensureActive($class);

        if ((int) $teacher->id === (int) $class->teacher_id) {
            return $this->error('The class owner cannot be removed.', 403);
        }

        $class->members()
              ->where('user_id', $teacher->id)
              ->where('role', ClassMember::ROLE_TEACHER)
              ->delete();

        return $this->success("{$teacher->name} is no longer a teacher in this class.", redirectUrl: route('stream', $class->id).'#people');
    }

    /**
     * Look up one person by their exact email address.
     *
     * Partial matches are deliberately not supported: they would let any
     * teacher account browse the names and emails of every user.
     */
    public function search(Request $request, Classess $class): JsonResponse
    {
        Gate::authorize('teach', $class);

        $email = Str::lower(trim((string) $request->query('data')));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['data' => []]);
        }

        $users = User::query()
            ->where('email', $email)
            ->whereDoesntHave('memberships', fn ($q) => $q->where('class_id', $class->id))
            ->limit(1)
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
        $this->ensureActive($class);

        $data = $request->validate([
            'added_users' => 'required|array|min:1|max:50',
            'added_users.*' => 'integer|exists:users,id',
        ], ['added_users.required' => 'Pick at least one person to add.']);

        // Only the owner can grant teacher rights; everyone else is added as a student.
        $canAddTeachers = Gate::allows('manage', $class);

        $existing = $class->members()->pluck('user_id');
        $users = User::whereIn('id', $data['added_users'])->whereNotIn('id', $existing)->get();

        foreach ($users as $user) {
            $class->members()->create([
                'user_id' => $user->id,
                'role' => $canAddTeachers ? ClassMember::roleFor($user) : ClassMember::ROLE_STUDENT,
                'joined_at' => now(),
            ]);
        }

        return $this->success("Added {$users->count()} person(s) to the class.", redirectUrl: route('stream', $class->id).'#people');
    }
}
