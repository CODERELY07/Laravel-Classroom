<div class="class-card  relative shadow-3">
    <div class="class-banner d-flex p-3 border justify-content-between">
        <div>
            <a href="{{route('stream', [$classID])}}">
                <span class="cursor-pointer text-medium d-block">{{$classname}}</span>
                <span class="cursor-pointer text-small d-block">{{$section}}</span>
            </a>
            @if (auth()->user()->id != $userID)
                <div class="mt-1">
                    <p>{{$teacherName}}</p>
                </div>
            @endif
        </div>
        @if (auth()->user()->id != $userID)
            <div class="class-user-profile">
                <div id="profile" class="profile-text shadow-3 text-big-initial relative cursor-pointer">
                    @if ($teacherProfile )
                        <img style="position:absolute;width:100%;height:100%;border-radius:inherit;object-fit:cover" src="{{ asset('images/' . $teacherProfile) }}" alt="Profile Picture"/>
                    @else
                        <span>{{$firstLetter}}</span>
                    @endif
                    
                </div>
            </div>
        @endif
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