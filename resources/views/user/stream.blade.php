@vite('resources/js/app.js')
@extends('layouts.app')

@section('title', "$data->classname")
@section('content')
    <div class="sub-container">
        <div class="tabs mt-4 d-flex flex-wrap gap-3">
            <button class="tab-button active" data-tab="stream">Stream</button>
            <button class="tab-button" data-tab="classwork">Classwork</button>
            <button class="tab-button" data-tab="people">People</button>
            <button class="tab-button" data-tab="grade">Grade</button>
            <button class="tab-button" data-tab="chats">Chats</button>
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
                    @php
                    foreach ($classworks as $classwork) {
                        $questions = $classwork->questions;
                        $materials = $classwork->materials;
                    
                        if ($questions && $materials) {
                            $combined = array_merge($questions->toArray(), $materials->toArray());
                            //sort array by created_at
                            usort($combined, function ($a, $b) {
                                return strtotime($a['created_at']) - strtotime($b['created_at']);
                            });
                            
                            foreach ($combined as $item) {
                                $icon = '';
                                if (isset($item['question'])) { 
                                    $icon = '<i class="fa-solid fa-question"></i>';
                                    $url = route('question.show', [$item['id']]);
                                } else {
                                    $icon = '<i class="fa-solid fa-book"></i>';
                                    $url = route('material.show', [$item['id']]);
                                }
                                
                                echo '<a href= ' . $url . ' class="material-item mt-3 p-3 gap-3 d-flex align-items-center"> 
                                        <div class="profile-text text-small-initial">
                                            ' . $icon . '
                                        </div>
                                        <div>
                                            <h6 class="material-title p-0 m-0">' . $item['title'] . '</h6>
                                            <p class="p-0 text-start m-0">
                                                ' . $item['description'] . '
                                            </p> 
                                        </div>
                                      </a>';
                            }
                        } else {
                            echo "No questions or materials found.";
                        }
                    }
                @endphp

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
            <div id="chats" class="card tab-content mx-auto" style="height:520px;width:100%;max-width:800px;position: relative;">
                <div>
                    <div>
                        <h2><b>{{$data->classname}}</b></h2>
                    </div>
                    <div class="card-body" id="messages" style="height:400px;overflow: auto">
                       {{-- fetch chats --}}
                      
                    </div>
                    
                </div>
                <div class="d-flex gap-2 align-items-center mx-auto justify-content-center" style="position: relative;bottom:0px;width:85%">
                    <input type="text" style="width: 90%" class="form-control" placeholder="Type your message here..." id="messageInput">
                    <div style="width: 10%" >
                        <button  class="btn btn-primary" type="button" onclick="sendMsg()"><i class="fa-solid fa-paper-plane"></i></button>
                    </div>
                </div>
            </div>
            @section('script')

            <script>
            
                function sendMsg(){
                   const sender = "{{auth()->user()->name}}";
                   const userID = "{{auth()->user()->id}}";
                   const classID = "{{$data->id}}";
                   const csrfToken = "{{csrf_token()}}";
                   const message = messageInput.value;
              
                   if(message){
                    $.ajax({
                       url: "{{route('send.message')}}",
                       type: 'POST',
                       data: {
                           sender: sender,
                           message: message,
                           user_id: userID,
                           class_id: classID,
                           _token: csrfToken
                       },
                       success: function(response){
                        const senderName = response.sender;
                        const alignment = senderName === "{{auth()->user()->name}}" ? "right" : "left";
                     
                           $('#messages').append(` 
                           <div class="message ${alignment}">
                               <p>${response.message}</p>
                           </div>`)
                           messageInput.value = '';
                       },
                       error: function(response){
                            console.log(response.errors);
                       }
                    })
                   }
               }
       
                    window.onload = () => {
                //    console.log("Test");

                   const classID = "{{$data->id}}"; 
                   const csrfToken = "{{csrf_token()}}";

                   $.ajax({
                    url: `{{route('chats', ['classID' => $data->id])}}`, 
                    type: 'GET',
                    beforeSend: function(){
                        $('#messages').html(`<p id="loadingFlag" style="text-align:center;margin-top:250px;color:#727D73">loading...</p>`)                       
                    },
                    success: function(response) {
                        const chats = response.chats;
                        $('#messages p#loadingFlag').text('');
                        if (response.chats.length === 0 || response.chats.every(chat => !chat.message)) {
                            $('#messages').html(`<p id="chatFlag" style="text-align:center;">There are no chats at the moment. You have the opportunity to start one.</p>`);
                        } else {
                            $('#messages p#chatFlag').text('');
                        }

                        chats.forEach(function(data) {
                            const alignment = data.user.name === "{{auth()->user()->name}}" ? "right" : "left";
                            const name = data.user.name === "{{auth()->user()->name}}" ? "" : data.user.name;
                                
                            let profileContent = '';
                            if (data.user.name !== "{{auth()->user()->name}}") {
                                if (data.user.profile && data.user.profile.trim() !== '') {
                                    profileContent = `<img style="width:30px;height:30px;border-radius:50%;object-fit:cover" src="{{ asset('images/') }}/${data.user.profile}" alt="Profile Picture" />`;
                                } else {
                                    profileContent = `<span style="width:30px;height:30px;display:flex;justify-content:center;align-items:center;border-radius:50%;background-color:#ccc; color:white; font-size:18px;">${data.user.name.charAt(0).toUpperCase()}</span>`;
                                }
                            }
                            $('#messages').append(`
                                <div class="message-container ${alignment}" style="position:relative; display:flex;align-items:flex-end; gap:5px;">
                                    <div class="align-self:flex-end">
                                        ${profileContent}
                                    </div>
                                    
                                    <div style="flex-grow:1;">
                                        <small style="font-size:10px">${name}</small>
                                        <div class="message ${alignment}">
                                            <p>${data.message}</p>
                                        </div>
                                    </div>
                                </div>
                            `);


                            });
                        },
                        error: function(response) {
                            console.log(response.errors);
                        }
                    });
                  
                   window.Echo.channel('user-message')
                       .listen('MessageSent', function(data) { 
                        let profileContent = '';
                        $('#messages p#chatFlag').text('');
                      
                          if(data.sender != "{{auth()->user()->name}}"){
                            if (data.profile['profile'] && data.profile['profile'].trim() !== '') {
                                profileContent = `<img style="width:30px;height:30px;border-radius:50%;object-fit:cover" src="{{ asset('images/') }}/${data.profile['profile']}" alt="Profile Picture" />`;
                            } else {
                                profileContent = `<span style="width:30px;height:30px;display:flex;justify-content:center;align-items:center;border-radius:50%;background-color:#ccc; color:white; font-size:18px;">${data.profile['name'].charAt(0).toUpperCase()}</span>`;
                            }
                            const alignment = data.sender !== "{{auth()->user()->name}}" ? "left" : "right";
                         
                            $("#messages").append(`
                                 <div class="message-container ${alignment}" style="position:relative; display:flex;align-items:flex-end; gap:5px;">
                                    <div class="align-self:flex-end">
                                        ${profileContent}
                                    </div>
                                    
                                    <div style="flex-grow:1;">
                                        <small style="font-size:10px">${data.sender}</small>
                                        <div class="message ${alignment}">
                                            <p>${data.message}</p>
                                        </div>
                                    </div>
                                </div>
                            `);
                          }
                       });
       
                //    console.log("Test channel...");
                };
       
           </script>

           @endsection
        </div>
    </div>
@endsection
<style>
    
    .message {
        padding: 8px;
        border-radius: 5px;
        width: max-content;
        max-width: 400px;
    }

    .right {
        align-self: flex-end;
        margin-left: auto;
    }
    .message.right{
        background-color: #007bff;
        color: white;
    }
    .left {
        align-self: flex-start;
    }
    .message.left{
        background-color: #f1f1f1;
        color: black;
    }

    .card-body {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
</style>