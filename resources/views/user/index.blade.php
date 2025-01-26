@extends('layouts.app')
@section('title', 'Classroom')

@section('content')
    <div class="main-alert-msg mx-5 my-2 alert" style="display:none;width:100%;max-width:500px;">
    </div>

    <div id="classessParent">
        
    </div>
    {{-- Home Modals --}}
    @extends('layouts.partials.homeModal')
    {{-- Home Modals --}}
@endsection 