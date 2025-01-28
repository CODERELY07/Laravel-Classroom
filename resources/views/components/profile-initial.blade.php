<div {{$attributes}}>
  
    @if ($profile || $profile != NULL)
        <!-- Display the profile picture from public/images -->
        <img style="position:absolute;width:100%;height:100%;border-radius:inherit;object-fit:cover" src="{{ asset('images/' . $profile) }}" alt="Profile Picture"/>
        {{$slot}}
    @else
       
        <span>{{$nameInitial()}}</span>
        {{$slot}}
    @endif
</div>