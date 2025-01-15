@extends('layouts.clean', [
    'cssBody' => 'home-body'
])
@section('title', 'Home')

@section('childContent')
    <div class="container">
        <div class="logo">
        <h1>
            ME Classroom
        </h1>
        </div>
        <h2 class="main-heading">
        Enhance Learning Experiences with Melc Classroom
        </h2>
        <p class="sub-heading">
            Join ME Classroom to build meaningful and tailored learning experiences. Equip your students for success with tools crafted to enhance teaching efficiency and create engaging learning environments.
        </p>
        <div class="btn-container">
        <a href="{{ route('signup') }}">
        <button class="btn btn-primary">
            Sign Up
        </button>
        </a>
        <a  href="{{ route('signin') }}">
        <button class="btn btn-outline-primary">
            Sign in
        </button>
        </a>
        </div>
        </div>
    </div>
@endsection
