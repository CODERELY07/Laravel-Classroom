@if(!$questions->isEmpty())
<div class="border-bottom d-flex  mt-3 justify-content-between align-items-center pb-1">
    <h2 class="text-start">Questions</h2> 
</div>
@foreach ($questions as $question)
    <div class="material mt-2 gap-2 pb-2 d-flex align-items-center border-bottom " style="width:100%;">
        <a href="{{route('question.show', [$question->id])}}" class="material mt-2 gap-2 pb-2 d-flex align-items-center " style="width:100%;">
            <div class="profile-text text-small-initial">
                <i class="fa-solid fa-question"></i>
            </div>
            <div class="d-flex justify-content-between align-items-center" style="width:100%;">
                <h6 class="material-title p-0 m-0">{{$question->title}}</h6>
                <small>{{$question->due_date}}</small>
            </div>
        </a>
    </div>
@endforeach
@endif