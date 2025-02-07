<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | {{ config('app.name') }}</title>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <script>
        var teacherClassesUrl = '{{ route("classess.show") }}';
        var achiveClassesUrl = '{{ route("archive.show") }}';
    </script>
</head>
<body class="@isset($cssBody){{$cssBody}}@endisset">
    @yield('childContent')

     {{-- Profile Class Modal --}}
     <x-modal id="profileModal">
        <x-slot name="heading">
           Profile Picture
        </x-slot>
        <div>
            <span>Update your profile picture to help people recognize you.</span>
        </div>
        <div class="mt-4">
            <x-profile-initial id="profile-change" class="profile-text text-xxbig-initial cursor-pointer relative mx-auto"/>
        </div>
        <form id="changeProfile" action="{{route('user.changeProfile')}}" enctype="multipart/form-data">
            @csrf
            <input type="file" id="profile_picture" name="profile_picture" class="d-none">
            <label for="profile_picture" class="btn mt-4 btn btn-primary w-100 d-block" id="profileButtons">
                <div class="d-flex gap-2 align-items-center justify-content-center w-100" >
                    <i class="fa-solid fa-image"></i>
                    <span>Add profile Picture</span>
                </div>
            </label>
        </form>
        </div>
    </x-modal>
    {{-- Profile Class Modal --}}
    <script type="module" src="/js/main.js"></script>
  
    {{-- <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @yield('script')
</body>
</html>