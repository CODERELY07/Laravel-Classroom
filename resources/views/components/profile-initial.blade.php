<div {{$attributes}}>
    
    @if ($profile)
    <!-- Display the profile picture from public/images -->
    <img style="position:absolute;width:100%;height:100%;border-radius:inherit;object-fit:cover" src="{{ asset('images/' . $profile) }}" alt="Profile Picture"/>
    {{$slot}}
    @else
        <span>M</span>
        {{$slot}}
    @endif
</div>