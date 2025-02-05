@extends('layouts.app')

@section('title', 'Question')

@section('content')
<div class="container-2 mt-4 p-3">
    <div>
        <h3 class="title">
            {{$questionsData->title}}
        </h3>
        <p class="author-date">
            {{$questionsData->user->name}} • <span class="fw-bold">Dec 12, 2025</span>
        </p>
        <div class="d-flex justify-content-between">
            {{-- <p class="grade">47/50</p> --}}
            <p class="text-muted text-start">Due Date: <span class="fw-bold"> {{$questionsData->due_date}}</span></p>
        </div>
    </div>
    <div class="line mt-2"></div>
    <div class="mt-3">
        <h6>
            {{$questionsData->description}}
        </h6>
        <div>
            <h6>
                @if($questions && count($questions) > 0)
                <ol style="line-height: 30px"> 
                    @foreach($questions as $index => $question)
                        <li>{{ $question }}</li>
                    @endforeach
                </ol>
                @endif
            </h6>
        </div>
        <div class="shadow-3 p-4 rounded">
            <p>Your Answer:</p>
            <form action="{{route('turnIn')}}" method="POST">
                @csrf
                @method('POST')
                <input type="hidden" name="question_id" value="{{$questionsData->id}}">
                <input type="hidden" name="class_member_id" value="{{$questionsData->user->id}}">
                <textarea class="p-3 mt-3 text-dark" name="answer" style="border:none;background-color:#f1f1f1;outline:none;width:100%;height:150px;text-align:start;vertical-align:top;resize:none;font-size:14px"></textarea>
                <input type="submit" class="btn btn-primary mt-3 w-100" value="Turn In">
            </form>
        </div>
        {{-- <div class="shadow-3 mt-3 p-4 rounded">
            <div class="d-flex justify-content-between align-items-center p-2">
                <input type="text" style="border:none;outline:none;width:100%;" placeholder="Class Comment">
                <i class="fa-solid fa-paper-plane cursor-pointer"></i>
            </div>
           
            <div class="line"></div>
            <p class="text-small mt-2">Comment in the class</p>
            <div class="relative cursor-pointer m-2">
                <i class="fa-regular fa-comment"></i>
                <span>1</span>
            </div>
        </div> --}}
    </div>
</div>
@endsection
