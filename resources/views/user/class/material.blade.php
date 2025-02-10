@extends('layouts.app')

@section('title', 'Material')

@section('content')
<div class="container-2 mt-4 p-3">
    <div>
        <h3 class="title">
            {{$materials->title}}
        </h3>
        <p class="author-date">
            {{$materials->user->name}} • <span class="fw-bold">{{$materials->created_at}}</span>
        </p>
    </div>
    <div class="line mt-2"></div>
    <div class="mt-3">
        <h6>
            {{$materials->description}}
        </h6>
    </div>
    @if($materials->file)
        <div class="mt-4">
            <h5>File:</h5>
            <a href="{{ Storage::url($materials->file) }}" target="_blank" class="btn text-white btn-primary">
                View Or Download File
            </a>
        </div>
    @endif
</div>
@endsection
