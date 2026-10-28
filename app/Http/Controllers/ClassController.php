<?php

namespace App\Http\Controllers;

use App\Models\ClassMember;
use App\Models\Classess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class ClassController extends Controller
{
    private const RULES = [
        'classname' => 'required|string|max:100',
        'section' => 'required|string|max:100',
        'subject' => 'required|string|max:100',
    ];

    private const ATTRIBUTES = ['classname' => 'class name', 'classcode' => 'class code'];

    public function create(Request $request): JsonResponse
    {
        Gate::authorize('create', Classess::class);

        $data = $request->validate(self::RULES, [], self::ATTRIBUTES);
        $user = $request->user();

        $class = DB::transaction(function () use ($data, $user) {
            $class = Classess::create($data + [
                'class_code' => Classess::generateCode(),
                'status' => Classess::STATUS_ACTIVE,
                'teacher_id' => $user->id,
            ]);

            $class->members()->create([
                'user_id' => $user->id,
                'role' => ClassMember::ROLE_TEACHER,
                'joined_at' => now(),
            ]);

            return $class;
        });

        return $this->success("“{$class->classname}” is ready. Share the code {$class->class_code} with your students.", redirectUrl: route('stream', $class->id));
    }

    public function join(Request $request): JsonResponse
    {
        $request->validate(['classcode' => 'required|string|max:20'], [], self::ATTRIBUTES);

        $user = $request->user();
        // Codes are generated lowercase; accept whatever case the student types.
        $class = Classess::where('class_code', Str::lower(trim($request->classcode)))->first();

        if (! $class || $class->isArchived()) {
            return $this->error('No active class uses that code. Double-check it with your teacher.', 404);
        }

        if ($class->members()->where('user_id', $user->id)->exists()) {
            return $this->error('You are already a member of this class.');
        }

        // A class code is shared with students, so it only ever grants student access.
        // Co-teachers are added by the owner from the People tab.
        $class->members()->create([
            'user_id' => $user->id,
            'role' => ClassMember::ROLE_STUDENT,
            'joined_at' => now(),
        ]);

        return $this->success("You joined {$class->classname}.", redirectUrl: route('stream', $class->id));
    }

    /** Re-rendered class grid, used to refresh the dashboard after changes. */
    public function show(Request $request): JsonResponse
    {
        return $this->grid($request, Classess::STATUS_ACTIVE);
    }

    public function showArchiveClass(Request $request): JsonResponse
    {
        return $this->grid($request, Classess::STATUS_ARCHIVED);
    }

    public function update(Request $request, Classess $class): JsonResponse
    {
        Gate::authorize('manage', $class);

        $class->update($request->validate(self::RULES, [], self::ATTRIBUTES));

        return $this->success('Class details saved.');
    }

    public function archive(Classess $class): JsonResponse
    {
        Gate::authorize('manage', $class);

        $class->update(['status' => Classess::STATUS_ARCHIVED]);

        return $this->success("{$class->classname} moved to the archive.");
    }

    public function unarchive(Classess $class): JsonResponse
    {
        Gate::authorize('manage', $class);

        $class->update(['status' => Classess::STATUS_ACTIVE]);

        return $this->success("{$class->classname} restored.");
    }

    /** Issue a new class code, e.g. after the old one was shared too widely. */
    public function resetCode(Classess $class): JsonResponse
    {
        Gate::authorize('manage', $class);
        $this->ensureActive($class);

        $class->update(['class_code' => Classess::generateCode()]);

        return $this->success("New class code: {$class->class_code}. The old code no longer works.", redirectUrl: route('stream', $class->id));
    }

    public function unenrollClass(Request $request, Classess $class): JsonResponse
    {
        Gate::authorize('view', $class);

        if (Gate::allows('manage', $class)) {
            return $this->error('You own this class. Archive or delete it instead of leaving.', 403);
        }

        $class->members()->where('user_id', $request->user()->id)->delete();

        return $this->success("You left {$class->classname}.");
    }

    public function destroy(Classess $class): JsonResponse
    {
        Gate::authorize('manage', $class);

        // Rows cascade in the database, but uploaded files have to be removed by hand.
        $class->materials()->whereNotNull('file')->get()->each->deleteFile();
        $class->delete();

        return $this->success('Class deleted.');
    }

    private function grid(Request $request, string $status): JsonResponse
    {
        $classess = Classess::forMember($request->user())
                            ->where('status', $status)
                            ->with('owner')
                            ->latest()
                            ->get();

        return response()->json([
            'html' => view('layouts.partials.classess', ['classess' => $classess, 'archived' => $status === Classess::STATUS_ARCHIVED])->render(),
            'count' => $classess->count(),
        ]);
    }
}
