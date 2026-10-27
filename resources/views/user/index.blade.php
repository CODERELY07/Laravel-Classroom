@extends('layouts.app')
@section('title', 'Classes')

@push('topbar-actions')
    <div class="dropdown">
        <button type="button" class="icon-btn" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Create or join a class" title="Create or join a class">
            <i class="fa-solid fa-plus" aria-hidden="true"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end">
            @if (auth()->user()->isTeacher())
                <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#createClassModal">
                    <i class="fa-solid fa-chalkboard" aria-hidden="true"></i> Create class
                </button>
            @endif
            <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#joinClassModal">
                <i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i> Join class
            </button>
        </div>
    </div>
@endpush

@section('content')
    <div class="page">
        <header class="page-header">
            <div>
                <h1 class="page-title">Good to see you, {{ Str::before($authUser->name, ' ') }}</h1>
                <p class="page-subtitle" data-class-count>
                    {{ $classess->count() }} active {{ Str::plural('class', $classess->count()) }}
                </p>
            </div>
        </header>

        <div id="classGrid" data-source="{{ route('classess.show') }}">
            @include('layouts.partials.classess', ['classess' => $classess, 'archived' => false])
        </div>
    </div>

    @include('layouts.partials.homeModal')
@endsection
