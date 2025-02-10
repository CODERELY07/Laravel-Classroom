@extends('layouts.app')

@section('title', 'Grade')

@section('content')
    <div class="mt-3 mx-4">
        <h4 class="text-start">Questions</h4>
        <ul class="list-unstyled">
            @foreach ($questions as $question)
                @php
                    $questionArray = json_decode($question->question, true);
                    $answer = $question->answers->first(); 
                @endphp
                <li class="d-flex border-bottom pb-2 gap-5 pt-2 align-items-center mb-2 flex-column flex-md-row">
                    <div class="w-100">
                        <p class="p-0 m-0">Title: {{ $question->title }}</p>
                        <ul>
                            @foreach ($questionArray as $index => $questionText)
                                <li>{{ $index + 1 }}. {{ $questionText }}</li>
                            @endforeach
                        </ul>
                        <br>
                        @if($answer)
                            <label for="answer">Answer:</label>
                            <textarea class="p-3 mt-3 text-dark w-100" name="answer" style="border:none;background-color:#f1f1f1;outline:none;height:150px;text-align:start;vertical-align:top;resize:none;font-size:14px">
                                {{ $answer->answer }}
                            </textarea>
                            <p class="d-flex gap-2 align-items-center mt-2 mt-md-0">
                                <label>Grade: <span id="grade" data-id="{{$answer->id}}">{{ empty($answer->grade) ? 0 : $answer->grade }}</span>/{{ $question->points }}</label>
                                <input type="number" 
                                    data-url = "{{ route('grade.update') }}"
                                    class="form-control text-start grade-input" 
                                    data-answer-id="{{ $answer->id }}" 
                                    value="{{ $answer->grade ?? '' }}" 
                                    placeholder="Enter Grade" 
                                    style="max-width: 200px;" 
                                    min="0" max="{{ $question->points }}"/>
                            </p>
                        @else
                            <p>User has not submitted yet.</p>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endsection

@section('script')
<script>
    $(document).ready(function () {
        $('.grade-input').on('input', function () {
            let answerId = $(this).data('answer-id');
            let grade = $(this).val();
            const url = $(this).data('url');

            if (answerId) {
                $.ajax({
                    url: url,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        answer_id: answerId,
                        grade: grade
                    },
                    success: function (response) {
                        let gradeSpan = $('span#grade[data-id="' + answerId + '"]');
                        gradeSpan.text(response.updatedGrade);  

                        $(this).addClass('is-valid').removeClass('is-invalid'); 
                    },
                    error: function (xhr) {
                        $(this).removeClass('is-valid').addClass('is-invalid');
                    }
                });
            }
        });
    });
</script>
@endsection
 