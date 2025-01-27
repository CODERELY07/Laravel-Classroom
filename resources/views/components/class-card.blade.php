<div class="class-card  relative shadow-3">
    <div class="class-banner d-flex p-3 border justify-content-between">
        <div>
            <a href="{{route('user.class', ['BSIT'])}}">
                <span class="cursor-pointer text-medium d-block">{{$classname}}</span>
                <span class="cursor-pointer text-small d-block">{{$section}}</span>
            </a>
            <div class="mt-1">
                <p>{{$teacherName}}</p>
            </div>
        </div>
        <div class="class-user-profile">
            <x-profile-initial id="profile" class="profile-text shadow-3 text-big-initial relative cursor-pointer"/>
        </div>
        <div class="relative cursor-pointer">
            <div class="cursor-pointer px-1 pe-2 class-ellipsis">
                <i class="fa-solid fa-ellipsis-vertical"></i>                    
            </div>
            <div class="toggle-content z-index-1 class-options shadow-1">
                <ul class="list-unstyled z-index-1">
                    {{$slot}}
                </ul>
            </div>
        </div>
    </div>
    <div class="class-footer">
        <span><i class="fa-solid fa-clipboard-user text-dark cursor-pointer"></i></span> 
    </div>
</div>