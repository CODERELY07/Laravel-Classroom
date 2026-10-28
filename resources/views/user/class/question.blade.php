@extends('layouts.app')
@section('title', $question->title)
@section('crumb', $class->classname)

@section('content')
<div class="page">
    <a href="{{ route('stream', $class->id) }}#classwork" class="btn btn-ghost btn-sm mb-3">
        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back to {{ $class->classname }}
    </a>

    <div class="row g-4">
        <div class="col-lg-8">
            <article class="panel">
                <div class="panel-body">
                    <header class="article-header">
                        <span class="feed-icon" style="background: var(--clay-soft); color: var(--clay)" aria-hidden="true">
                            <i class="fa-solid fa-clipboard-question"></i>
                        </span>
                        <div class="min-w-0">
                            <h1 class="article-title">{{ $question->title }}</h1>
                            <div class="article-meta">
                                <span>{{ $question->user->name }}</span>
                                <span>Posted {{ $question->created_at->format('M j, Y') }}</span>
                            </div>
                        </div>
                    </header>

                    <hr class="my-4">

                    <p class="prose">{{ $question->description }}</p>

                    @if ($items = $question->items())
                        <h2 class="h6 mt-4 mb-2">Questions</h2>
                        <ol class="question-list">
                            @foreach ($items as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ol>
                    @endif
                </div>
            </article>
        </div>

        <aside class="col-lg-4">
            <div class="sticky-aside d-flex flex-column gap-3">
                <div class="panel">
                    <div class="panel-body py-2">
                        <div class="stat"><span>Due</span><strong @class(['text-danger' => $question->isPastDue()])>{{ $question->due_date?->format('D, M j, Y') }}</strong></div>
                        <div class="stat"><span>Points</span><strong>{{ $question->points }}</strong></div>
                        @if ($canTeach)
                            <div class="stat"><span>Turned in</span><strong>{{ $submissionCount }} / {{ $studentCount }}</strong></div>
                        @elseif ($answer?->isGraded())
                            <div class="stat"><span>Your grade</span><strong class="text-success">{{ $answer->grade }} / {{ $question->points }}</strong></div>
                        @endif
                    </div>
                </div>

                @if ($canTeach)
                    <a href="{{ route('stream', $class->id) }}#grade" class="btn btn-primary">
                        <i class="fa-solid fa-square-poll-vertical" aria-hidden="true"></i> Grade submissions
                    </a>
                    @unless ($class->isArchived())
                        <button type="button" class="btn btn-quiet text-danger"
                                data-confirm-url="{{ route('question.destroy', $question->id) }}"
                                data-confirm-title="Delete “{{ $question->title }}”?"
                                data-confirm-body="Every student answer and grade for this question will be permanently deleted."
                                data-confirm-label="Delete question" data-confirm-danger>
                            <i class="fa-regular fa-trash-can" aria-hidden="true"></i> Delete question
                        </button>
                    @endunless
                @else
                    <form class="panel" action="{{ route('turnIn') }}" method="POST">
                        @csrf
                        <input type="hidden" name="question_id" value="{{ $question->id }}">
                        <div class="panel-header">
                            <h2 class="panel-title">Your work</h2>
                            @if ($answer || $question->isPastDue())
                                <x-work-status :question="$question" :answer="$answer" />
                            @else
                                <span class="pill">Assigned</span>
                            @endif
                        </div>
                        <div class="panel-body">
                            @if ($answer?->isGraded() || ($answer && $question->isPastDue()) || $class->isArchived())
                                {{-- Locked: graded, past the deadline, or the class is archived. --}}
                                @if ($answer)
                                    <div class="answer-box">{{ $answer->answer }}</div>
                                @endif
                                <p class="form-text mb-0 mt-2">
                                    @if ($class->isArchived())
                                        This class is archived, so work can no longer be turned in.
                                    @elseif ($answer->isGraded())
                                        Graded. Your answer can no longer be changed.
                                    @else
                                        The due date has passed, so your answer can no longer be changed.
                                    @endif
                                </p>
                            @else
                                <label for="answer" class="visually-hidden">Your answer</label>
                                <textarea id="answer" name="answer" rows="8" maxlength="20000"
                                          @class(['form-control', 'is-invalid' => $errors->has('answer')])
                                          placeholder="Write your answers here. Number them to match the questions." required>{{ old('answer', $answer?->answer) }}</textarea>
                                @error('answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <button type="submit" class="btn btn-primary w-100 mt-3">
                                    {{ $answer ? 'Update answer' : ($question->isPastDue() ? 'Turn in late' : 'Turn in') }}
                                </button>
                                @if ($answer)
                                    <p class="form-text text-center mb-0 mt-2">Turned in {{ $answer->updated_at->diffForHumans() }}. You can edit it until it’s graded or the due date passes.</p>
                                @elseif ($question->isPastDue())
                                    <p class="form-text text-center mb-0 mt-2">The due date has passed. You can still turn this in, but it will be marked late.</p>
                                @endif
                            @endif
                        </div>
                    </form>
                @endif
            </div>
        </aside>
    </div>
</div>
@endsection
