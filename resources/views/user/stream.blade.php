@extends('layouts.app')

@section('title', "$data->classname")
@section('content')
    <div class="sub-container">
        <div class="tabs mt-4">
            <button class="tab-button active" data-tab="stream">Stream</button>
            <button class="tab-button" data-tab="classwork">Classwork</button>
            <button class="tab-button" data-tab="people">People</button>
            <button class="tab-button" data-tab="grade">Grade</button>
        </div>
        <div class="mt-1">
            <div id="stream" class="tab-content active">
                <div class="class-banner-card">
                    <div class="overlay"></div>
                    <div class="relative card-body">
                        <div class="relative" style="top: -40px">
                            <div id="show-subject" class="float-end cursor-pointer">
                                <i class="fa-solid fa-circle-exclamation" style="font-size: 20px"></i>
                            </div>
                            <div class="toggle-content" style="background: none;box-shadow:none">
                                <p class="text-normal"><span class="semi-bold">Subject: </span>{{$data->subject}}</p>
                            </div>
                        </div>
                        <div class="mt-5 pt-3">
                            <h1 class="text-start text-medium">{{$data->classname}}</h1>
                            <p class="text-white text-start text-normal">{{$data->section}}</p>
                            @if(auth()->user()->role == 'teacher')
                            <p class="text-white text-small text-start">Your Class code: <span>{{$data->class_code}}</span></p>
                            @endif
                        </div>
                    </div>
                </div>        
                <div class="materials-section">
                    {{-- <a href="{{route('user.material', ['material'])}}" class="material-item mt-3 p-3 gap-3 d-flex align-items-center">
                        <div class="profile-text text-small-initial">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                        <div>
                            <h6 class="material-title p-0 m-0">Title</h6>
                            <p class="p-0 text-start m-0">
                                This is the content content
                            </p>
                        </div>
                    </a> --}}
                </div>
            </div>
            {{-- Classwork --}}
            <x-class-work :classID="$data->id" classID="{{$data->id}}"/>
    
            {{-- Classwork --}}
            <div id="people" class="tab-content">
                <div class="border-bottom d-flex justify-content-between align-items-center pb-3">
                    <h2 class="text-start">Teachers</h2>
                    <div class="d-flex align-items-center gap-1">
                        <div class="align-items-center justify-content-center gap-3 d-flex">
                            <p class="m-0">1 Students</p>
                            @if(auth()->user()->role == 'teacher')
                            <p class="m-0 cursor-pointer show-modal" data-toggle="modal" data-target="#addStudentModal"><i class="fa-solid fa-user-plus"></i></p>
                            @endif
                        </div>
                    </div>
                </div>
                <ul class="list-unstyled">
                    @foreach ($teachers as $teacher)
                        <li class="d-flex border-bottom pb-2 gap-2 pt-2 align-items-center mb-2">
                            <div class="profile-text shadow-3 text-small-initial relative cursor-pointer">
                                @if ($teacher->profile)
                                    <img style="position:absolute;width:100%;height:100%;border-radius:inherit;object-fit:cover" src="{{ asset('images/' . $teacher->profile) }}" alt="Profile Picture"/>
                                @else
                                    {{ strtoupper(substr($teacher->name, 0, 1)) }}  
                                @endif
                            </div>
                            <p class="p-0 m-0">{{ $teacher->name }}</p>
                        </li>
                    @endforeach
                </ul>
                <!-- Display Students -->
                <div class="border-bottom d-flex justify-content-between align-items-center pb-3">
                    <h2 class="text-start">Students</h2>
                    @if ($teachers->contains('id', auth()->user()->id))
                        <p type="button" class="d-none" id="unenroll-selected">
                            <i class="fa-solid fa-user-minus"></i> 
                        </p>
                    @endif
                </div>
                
                <form id="unenroll-form" action="{{ route('unenroll', ['class_id' => $data->id]) }}">
                    @csrf
                    @foreach ($students as $student)
                        <li class="d-flex border-bottom pb-2 gap-2 pt-2 align-items-center mb-2">
                            <label class="d-flex gap-2 align-items-center">
                                <div class="profile-text shadow-3 text-small-initial relative cursor-pointer">
                                    @if ($student->profile)
                                        <img style="position:absolute;width:100%;height:100%;border-radius:inherit;object-fit:cover" 
                                             src="{{ asset('images/' . $student->profile) }}" 
                                             alt="Profile Picture"/>
                                    @else
                                        {{ strtoupper(substr($student->name, 0, 1)) }}  
                                    @endif
                                </div>
                                <p class="p-0 m-0">{{ $student->name }}</p>
                            </label>
                            @if ($teachers->contains('id', auth()->user()->id))
                                <input type="checkbox" name="unenroll[]" value="{{ $student->id }}" class="unenroll-checkbox cursor-pointer" style="margin-left: auto;">
                            @endif
                        </li>
                    @endforeach
                </form>
                {{-- Add stutdent Modal --}}
                <x-modal id="addStudentModal">
                    <x-slot name="heading">
                        Add Users
                    </x-slot>
                    <x-search-form action="/stream/search/{{$data->id}}" id="{{$data->id}}"/>
                </x-modal>
            </div>
            <div id="grade" class="tab-content">
                <ul class="list-unstyled">
                    @if(auth()->user()->role == 'teacher')
                        @foreach ($students as $student)
                        <a href="{{ route('grade.show', ['classID' => $data->id, 'userID' => $student->id]) }}">
                            <li class="d-flex border-bottom pb-2 gap-2 pt-2 align-items-center mb-2">
                                <div class="profile-text shadow-3 text-small-initial relative cursor-pointer">
                                    @if ($student->profile)
                                        <img style="position:absolute;width:100%;height:100%;border-radius:inherit;object-fit:cover" src="{{ asset('images/' . $student->profile) }}" alt="Profile Picture"/>
                                    @else
                                        {{ strtoupper(substr($student->name, 0, 1)) }}  
                                    @endif
                                </div>
                                <p class="p-0 m-0">{{ $student->name }}</p>
                            </li>
                        </a>
                        @endforeach
                    @else
                        <x-show-grade :userID="$student->id" :classID="$data->id" />
                    @endif
                </ul>
            </div>
        </div>
    </div>
@endsection