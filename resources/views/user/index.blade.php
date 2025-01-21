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
    <div class="sub-continer">
        <div class="class-card shadow-3">
            <div class="class-banner d-flex p-3 justify-content-between">
                <div>
                    <div>
                        <p class="cursor-pointer"><strong>BSIT</strong></p>
                        <p class="cursor-pointer">2-A</p>
                    </div>
                    <div>
                        <p>Jonel Smals</p>
                    </div>
                </div>
                <div class="cursor-pointer">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </div>
                
               
            </div>
        </div>
    </div>
    @endif
  
    {{-- Create Class Modal --}}
    <x-modal  id="createClassModal">
        <x-slot name="heading">
           Create Class
        </x-slot>
        <div class="mt-3">
            <label for="classname">Class Name:</label>
            <input type="text" name="classname" id="classname" class="form-control mt-2">
        </div>
        <div class="mt-3">
            <label for="section">Section:</label>
            <input type="text" name="section" id="section" class="form-control mt-2">
        </div>
        <div class="mt-3">
            <label for="subject">Subject:</label>
            <input type="text" name="subject" id="subject" class="form-control mt-2">
        </div>
        <div class="mt-3 float-end">
            <input type="submit" value="Create" class="btn btn-primary">
        </div>
    </x-modal>
    {{-- Create Class Modal --}}

    {{-- Join Class Modal --}}
    <x-modal id="joinClassModal">
        <x-slot name="heading">
           Join Class
        </x-slot>
        <p>
            <strong>Class code</strong><br>
            Ask your teacher for the class code, then enter it here.</p>
        <div class="mt-3">
            <label for="classname">Enter Class Code:</label>
            <input type="text" name="classname" id="classname" class="form-control mt-2">
        </div>
        <div class="mt-3 float-end">
            <input type="submit" value="Join" class="btn btn-primary">
        </div>
    </x-modal>
    {{-- Join Class Modal --}}
    
    {{-- Profile Class Modal --}}
    <x-modal id="profileModal">
        <x-slot name="heading">
           Profile Picture
        </x-slot>
        <div>
            <span>Update your profile picture to help people recognize you.</span>
        </div>
        <div class="mt-4">
            <x-profileInitial id="profile-class-btn" class="profile-text text-xxbig-initial cursor-pointer mx-auto"/>
        </div>
        <div class="mt-4 d-flex gap-5 justify-content-center mx-auto">
            <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-image"></i>
            <span>Add profile Picture</span>
        </button>
        </div>
    </x-modal>
    {{-- Join Class Modal --}}
@endsection 