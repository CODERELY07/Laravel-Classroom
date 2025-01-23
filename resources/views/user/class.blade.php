@extends('layouts.app')

@section('title', 'BSIT')
@section('content')
    <div class="sub-container">
        <div class="tabs mt-4">
            <button class="tab-button active" data-tab="stream">Stream</button>
            <button class="tab-button" data-tab="classwork">Classwork</button>
            <button class="tab-button" data-tab="people">People</button>
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
                                <p class="text-normal"><span class="semi-bold">Subject: </span>Web Development</p>
                            </div>
                        </div>
                        <div class="mt-5 pt-3">
                            <h1 class="text-start text-medium">Classname</h1>
                            <p class="text-white text-start text-normal">Section</p>
                            <p class="text-white text-small text-start">Your Class code: <span>WY3DS3WDSA</span></p>
                        </div>
                    </div>
                </div>        
                
                <div class="materials-section">
                    <a href="{{route('user.material', ['material'])}}" class="material-item mt-3 p-3 gap-3 d-flex align-items-center">
                        <div class="profile-text text-small-initial">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                        <div>
                            <h6 class="material-title p-0 m-0">Title</h6>
                            <p class="p-0 text-start m-0">
                                This is the content content
                            </p>
                        </div>
                    </a>
                </div>
            </div>
            <div id="people" class="tab-content">
                <div class="border-bottom d-flex justify-content-between align-items-center pb-3">
                    <h2 class="text-start">Teachers</h2>
                    <div class="d-flex align-items-center gap-1">
                        <div class="align-items-center justify-content-center gap-3 d-flex">
                            <p class="m-0">1 Students</p>
                            <p class="m-0 cursor-pointer show-modal" data-toggle="modal" data-target="#addStudentModal"><i class="fa-solid fa-user-plus"></i></p>
                        </div>
                    </div>
                </div>
    
                <ul class="list-unstyled">
                    <li class="d-flex border-bottom pb-2 gap-2 pt-2 align-items-center mb-2">                        
                        {{-- <img src="" alt="Profile" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 10px;"> --}}
                        <div class="profile-text text-small-initial" >
                            M
                        </div>
                        <p class="p-0 m-0">
                            Mark Ely
                        </p>
                    </li>
                </ul>
    
                <!-- Display Students -->
                <div class="border-bottom d-flex justify-content-between align-items-center pb-3">
                    <h3>Students</h3>
                    <p type="button" id="unenroll-selected" ><i class="fa-solid fa-user-minus"></i></p>
                </div>

                <form id="unenroll-form">
                    <ul class="list-unstyled">
                        <li class="d-flex border-bottom pb-2 pt-2 align-items-center mb-2">
                            <label for="" class="d-flex gap-2">
                                {{-- <img src="" alt="Profile" style="width: 30px; height: 30px; border-radius: 50%; margin-right: 5px;"> --}}
                                <div class="profile-text text-small-initial" >
                                    M
                                </div>
                                <p class="text-start mt-1">Name</p>
                            </label>
                            <input type="checkbox" name="unenroll[]" value="1" class="unenroll-checkbox" style="margin-left: auto;">
                        </li>
                    </ul>
                </form>

                {{-- Add stutdent Modal --}}
                <x-modal id="addStudentModal">
                    <x-slot name="heading">
                        Add Users
                    </x-slot>
                    
                    <ul id="addedUsersList" class="list-unstyled mb-3">
                        <li class="my-2 d-flex justify-content-between align-items-center">
                            <p>Sam Smith</p>
                            <p type="button" class="btn-sm remove-user" data-id=""><i class="fa-solid fa-user-minus"></i></p>
                        </li>
                    </ul>
                    <form id="userSearch">
                        <input type="hidden" name="id" id="class_id" value="">
                        <div class="d-flex">
                             <!-- Added users  -->
                            <input type="text" id="userSearchInput" class="form-control" name="email" placeholder="Search by email">
                        </div>
                        <div id="searchResults" class="mt-2">
                            <!-- Live search results -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <p>Brook D. Danger</p>
                                <p type="button" class="btn-sm  add-user" data-id="" data-role="" data-name=""><i class="fa-solid fa-user-plus"></i></p>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary float-end mt-5">Save Changes</button>
                    </form>
                </x-modal>
            </div>
        </div>
    </div>
@endsection