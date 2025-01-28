<ul class="list-unstyled pt-0 dropdown-class-parent">
    <span class="px-3 pb-1 d-block cursor-pointer" id="dropdown-class">
        <i class="fa-solid fa-caret-right"></i> 
        <i class="fa-solid fa-graduation-cap"></i>
        <span>Enrolled</span>
    </span>
    @foreach ($classess as $class)
    <a href="{{route('stream', [$class->classID])}}">
        <li class="d-flex mt-2 align-items-center">
            <div class="profile-text text-small-initial">
                {{$classInitial}}
            </div>
            <div class="d-flex flex-column justify-content-center">
                <span class="m-0 p-0 semi-bold">{{$class->classname}}</span>
                <span class="m-0 p-0 text-start text-small">{{$class->section}}</span>
            </div>
        </li>
    </a>
    
    @endforeach
   
   
</ul>