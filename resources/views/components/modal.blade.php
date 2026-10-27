@props(['id', 'title' => null, 'size' => null])
<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div @class(['modal-dialog modal-dialog-centered modal-dialog-scrollable', 'modal-'.$size => $size])>
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="{{ $id }}Label">{{ $title ?? $heading ?? '' }}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            {{ $slot }}
        </div>
    </div>
</div>
