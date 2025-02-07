<div>
    @foreach($questions as $question)
        <div class="mt-2 gap-2 pb-2 d-flex align-items-center border-bottom ">
            <div class="profile-text text-small-initial">
                <i class="fa-solid fa-question"></i>
            </div>
            <div>
                <strong>Title:</strong> {{ $question->title }}<br>
                <strong>Grade:</strong> 
                {{$question->answers->isNotEmpty() && ($question->answers->first()->grade !== null && $question->answers->first()->grade !== 0) ? $question->answers->first()->grade : 'No grade available'}}

            </div>
        </div>
    @endforeach
</div>
