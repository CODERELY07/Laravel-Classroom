@props(['class', 'archived' => false])
@php
    $owner = $class->owner;
    $isOwner = (int) $class->teacher_id === (int) auth()->id();
@endphp
<article class="class-card">
    <div class="class-banner theme-{{ $class->themeIndex() }}">
        <a href="{{ route('stream', $class->id) }}" class="class-name">{{ $class->classname }}</a>
        <div class="class-section">{{ $class->section }}</div>
        @unless ($isOwner)
            <div class="class-teacher">{{ $owner?->name }}</div>
        @endunless

        <div class="dropdown class-menu">
            <button type="button" class="icon-btn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Options for {{ $class->classname }}">
                <i class="fa-solid fa-ellipsis-vertical" aria-hidden="true"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                @if ($isOwner)
                    @unless ($archived)
                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editClassModal"
                                data-action="{{ route('updateClass.post', $class->id) }}"
                                data-classname="{{ $class->classname }}"
                                data-section="{{ $class->section }}"
                                data-subject="{{ $class->subject }}">
                            <i class="fa-solid fa-pen" aria-hidden="true"></i> Edit details
                        </button>
                        <button type="button" class="dropdown-item" data-copy="{{ $class->class_code }}">
                            <i class="fa-regular fa-copy" aria-hidden="true"></i> Copy class code
                        </button>
                        <button type="button" class="dropdown-item"
                                data-confirm-url="{{ route('archiveClass.post', $class->id) }}"
                                data-confirm-title="Archive {{ $class->classname }}?"
                                data-confirm-body="Archived classes are hidden from everyone's home page. You can restore it any time."
                                data-confirm-label="Archive">
                            <i class="fa-solid fa-box-archive" aria-hidden="true"></i> Archive
                        </button>
                    @else
                        <button type="button" class="dropdown-item"
                                data-confirm-url="{{ route('unarchiveClass.post', $class->id) }}"
                                data-confirm-title="Restore {{ $class->classname }}?"
                                data-confirm-body="The class will appear on everyone's home page again."
                                data-confirm-label="Restore">
                            <i class="fa-solid fa-rotate-left" aria-hidden="true"></i> Restore
                        </button>
                        <button type="button" class="dropdown-item text-danger"
                                data-confirm-url="{{ route('destroyClass.post', $class->id) }}"
                                data-confirm-title="Delete {{ $class->classname }} for good?"
                                data-confirm-body="All posts, submissions, grades and chat history in this class will be permanently removed."
                                data-confirm-label="Delete class"
                                data-confirm-danger>
                            <i class="fa-regular fa-trash-can" aria-hidden="true"></i> Delete
                        </button>
                    @endunless
                @else
                    <button type="button" class="dropdown-item text-danger"
                            data-confirm-url="{{ route('unenrollClass.post', $class->id) }}"
                            data-confirm-title="Leave {{ $class->classname }}?"
                            data-confirm-body="You'll lose access to its classwork and chat. You can rejoin later with the class code."
                            data-confirm-label="Leave class"
                            data-confirm-danger>
                        <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i> Leave class
                    </button>
                @endif
            </div>
        </div>

        @unless ($isOwner)
            <x-avatar :user="$owner" size="lg" class="avatar-ring class-owner-avatar" />
        @endunless
    </div>

    <div class="class-body">
        <span class="subject"><i class="fa-solid fa-bookmark" aria-hidden="true"></i> {{ $class->subject }}</span>
    </div>

    <div class="class-footer">
        <a href="{{ route('stream', $class->id) }}#classwork" class="icon-btn" aria-label="Classwork for {{ $class->classname }}" title="Classwork">
            <i class="fa-regular fa-folder-open" aria-hidden="true"></i>
        </a>
        <a href="{{ route('stream', $class->id) }}#chats" class="icon-btn" aria-label="Chat for {{ $class->classname }}" title="Chat">
            <i class="fa-regular fa-comments" aria-hidden="true"></i>
        </a>
    </div>
</article>
