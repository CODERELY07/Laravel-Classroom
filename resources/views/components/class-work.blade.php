@props(['classID'])
<div id="classwork" class="tab-content">
    @if(auth()->user()->role == 'teacher')
        <div class="relative" style="width:200px">
            <button class="btn btn-primary" id="create-classwork-btn">Create +</button>
            <div class="toggle-content shadow-1 z-index-99">
                <ul class="list-unstyled cursor-pointer">
                    <li class="toggle-classwork" data-target="#creatematerial">Material</li>
                    <li class="toggle-classwork" data-target="#createquestion">Question</li>
                    <li class="toggle-classwork" data-target="#viewAll">View All</li>
                </ul>
            </div>
        </div>
        <main >
            <div id="viewAll" class="remove-toggle">
                <div class="border-bottom d-flex  mt-3 justify-content-between align-items-center pb-1">
                    <h2 class="text-start">Questions</h2> 
                </div>
                <div>
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
                        <div class="relative cursor-pointer">
                            <div class="cursor-pointer px-1 pe-2 class-ellipsis">
                                <i class="fa-solid fa-ellipsis-vertical"></i>                    
                            </div>
                            <div class="toggle-content z-index-1 class-options shadow-1">
                                <ul class="list-unstyled z-index-1">
                                    hi
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div id="creatematerial" class="d-none remove-toggle">
            </div>
            <div id="createquestion" class="d-none remove-toggle mt-3 mx-2">
                <form id="createquestionForm" class="d-flex flex-start justify-content-between flex-wrap" action="{{route('question.store')}}">
                    <div style="width:100%;max-width:500px">
                        <div class="msg alert" style="display: none"></div>
                            @csrf
                            <input type="hidden" name="class_id" id="class_id" value="{{$classID}}">
                            <div class="mb-2">
                                <label for="title">Title: </label><br>
                                <input name="title" class="form-control" id="title"/>
                            </div>
                            <div class="mb-2">
                                <label for="description">Description: </label><br>
                                <textarea name="description" class="form-control" id="description"></textarea>
                            </div>
                            <div class="mb-2">
                                <label for="points">Points: </label><br>
                                <input type="number" name="points" id="points" class="form-control">
                          
                            </div>
                            <div class="mb-2">
                                <label for="points">Due Date: </label><br>
                                <input type="date" name="duedate" id="duedate" class="form-control">
                            </div>
                    </div>
                    <div style="width:100%;max-width:500px;">
                        <p>Select the number of items:</p>
                        <input type="number" id="question-items" class="form-control" placeholder="5">
                        <label class="mt-2"  for="question" >Questions: </label><br>
                        <div style="overflow-y:auto;height:300px">
                            <div id="questionInputs">

                            </div>
                        </div>
                        <div>
                            <input type="submit" class="btn btn-primary float-end">
                        </div>
                    </div>
                </form>
            </div>
        </main>
        @endif
</div>