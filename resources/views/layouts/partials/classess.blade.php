@if ($classess->isEmpty())
    @if ($archived ?? false)
        <x-empty-state icon="fa-solid fa-box-archive" title="Nothing in the archive">
            <p>When a class wraps up, archive it from its card menu to keep your home page tidy. It will wait here.</p>
        </x-empty-state>
    @else
        <x-empty-state icon="fa-solid fa-chalkboard" title="No classes yet">
            @if (auth()->user()->isTeacher())
                <p>Create your first class, then share its code with students so they can join.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createClassModal">
                    <i class="fa-solid fa-plus" aria-hidden="true"></i> Create a class
                </button>
            @else
                <p>Ask your teacher for a class code and enter it to join.</p>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#joinClassModal">
                    <i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i> Join a class
                </button>
            @endif
        </x-empty-state>
    @endif
@else
    <div class="class-grid">
        @foreach ($classess as $class)
            <x-class-card :class="$class" :archived="$archived ?? false" />
        @endforeach
    </div>
@endif
