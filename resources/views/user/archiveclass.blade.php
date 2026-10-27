@extends('layouts.app')
@section('title', 'Archived classes')
@section('crumb', 'Archived')

@section('content')
    <div class="page">
        <header class="page-header">
            <div>
                <h1 class="page-title">Archived classes</h1>
                <p class="page-subtitle">Finished classes wait here, out of everyone's way, until you restore or delete them.</p>
            </div>
        </header>

        <div id="classGrid" data-source="{{ route('archive.show') }}">
            @include('layouts.partials.classess', ['classess' => $classess, 'archived' => true])
        </div>
    </div>
@endsection
