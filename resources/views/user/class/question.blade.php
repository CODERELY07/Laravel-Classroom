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
                @else
                    <form class="panel" action="{{ route('turnIn') }}" method="POST">
                        @csrf
                        <input type="hidden" name="question_id" value="{{ $question->id }}">
                        <div class="panel-header">
                            <h2 class="panel-title">Your work</h2>
                            @if ($answer?->isGraded())
                                <span class="pill pill-accent">Graded</span>
                            @elseif ($answer)
                                <span class="pill pill-clay">Turned in</span>
                            @elseif ($question->isPastDue())
                                <span class="pill pill-danger">Missing</span>
                            @else
                                <span class="pill">Assigned</span>
                            @endif
                        </div>
                        <div class="panel-body">
                            @if ($answer?->isGraded())
                                <div class="answer-box">{{ $answer->answer }}</div>
                            @else
                                <label for="answer" class="visually-hidden">Your answer</label>
                                <textarea id="answer" name="answer" rows="8" maxlength="20000"
                                          @class(['form-control', 'is-invalid' => $errors->has('answer')])
                                          placeholder="Write your answers here. Number them to match the questions." required>{{ old('answer', $answer?->answer) }}</textarea>
                                @error('answer')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <button type="submit" class="btn btn-primary w-100 mt-3">
                                    {{ $answer ? 'Update answer' : 'Turn in' }}
                                </button>
                                @if ($answer)
                                    <p class="form-text text-center mb-0 mt-2">Turned in {{ $answer->updated_at->diffForHumans() }}. You can edit until it’s graded.</p>
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
