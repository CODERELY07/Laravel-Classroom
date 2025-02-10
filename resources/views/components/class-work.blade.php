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
        <main>
            <div id="viewAll" class="remove-toggle">
                @if(!$questions->isEmpty() || !$materials->isEmpty())
                <x-questions :classID="$classID" />
                <x-materials :classID="$classID" />
                
                @else
                    <p class="text-center mt-5">This class is empty</p>
                @endif
            </div>
            <div id="creatematerial" class="d-none remove-toggle">
                <form id="createMaterialForm" class="d-flex flex-start justify-content-between flex-wrap" action="{{ route('material.store') }}" enctype="multipart/form-data">
                    <div style="width:100%; max-width:500px">
                        <div class="msg alert" style="display: none"></div>
                        @csrf 
                        <input type="hidden" name="class_id" id="class_id" value="{{ $classID }}">
                
                        <div class="mb-2">
                            <label for="title">Title: </label><br>
                            <input type="text" name="title" class="form-control" id="title" required />
                        </div>
    
                        <div class="mb-2">
                            <label for="description">Description: </label><br>
                            <textarea name="description" class="form-control" id="description" required></textarea>
                        </div>
        
                        <div class="mb-2">
                            <label for="file">Upload File: </label><br>
                            <input type="file" name="file" class="form-control" id="file" required />
                        </div>
                        <div class="mb-2">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
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
                        <input type="number" id="question-items" class="form-control" placeholder="1" value="1">
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
    @else
    @if(!$questions->isEmpty() || !$materials->isEmpty())
    <x-questions :classID="$classID" />
    @else
        <p class="text-center mt-5">This class is empty</p>
    @endif
    @endif
</div>