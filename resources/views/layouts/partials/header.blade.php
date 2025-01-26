<header class="container-fluid p-3 shadow-1">
    <div class="row">
       <div class="col-12 d-flex justify-content-between align-items-center">
        <div class="d-flex gap-3 align-items-center">
            <div class="cursor-pointer" id="bar">
                <i class="fa-solid fa-bars"></i>
            </div>
            {{-- Navigation --}}
            <nav class="nav pt-2 shadow-1 d-flex flex-column flex-nowrap">
                <ul class="list-unstyled">
                    <a href="{{route('user.index')}}">
                        <li class="d-flex align-items-center">
                            <i class="fa-solid fa-house"></i>
                            <span>Home</span>
                        </li>
                    </a>
                </ul>
                <ul class="list-unstyled pt-0 dropdown-class-parent">
                    <span class="px-3 pb-1 d-block cursor-pointer" id="dropdown-class">
                        <i class="fa-solid fa-caret-right"></i> 
                        <i class="fa-solid fa-graduation-cap"></i>
                        <span>Enrolled</span>
                    </span>
                    <li class="d-flex mt-2 align-items-center">
                        <div class="profile-text text-small-initial">
                            B
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <span class="m-0 p-0 semi-bold">BSIT</span>
                            <span class="m-0 p-0 text-start text-small">2-A</span>
                        </div>
                    </li>
                    <li class="d-flex align-items-center">
                        <div class="profile-text text-small-initial">
                            C
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <span class="m-0 p-0 semi-bold">CS</span>
                            <span class="m-0 p-0 text-start text-small">2-A</span>
                        </div>
                    </li>
                    <li class="d-flex align-items-center">
                        <div class="profile-text text-small-initial">
                            D
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <span class="m-0 p-0 semi-bold">Data Structure</span>
                            <span class="m-0 p-0 text-start text-small">2-A</span>
                        </div>
                    </li>
                    <li class="d-flex align-items-center">
                        <div class="profile-text text-small-initial">
                            I
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <span class="m-0 p-0 semi-bold">Information Management</span>
                            <span class="m-0 p-0 text-start text-small">2-A</span>
                        </div>
                    </li>
                    <li class="d-flex align-items-center">
                        <div class="profile-text text-small-initial">
                            O
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <span class="m-0 p-0 semi-bold">Object Oriented Programming</span>
                            <span class="m-0 p-0 text-start text-small">2-A</span>
                        </div>
                    </li>
                </ul>
                
                <ul class="list-unstyled">
                    <ul class="list-unstyled">
                        <a href="{{route('user.archive')}}">
                            <li class="d-flex align-items-center">
                                <i class="fa-solid fa-box-archive"></i>
                                <span>Archive</span>
                            </li>
                        </a>   
                        <li class="d-flex align-items-center">
                            <i class="fa-solid fa-gear"></i>                            
                            <span>Settings</span>
                        </li>
                    </ul>
                </ul>
            </nav>
             {{-- Navigation --}}
            <span class="pl-3">
                @yield('title')
            </span>
        </div>
        <div class="d-flex align-items-center gap-3">
            {{-- join and create class --}}
            @if(View::getSections()['title'] === 'Classroom')
            <div class="relative">
                <div class="d-flex gap-2">
                    <div class="relative">
                        <div class="cursor-pointer add-btn" id="add-class-btn">
                            <i class="fa-solid fa-plus"></i>
                        </div>
                        <div class="toggle-content shadow-1 z-index-99">
                            <ul class="list-unstyled">
                                @if (auth()->user()->role == 'teacher')
                                    <li type="button" class="cursor-pointer show-modal" data-toggle="modal" data-target="#createClassModal">Create class</li>
                                    <li type="button" class="cursor-pointer show-modal" data-toggle="modal" data-target="#joinClassModal">Join class</li>
                                @else
                                    <li type="button" class="cursor-pointer show-modal" data-toggle="modal" data-target="#joinClassModal">Join class</li>
                                @endif
                            </ul>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="cursor-pointer add-btn relative" id="bell-btn">
                            <i class="fa-solid fa-bell" style="font-size: 24px; color: #333;"></i>
                            <span class="text-white" style="position: absolute; top: 8px; right: 5px; background-color: #ff0000;font-size:10px;border-radius: 50%; width:15px;height:15px; font-weight: bold; display: flex; justify-content: center; align-items: center;">2</span>
                        </div>
                        <div class="toggle-content shadow-1 z-index-99">
                            <ul class="list-unstyled">
                                <a href="#" class="hover-bg">
                                    <li type="button" class="cursor-pointer">
                                        <div class="d-flex justify-content-between">
                                            <p class="text-start">Activity 1</p>
                                            <p class="text-start text-small">Dec 12, 2024</p>
                                        </div>
                                        <p class="text-start">You have new activity from Ms.Riben in Class BSIT</p>
                                    </li>
                                    <div class="line"></div>
                                </a>
                                <a href="#" class="hover-bg">
                                    <li type="button" class="cursor-pointer">
                                        <div class="d-flex justify-content-between">
                                            <p class="text-start">Activity 1</p>
                                            <p class="text-start text-small">Dec 12, 2024</p>
                                        </div>
                                        <p class="text-start">You have new activity from Ms.Riben in Class BSIT</p>
                                    </li>
                                    <div class="line"></div>
                                </a>
                            </ul>
                        </div>
                    </div>
                   
            
                </div>
                
            </div>
            @endif
            {{-- Profile --}}
            <div class="relative profile-parent">
                <x-profileInitial id="profile-class-btn" class="profile-text cursor-pointer"/>
                <div class="toggle-content profile-content align-items-center shadow-1 py-3 px-5">
                    <div class="d-flex align-items-center flex-column">
                        <p class="text-center text-dark mb-4">calipjo.markely@gmail.com</p>
                        <x-profileInitial id="profile-class-btn" class="profile-text shadow-3 text-big-initial relative cursor-pointer">
                            <div class="pen shadow-3 cursor-pointer show-modal" data-toggle="modal" data-target="#profileModal">
                                <i class="fa-solid fa-pen"></i>
                            </div>
                        </x-profileInitial>
                        <h5 class="mt-3">Hi, Mark Ely!</h5>
                        <div class="d-flex mt-3 gap-3">
                            <a href="{{route('account')}}">
                                <button class="btn pb-2 border text-center form-control">Account</button>
                            </a>
                            <a href="{{route('logout')}}">
                                <button class="btn pb-2 border text-center form-control">Logout</button>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
             {{-- Profile --}}
        </div>
       </div>
    </div>
</header>
