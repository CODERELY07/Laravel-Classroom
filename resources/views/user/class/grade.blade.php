@extends('layouts.app')
@section('title', 'Grades · '.$student->name)
@section('crumb', $class->classname)

@section('content')
<div class="page-narrow">
    <a href="{{ route('stream', $class->id) }}#grade" class="btn btn-ghost btn-sm mb-3">
        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> All students
    </a>

    <header class="page-header align-items-center">
        <div class="d-flex align-items-center gap-3 min-w-0">
            <x-avatar :user="$student" size="lg" />
            <div class="min-w-0">
                <h1 class="page-title text-truncate">{{ $student->name }}</h1>
                <p class="page-subtitle text-truncate">{{ $student->email }}</p>
            </div>
        </div>
        @php
            $earned = $questions->sum(fn ($q) => $q->answers->first()?->grade ?? 0);
            $possible = $questions->filter(fn ($q) => $q->answers->first()?->isGraded())->sum('points');
        @endphp
        <div class="text-end">
            <div class="small text-muted-2">Graded total</div>
            <div class="fs-4 fw-semibold" data-grade-total>{{ $earned }} <span class="fs-6 text-muted-2">/ {{ $possible }}</span></div>
        </div>
    </header>

    @forelse ($questions as $question)
        @php $answer = $question->answers->first(); @endphp
        <article class="panel mb-3">
            <div class="panel-header">
                <div class="min-w-0">
                    <h2 class="panel-title text-truncate">
                        <a href="{{ route('question.show', $question->id) }}" class="text-reset">{{ $question->title }}</a>
                    </h2>
                    <div class="small text-muted-2">Due {{ $question->due_date?->format('M j, Y') }} · {{ $question->points }} points</div>
                </div>
                @if (! $answer)
                    <span @class(['pill', 'pill-danger' => $question->isPastDue()])>{{ $question->isPastDue() ? 'Missing' : 'Not turned in' }}</span>
                @endif
            </div>
            <div class="panel-body">
                @if ($items = $question->items())
                    <ol class="question-list mb-3 small text-muted-2">
                        @foreach ($items as $item)<li>{{ $item }}</li>@endforeach
                    </ol>
                @endif

                @if ($answer)
                    <div class="answer-box">{{ $answer->answer }}</div>
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                        <label for="grade-{{ $answer->id }}" class="form-label mb-0">Grade</label>
                        <div class="input-group" style="width: auto">
                            <input type="number" id="grade-{{ $answer->id }}" class="form-control grade-input"
                                   min="0" max="{{ $question->points }}" step="1" inputmode="numeric"
                                   value="{{ $answer->grade }}" placeholder="—"
                                   data-grade-input data-answer-id="{{ $answer->id }}" data-url="{{ route('grade.update') }}">
                            <span class="input-group-text">/ {{ $question->points }}</span>
                        </div>
                        <span class="save-state" data-save-state aria-live="polite"></span>
                    </div>
                    <p class="form-text mb-0">Turned in {{ $answer->updated_at->diffForHumans() }}. Grades save automatically.</p>
                @else
                    <p class="text-muted-2 mb-0">{{ $student->name }} hasn’t turned this in yet.</p>
                @endif
            </div>
        </article>
    @empty
        <x-empty-state icon="fa-solid fa-clipboard-question" title="No questions to grade">
            <p>Post a question from the Classwork tab first.</p>
        </x-empty-state>
    @endforelse
</div>
@endsection
