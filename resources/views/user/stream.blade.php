@extends('layouts.app')
@section('title', $class->classname)
@section('crumb', $class->classname)

@push('head')
    {{-- Echo (realtime chat) is bundled by Vite; the page still works without a build. --}}
    @if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
        @vite('resources/js/app.js')
    @endif
@endpush

@php
    $tabs = [
        'stream' => ['Stream', 'fa-solid fa-bars-staggered'],
        'classwork' => ['Classwork', 'fa-regular fa-folder-open'],
        'people' => ['People', 'fa-solid fa-user-group'],
        'grade' => ['Grades', 'fa-solid fa-square-poll-vertical'],
        'chats' => ['Chat', 'fa-regular fa-comments'],
    ];
@endphp

@section('content')
<div class="page">
    {{-- Header --}}
    <section class="class-hero theme-{{ $class->themeIndex() }}" aria-labelledby="class-title">
        <h1 id="class-title">{{ $class->classname }}</h1>
        <div class="hero-meta">
            <span><i class="fa-solid fa-layer-group me-1" aria-hidden="true"></i>{{ $class->section }}</span>
            <span><i class="fa-solid fa-bookmark me-1" aria-hidden="true"></i>{{ $class->subject }}</span>
            @if ($class->isArchived())
                <span class="pill">Archived</span>
            @endif
        </div>
        @if ($canEdit)
            <div class="code-chip">
                <span>Class code</span>
                <code>{{ $class->class_code }}</code>
                <button type="button" data-copy="{{ $class->class_code }}">Copy</button>
                @if ($isOwner)
                    <button type="button"
                            data-confirm-url="{{ route('resetCode.post', $class->id) }}"
                            data-confirm-title="Reset the class code?"
                            data-confirm-body="The current code stops working right away. People already in the class stay in it."
                            data-confirm-label="Reset code">Reset</button>
                @endif
            </div>
        @endif
    </section>

    @if ($class->isArchived())
        <p class="alert alert-secondary mt-3 mb-0" role="status">
            <i class="fa-solid fa-box-archive me-1" aria-hidden="true"></i>
            This class is archived and read-only.{{ $isOwner ? ' Restore it from the Archived page to make changes.' : '' }}
        </p>
    @endif

    {{-- Tabs --}}
    <nav class="nav class-tabs" role="tablist" aria-label="Class sections">
        @foreach ($tabs as $key => [$label, $icon])
            <button type="button" role="tab" id="tab-{{ $key }}"
                    @class(['nav-link', 'active' => $loop->first])
                    data-bs-toggle="tab" data-bs-target="#{{ $key }}" data-tab="{{ $key }}"
                    aria-controls="{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                <i class="{{ $icon }}" aria-hidden="true"></i>{{ $label }}
            </button>
        @endforeach
    </nav>

    <div class="tab-content">
        {{-- Stream --}}
        <section id="stream" class="tab-pane fade show active" role="tabpanel" aria-labelledby="tab-stream" tabindex="0">
            @if ($feed->isEmpty())
                <x-empty-state icon="fa-regular fa-newspaper" title="Nothing posted yet">
                    <p>{{ $canTeach ? 'Post a material or a question from the Classwork tab and it will appear here.' : 'New materials and questions from your teacher will appear here.' }}</p>
                </x-empty-state>
            @else
                <div class="feed">
                    @foreach ($feed as $entry)
                        <x-feed-item :type="$entry['type']" :item="$entry['item']"
                                     :aside="'Posted '.$entry['item']->created_at?->diffForHumans()" />
                    @endforeach
                </div>
            @endif
        </section>

        {{-- Classwork --}}
        <section id="classwork" class="tab-pane fade" role="tabpanel" aria-labelledby="tab-classwork" tabindex="0">
            @if ($canEdit)
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createQuestionModal">
                        <i class="fa-solid fa-clipboard-question" aria-hidden="true"></i> New question
                    </button>
                    <button type="button" class="btn btn-quiet" data-bs-toggle="modal" data-bs-target="#createMaterialModal">
                        <i class="fa-solid fa-book-open" aria-hidden="true"></i> New material
                    </button>
                </div>
            @endif

            @if ($class->questions->isEmpty() && $class->materials->isEmpty())
                <x-empty-state icon="fa-regular fa-folder-open" title="No classwork yet">
                    <p>{{ $canTeach ? 'Questions collect answers you can grade. Materials share notes, slides or files.' : 'Your teacher hasn’t posted any work. You can ask about it in the class chat.' }}</p>
                </x-empty-state>
            @else
                <div class="section">
                    <div class="section-heading">
                        <h2>Questions <span class="count">{{ $class->questions->count() }}</span></h2>
                    </div>
                    @forelse ($class->questions as $question)
                        <div class="feed mb-2"><x-feed-item type="question" :item="$question" /></div>
                    @empty
                        <p class="text-muted-2">No questions yet.</p>
                    @endforelse
                </div>
                <div class="section">
                    <div class="section-heading">
                        <h2>Materials <span class="count">{{ $class->materials->count() }}</span></h2>
                    </div>
                    @forelse ($class->materials as $material)
                        <div class="feed mb-2"><x-feed-item type="material" :item="$material" /></div>
                    @empty
                        <p class="text-muted-2">No materials yet.</p>
                    @endforelse
                </div>
            @endif
        </section>

        {{-- People --}}
        <section id="people" class="tab-pane fade" role="tabpanel" aria-labelledby="tab-people" tabindex="0">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="panel">
                        <div class="panel-header">
                            <h2 class="panel-title">Teachers <span class="count text-muted-2 fw-normal">{{ $teachers->count() }}</span></h2>
                        </div>
                        <ul class="people-list px-3">
                            @foreach ($teachers as $teacher)
                                <li>
                                    <x-avatar :user="$teacher" size="sm" /><span class="person-name">{{ $teacher->name }}</span>
                                    @if ((int) $teacher->id === (int) $class->teacher_id)
                                        <span class="pill">Owner</span>
                                    @elseif ($isOwner && $canEdit)
                                        <button type="button" class="icon-btn" aria-label="Remove {{ $teacher->name }} as teacher" title="Remove teacher"
                                                data-confirm-url="{{ route('stream.removeTeacher', ['class' => $class->id, 'teacher' => $teacher->id]) }}"
                                                data-confirm-title="Remove {{ $teacher->name }}?"
                                                data-confirm-body="They will lose access to this class, including grades and chat."
                                                data-confirm-label="Remove" data-confirm-danger>
                                            <i class="fa-solid fa-user-minus" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="col-lg-7">
                    <form class="panel" action="{{ route('unenroll', $class->id) }}" data-ajax-form data-unenroll-form novalidate>
                        <div class="panel-header">
                            <h2 class="panel-title">Students <span class="count text-muted-2 fw-normal">{{ $students->count() }}</span></h2>
                            @if ($canEdit)
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-sm btn-quiet text-danger d-none" data-unenroll-submit>
                                        <i class="fa-solid fa-user-minus" aria-hidden="true"></i> Remove <span data-unenroll-count></span>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPeopleModal">
                                        <i class="fa-solid fa-user-plus" aria-hidden="true"></i> Add
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="form-alert mx-3 mt-3"></div>
                        @if ($students->isEmpty())
                            <p class="text-muted-2 p-3 mb-0">
                                {{ $canTeach ? 'No students yet. Share the class code or add people by email.' : 'You’re the first one here.' }}
                            </p>
                        @else
                            <ul class="people-list px-3">
                                @foreach ($students as $student)
                                    <li>
                                        @if ($canEdit)
                                            <input type="checkbox" class="form-check-input mt-0" name="unenroll[]" value="{{ $student->id }}" id="student-{{ $student->id }}" data-unenroll-checkbox>
                                            <label class="d-flex align-items-center gap-2 flex-fill min-w-0" for="student-{{ $student->id }}">
                                                <x-avatar :user="$student" size="sm" /><span class="person-name">{{ $student->name }}</span>
                                            </label>
                                        @else
                                            <x-avatar :user="$student" size="sm" /><span class="person-name">{{ $student->name }}</span>
                                            @if ($student->id === auth()->id())<span class="pill">You</span>@endif
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </form>
                </div>
            </div>
        </section>

        {{-- Grades --}}
        <section id="grade" class="tab-pane fade" role="tabpanel" aria-labelledby="tab-grade" tabindex="0">
            @if ($canTeach)
                @if ($students->isEmpty())
                    <x-empty-state icon="fa-solid fa-square-poll-vertical" title="No one to grade yet">
                        <p>Once students join, pick one here to review and grade their answers.</p>
                    </x-empty-state>
                @else
                    <div class="panel">
                        <div class="panel-header"><h2 class="panel-title">Choose a student to grade</h2></div>
                        <ul class="people-list px-3">
                            @foreach ($students as $student)
                                <li>
                                    <a class="person-row" href="{{ route('grade.show', ['class' => $class->id, 'student' => $student->id]) }}">
                                        <x-avatar :user="$student" size="sm" />
                                        <span class="person-name">{{ $student->name }}</span>
                                        <i class="fa-solid fa-chevron-right text-muted-2" aria-hidden="true"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            @elseif ($grades === null || $grades->isEmpty())
                <x-empty-state icon="fa-solid fa-square-poll-vertical" title="No graded work yet">
                    <p>Your grades will appear here once your teacher posts questions.</p>
                </x-empty-state>
            @else
                <div class="panel">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr><th scope="col" class="ps-3">Question</th><th scope="col">Status</th><th scope="col" class="text-end pe-3">Grade</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($grades as $question)
                                    @php $answer = $question->answers->first(); @endphp
                                    <tr>
                                        <td class="ps-3"><a href="{{ route('question.show', $question->id) }}">{{ $question->title }}</a></td>
                                        <td><x-work-status :question="$question" :answer="$answer" /></td>
                                        <td class="text-end pe-3 fw-medium">
                                            {{ $answer?->isGraded() ? $answer->grade : '—' }} <span class="text-muted-2">/ {{ $question->points }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </section>

        {{-- Chat --}}
        <section id="chats" class="tab-pane fade" role="tabpanel" aria-labelledby="tab-chats" tabindex="0">
            <div class="panel chat" id="chat"
                 data-class-id="{{ $class->id }}"
                 data-user-id="{{ auth()->id() }}"
                 data-history-url="{{ route('chats', $class->id) }}"
                 data-send-url="{{ route('send.message') }}">
                <div class="panel-header">
                    <h2 class="panel-title">Class chat</h2>
                    <span class="pill" data-chat-connection>Connecting…</span>
                </div>
                <div class="chat-log" data-chat-log role="log" aria-live="polite" aria-label="Messages">
                    <p class="chat-status" data-chat-status>Loading messages…</p>
                </div>
                <form class="chat-composer" data-chat-form>
                    <label for="chatMessage" class="visually-hidden">Message</label>
                    <textarea id="chatMessage" class="form-control" rows="1" maxlength="2000" required
                              placeholder="{{ $class->isArchived() ? 'This class is archived. Chat is read-only.' : 'Write a message — Enter to send, Shift+Enter for a new line' }}"
                              @disabled($class->isArchived())></textarea>
                    <button type="submit" class="btn btn-primary" aria-label="Send message" @disabled($class->isArchived())>
                        <i class="fa-solid fa-paper-plane" aria-hidden="true"></i>
                    </button>
                </form>
            </div>
        </section>
    </div>
</div>

@if ($canEdit)
    @include('user.partials.classworkModals', ['class' => $class])
@endif
@endsection
