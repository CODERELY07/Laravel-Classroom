<?php

use App\Models\ClassMember;
use App\Models\ClassMemberQuestionAnswers;
use App\Models\Classess;
use App\Models\ClassroomChat;
use App\Models\Question;
use App\Models\User;

/** A class owned by $teacher with $students enrolled. */
function makeClass(User $teacher, array $students = []): Classess
{
    $class = Classess::create([
        'classname' => 'Biology',
        'section' => 'B',
        'subject' => 'Science',
        'class_code' => Classess::generateCode(),
        'status' => Classess::STATUS_ACTIVE,
        'teacher_id' => $teacher->id,
    ]);
    $class->members()->create(['user_id' => $teacher->id, 'role' => ClassMember::ROLE_TEACHER, 'joined_at' => now()]);

    foreach ($students as $student) {
        $class->members()->create(['user_id' => $student->id, 'role' => ClassMember::ROLE_STUDENT, 'joined_at' => now()]);
    }

    return $class;
}

function makeQuestion(Classess $class, int $points = 10): Question
{
    return $class->questions()->create([
        'title' => 'Review',
        'description' => 'Answer these',
        'question' => ['What is a cell?'],
        'due_date' => now()->addWeek()->toDateString(),
        'points' => $points,
        'user_id' => $class->teacher_id,
    ]);
}

// --- Pages ------------------------------------------------------------------

it('renders the public pages', function (string $uri) {
    $this->get($uri)->assertOk();
})->with(['/', '/login', '/signup', '/forget-password']);

it('renders every signed-in page for a teacher and a student', function () {
    $teacher = User::factory()->teacher()->create();
    $student = User::factory()->create();
    $class = makeClass($teacher, [$student]);
    $question = makeQuestion($class);
    $material = $class->materials()->create(['title' => 'Slides', 'description' => 'Week 1', 'user_id' => $teacher->id]);
    ClassMemberQuestionAnswers::create(['class_member_id' => $class->membershipFor($student)->id, 'question_id' => $question->id, 'answer' => 'A unit of life']);

    foreach ([$teacher, $student] as $user) {
        $this->actingAs($user);
        $this->get('/user')->assertOk()->assertSee('Biology');
        $this->get('/user/archive')->assertOk();
        $this->get('/user/setting')->assertOk();
        $this->get("/stream/{$class->id}")->assertOk();
        $this->get("/question/{$question->id}")->assertOk();
        $this->get("/material/{$material->id}")->assertOk()->assertSee('Slides');
    }

    $this->actingAs($teacher)->get("/grade/{$class->id}/user/{$student->id}")->assertOk()->assertSee('A unit of life');
});

// --- Authentication -----------------------------------------------------------

it('signs a new user up and in', function () {
    $this->postJson('/signup', [
        'name' => 'Jamie Rivera',
        'email' => 'Jamie@Example.com',
        'password' => 'secret-pass',
        'role' => 'Teacher',
        'terms' => '1',
    ])->assertOk()->assertJsonPath('redirectUrl', route('user.index'));

    $this->assertAuthenticated();
    expect(User::first())->email->toBe('jamie@example.com')->role->toBe('teacher');
});

it('throttles repeated failed logins', function () {
    $user = User::factory()->create();

    foreach (range(1, 5) as $_) {
        $this->postJson('/login', ['email' => $user->email, 'password' => 'wrong'])->assertStatus(422);
    }

    $this->postJson('/login', ['email' => $user->email, 'password' => 'password'])->assertStatus(429);
    $this->assertGuest();
});

it('only logs out through POST', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/logout')->assertStatus(405);
    $this->post('/logout')->assertRedirect('/login');
    $this->assertGuest();
});

it('does not reveal whether an email is registered', function () {
    $this->postJson('/forget-password', ['email' => 'nobody@example.com'])
         ->assertOk()
         ->assertJsonStructure(['success']);
});

// --- Account settings -----------------------------------------------------------

it('changes only the signed-in user’s password and requires the current one', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->postJson('/user/setting/password', [
        'current_password' => 'wrong',
        'newPass' => 'new-password',
        'newPass_confirmation' => 'new-password',
    ])->assertStatus(422)->assertJsonValidationErrors('current_password');

    $this->actingAs($user)->postJson('/user/setting/password', [
        'current_password' => 'password',
        'newPass' => 'new-password',
        'newPass_confirmation' => 'new-password',
    ])->assertOk();

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});

// --- Classes --------------------------------------------------------------------

it('lets teachers create classes and blocks students', function () {
    $teacher = User::factory()->teacher()->create();

    $this->actingAs($teacher)
         ->postJson('/createClass', ['classname' => 'Chem', 'section' => 'A', 'subject' => 'Science'])
         ->assertOk();

    $class = Classess::first();
    expect($class->teacher_id)->toBe($teacher->id)
        ->and($class->membershipFor($teacher)->role)->toBe(ClassMember::ROLE_TEACHER);

    $this->actingAs(User::factory()->create())
         ->postJson('/createClass', ['classname' => 'X', 'section' => 'A', 'subject' => 'Y'])
         ->assertForbidden();
});

it('joins a class by code once', function () {
    $class = makeClass(User::factory()->teacher()->create());
    $student = User::factory()->create();

    $this->actingAs($student)->postJson('/joinClass', ['classcode' => $class->class_code])->assertOk();
    $this->actingAs($student)->postJson('/joinClass', ['classcode' => $class->class_code])->assertStatus(422);

    expect($class->members()->where('user_id', $student->id)->count())->toBe(1);
});

it('does not leak user records in the class list JSON', function () {
    $teacher = User::factory()->teacher()->create();
    makeClass($teacher);

    $json = $this->actingAs($teacher)->getJson('/classess')->assertOk()->json();

    expect($json)->toHaveKeys(['html', 'count'])->not->toHaveKey('classess')
        ->and(json_encode($json))->not->toContain($teacher->password);
});

it('stops other teachers from managing a class they do not own', function () {
    $class = makeClass(User::factory()->teacher()->create());
    $intruder = User::factory()->teacher()->create();

    foreach (['updateClass', 'archiveClass', 'unarchiveClass', 'destroyClass'] as $action) {
        $this->actingAs($intruder)
             ->postJson("/{$action}/{$class->id}", ['classname' => 'x', 'section' => 'x', 'subject' => 'x'])
             ->assertForbidden();
    }

    expect(Classess::find($class->id))->not->toBeNull();
});

// --- Stream / people ------------------------------------------------------------

it('keeps non-members out of a class', function () {
    $class = makeClass(User::factory()->teacher()->create());
    $outsider = User::factory()->create();

    $this->actingAs($outsider)->get("/stream/{$class->id}")->assertRedirect('/user');
    $this->actingAs($outsider)->getJson("/chat/{$class->id}")->assertForbidden();
    $this->actingAs($outsider)->getJson("/stream/{$class->id}/search?data=example")->assertForbidden();
});

it('lets only teachers remove students', function () {
    $teacher = User::factory()->teacher()->create();
    [$a, $b] = User::factory()->count(2)->create();
    $class = makeClass($teacher, [$a, $b]);

    $this->actingAs($a)->postJson("/stream/{$class->id}/unenroll", ['unenroll' => [$b->id]])->assertForbidden();
    $this->actingAs($teacher)->postJson("/stream/{$class->id}/unenroll", ['unenroll' => [$b->id]])->assertOk();

    expect($class->students()->pluck('users.id')->all())->toBe([$a->id]);
});

it('searches people by email without exposing passwords or current members', function () {
    $teacher = User::factory()->teacher()->create();
    $member = User::factory()->create(['email' => 'member@school.test']);
    $other = User::factory()->create(['email' => 'other@school.test']);
    $class = makeClass($teacher, [$member]);

    $response = $this->actingAs($teacher)->getJson("/stream/{$class->id}/search?data=school.test")->assertOk();

    expect(collect($response->json('data'))->pluck('id')->all())->toBe([$other->id])
        ->and($response->json('data.0'))->not->toHaveKey('password');
});

it('adds people without creating duplicate memberships', function () {
    $teacher = User::factory()->teacher()->create();
    $student = User::factory()->create();
    $class = makeClass($teacher, [$student]);
    $newcomer = User::factory()->create();

    $this->actingAs($teacher)
         ->postJson("/stream/{$class->id}/members", ['added_users' => [$student->id, $newcomer->id]])
         ->assertOk();

    expect($class->members()->count())->toBe(3);
});

// --- Classwork, answers and grades --------------------------------------------

it('lets only class teachers post questions and materials', function () {
    $teacher = User::factory()->teacher()->create();
    $student = User::factory()->create();
    $class = makeClass($teacher, [$student]);

    $question = [
        'class_id' => $class->id, 'title' => 'Q', 'description' => 'D',
        'question' => ['One'], 'points' => 5, 'duedate' => now()->addDay()->toDateString(),
    ];

    $this->actingAs($student)->postJson('/question', $question)->assertForbidden();
    $this->actingAs($teacher)->postJson('/question', $question)->assertOk();

    $this->actingAs($teacher)->postJson('/material/store', ['class_id' => $class->id, 'title' => 'M', 'description' => 'No file'])->assertOk();

    expect($class->questions()->first()->items())->toBe(['One'])
        ->and($class->materials()->first()->file)->toBeNull();
});

it('records answers for the signed-in student only', function () {
    $teacher = User::factory()->teacher()->create();
    [$alice, $bob] = User::factory()->count(2)->create();
    $class = makeClass($teacher, [$alice, $bob]);
    $question = makeQuestion($class);

    // A forged class_member_id is ignored: the answer belongs to Alice.
    $this->actingAs($alice)->post('/question/turnin', [
        'question_id' => $question->id,
        'answer' => 'Mine',
        'class_member_id' => $class->membershipFor($bob)->id,
    ])->assertRedirect("/question/{$question->id}");

    expect(ClassMemberQuestionAnswers::sole()->class_member_id)->toBe($class->membershipFor($alice)->id);
});

it('grades only within the class and within the points limit', function () {
    $teacher = User::factory()->teacher()->create();
    $student = User::factory()->create();
    $class = makeClass($teacher, [$student]);
    $question = makeQuestion($class, points: 10);
    $answer = ClassMemberQuestionAnswers::create([
        'class_member_id' => $class->membershipFor($student)->id,
        'question_id' => $question->id,
        'answer' => 'x',
    ]);

    $this->actingAs($student)->postJson('/grade/update', ['answer_id' => $answer->id, 'grade' => 10])->assertForbidden();
    $this->actingAs(User::factory()->teacher()->create())->postJson('/grade/update', ['answer_id' => $answer->id, 'grade' => 10])->assertForbidden();
    $this->actingAs($teacher)->postJson('/grade/update', ['answer_id' => $answer->id, 'grade' => 11])->assertStatus(422);
    $this->actingAs($teacher)->postJson('/grade/update', ['answer_id' => $answer->id, 'grade' => 9])->assertOk();

    expect($answer->fresh()->grade)->toBe(9);
});

it('shows each student their own grades, not a classmate’s', function () {
    $teacher = User::factory()->teacher()->create();
    [$alice, $bob] = User::factory()->count(2)->create();
    $class = makeClass($teacher, [$alice, $bob]);
    $question = makeQuestion($class);
    $answer = ClassMemberQuestionAnswers::create(['class_member_id' => $class->membershipFor($bob)->id, 'question_id' => $question->id, 'answer' => 'b']);
    $answer->forceFill(['grade' => 7])->save();

    $this->actingAs($alice)->get("/stream/{$class->id}")->assertOk()->assertSee('Not turned in')->assertDontSee('Graded');
});

// --- Chat -----------------------------------------------------------------------

it('accepts chat messages from class members only', function () {
    $teacher = User::factory()->teacher()->create();
    $class = makeClass($teacher);

    $this->actingAs($teacher)
         ->postJson('/fire/message', ['class_id' => $class->id, 'message' => '<script>alert(1)</script>'])
         ->assertOk()
         ->assertJsonPath('chat.user.id', $teacher->id);

    expect(ClassroomChat::sole()->user_id)->toBe($teacher->id);

    $this->actingAs(User::factory()->create())
         ->postJson('/fire/message', ['class_id' => $class->id, 'message' => 'hi'])
         ->assertForbidden();
});
