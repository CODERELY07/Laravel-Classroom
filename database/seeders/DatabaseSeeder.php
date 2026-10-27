<?php

namespace Database\Seeders;

use App\Models\ClassMember;
use App\Models\ClassMemberQuestionAnswers;
use App\Models\Classess;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Demo data for local development. Every account's password is "password".
 *   teacher@example.com  (teacher, owns two classes)
 *   student@example.com  (student, enrolled in both)
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::factory()->teacher()->create(['name' => 'Morgan Ellis', 'email' => 'teacher@example.com']);
        $student = User::factory()->create(['name' => 'Ava Santos', 'email' => 'student@example.com']);
        $classmates = User::factory()->count(4)->create();

        foreach ([['Grade 10 Biology', 'Section B · Mornings', 'Science'], ['World History', 'Period 3', 'Social Studies']] as [$name, $section, $subject]) {
            $class = Classess::create([
                'classname' => $name,
                'section' => $section,
                'subject' => $subject,
                'class_code' => Classess::generateCode(),
                'status' => Classess::STATUS_ACTIVE,
                'teacher_id' => $teacher->id,
            ]);

            $class->members()->create(['user_id' => $teacher->id, 'role' => ClassMember::ROLE_TEACHER, 'joined_at' => now()]);
            foreach ($classmates->concat([$student]) as $member) {
                $class->members()->create(['user_id' => $member->id, 'role' => ClassMember::ROLE_STUDENT, 'joined_at' => now()]);
            }

            $class->materials()->create([
                'user_id' => $teacher->id,
                'title' => 'Course outline',
                'description' => "What we'll cover this term, how grading works, and where to find help.",
            ]);

            $question = $class->questions()->create([
                'user_id' => $teacher->id,
                'title' => 'Week 1 review',
                'description' => 'Answer each question in two or three sentences.',
                'question' => ['What did you find most interesting this week?', 'Name one question you still have.'],
                'due_date' => now()->addDays(5)->toDateString(),
                'points' => 20,
            ]);

            $answer = ClassMemberQuestionAnswers::create([
                'class_member_id' => $class->membershipFor($student)->id,
                'question_id' => $question->id,
                'answer' => "1. How cells divide.\n2. Why some cells stop dividing.",
            ]);
            $answer->forceFill(['grade' => 18])->save();

            $class->messages()->create(['user_id' => $teacher->id, 'message' => 'Welcome to the class! Post questions here any time.']);
            $class->messages()->create(['user_id' => $student->id, 'message' => 'Thanks! Is the review due Friday?']);
        }
    }
}
