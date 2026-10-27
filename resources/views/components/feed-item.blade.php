@props(['type', 'item', 'aside' => null])
{{-- One row for a question or a material, used on the stream and classwork tabs. --}}
@php $isQuestion = $type === 'question'; @endphp
<a href="{{ $isQuestion ? route('question.show', $item->id) : route('material.show', $item->id) }}"
   @class(['feed-item', 'is-question' => $isQuestion])>
    <span class="feed-icon" aria-hidden="true">
        <i class="{{ $isQuestion ? 'fa-solid fa-clipboard-question' : 'fa-solid fa-book-open' }}"></i>
    </span>
    <span class="feed-main">
        <span class="visually-hidden">{{ $isQuestion ? 'Question:' : 'Material:' }}</span>
        <p class="feed-title">{{ $item->title }}</p>
        <p class="feed-meta">{{ Str::limit($item->description, 120) }}</p>
    </span>
    <span class="feed-aside">
        @if ($aside)
            {{ $aside }}
        @elseif ($isQuestion)
            Due {{ $item->due_date?->format('M j') }}
        @else
            {{ $item->created_at?->format('M j') }}
        @endif
    </span>
</a>
