@extends('layouts.app')
@section('title', $material->title)
@section('crumb', $material->classroom->classname)

@php
    $icon = match ($material->fileExtension()) {
        'pdf' => 'fa-regular fa-file-pdf',
        'doc', 'docx' => 'fa-regular fa-file-word',
        'ppt', 'pptx' => 'fa-regular fa-file-powerpoint',
        'xls', 'xlsx' => 'fa-regular fa-file-excel',
        'jpg', 'jpeg', 'png', 'gif', 'webp' => 'fa-regular fa-file-image',
        default => 'fa-regular fa-file-lines',
    };
@endphp

@section('content')
<div class="page-narrow">
    <a href="{{ route('stream', $material->class_id) }}#classwork" class="btn btn-ghost btn-sm mb-3">
        <i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Back to {{ $material->classroom->classname }}
    </a>

    <article class="panel">
        <div class="panel-body">
            @can('teach', $material->classroom)
                @unless ($material->classroom->isArchived())
                    <div class="d-flex justify-content-end mb-2">
                        <button type="button" class="btn btn-sm btn-quiet text-danger"
                                data-confirm-url="{{ route('material.destroy', $material->id) }}"
                                data-confirm-title="Delete “{{ $material->title }}”?"
                                data-confirm-body="The material and its attachment will be permanently deleted."
                                data-confirm-label="Delete material" data-confirm-danger>
                            <i class="fa-regular fa-trash-can" aria-hidden="true"></i> Delete
                        </button>
                    </div>
                @endunless
            @endcan
            <header class="article-header">
                <span class="feed-icon" aria-hidden="true"><i class="fa-solid fa-book-open"></i></span>
                <div class="min-w-0">
                    <h1 class="article-title">{{ $material->title }}</h1>
                    <div class="article-meta">
                        <span>{{ $material->user->name }}</span>
                        <span>{{ $material->created_at->format('M j, Y') }}</span>
                    </div>
                </div>
            </header>

            <hr class="my-4">

            <p class="prose">{{ $material->description }}</p>

            @if ($hasFile)
                <h2 class="h6 mt-4 mb-2">Attachment</h2>
                @if ($material->isImage())
                    <img src="{{ route('material.file', $material->id) }}" alt="Attachment for {{ $material->title }}" class="attachment-preview mb-3" loading="lazy">
                @endif
                <div class="attachment">
                    <span class="attachment-icon" aria-hidden="true"><i class="{{ $icon }}"></i></span>
                    <div class="flex-fill min-w-0">
                        <div class="fw-medium text-truncate">{{ Str::slug($material->title) }}.{{ $material->fileExtension() }}</div>
                        <div class="small text-muted-2 text-uppercase">{{ $material->fileExtension() }}</div>
                    </div>
                    <a href="{{ route('material.file', $material->id) }}" class="btn btn-sm btn-quiet" target="_blank" rel="noopener">Open</a>
                    <a href="{{ route('material.file', ['material' => $material->id, 'download' => 1]) }}" class="btn btn-sm btn-primary" aria-label="Download attachment">
                        <i class="fa-solid fa-download" aria-hidden="true"></i>
                    </a>
                </div>
            @elseif ($material->file)
                <p class="alert alert-danger mt-4 mb-0">The attached file could not be found. Ask your teacher to upload it again.</p>
            @endif
        </div>
    </article>
</div>
@endsection
