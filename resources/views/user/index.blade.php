@extends('layouts.app')
@section('title', 'Classroom')

@section('content')
    <?php $flag = true ?>
        @if (!$flag)
        <div class="mt-3 p-2">
            <h1 class="text-center">Welcome to Classroom!</h1>
            <p class="text-center">This platform is designed to help you manage and share your classroom materials.</p> 
            <p class="text-center">You have not created any classes yet.</p>
        </div>
    @else
    <div class="sub-continer d-flex flex-wrap gap-4">
        <x-classCard classname="Hello" section="2-A" teacherName="John Smith">
            <li type="button" class="cursor-pointer show-modal text-dark" data-toggle="modal" data-target="#unenrollClassModal">Unenroll class</li>
        </x-classCard>
        <x-classCard classname="Hello" section="2-A" teacherName="John Smith">
            <li type="button" class="cursor-pointer show-modal text-dark" data-toggle="modal" data-target="#editClassModal">Edit class</li>
            <li type="button" class="cursor-pointer show-modal text-dark" data-toggle="modal" data-target="#archiveClassModal">Archive class</li>
        </x-classCard>
    </div>
    @endif
    
    {{-- Home Modals --}}
    @extends('layouts.partials.homeModal')
    {{-- Home Modals --}}
@endsection 