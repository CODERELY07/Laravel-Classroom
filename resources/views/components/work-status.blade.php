@props(['question', 'answer' => null])
{{-- Status badge for one student's work on one question. --}}
@if (! $answer)
    <span @class(['pill', 'pill-danger' => $question->isPastDue()])>{{ $question->isPastDue() ? 'Missing' : 'Not turned in' }}</span>
@else
    <span class="d-inline-flex gap-1 flex-wrap">
        @if ($answer->isGraded())
            <span class="pill pill-accent">Graded</span>
        @else
            <span class="pill pill-clay">Turned in</span>
        @endif
        @if ($answer->isLate($question))
            <span class="pill pill-danger">Late</span>
        @endif
    </span>
@endif
