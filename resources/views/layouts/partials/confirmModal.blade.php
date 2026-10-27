{{-- One shared confirmation dialog, filled in by js/confirm.js from data-confirm-* attributes. --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-describedby="confirmModalBody" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body pb-2">
                <h2 class="modal-title mb-2" id="confirmModalLabel">Are you sure?</h2>
                <p class="text-muted-2 mb-0" id="confirmModalBody"></p>
            </div>
            <div class="modal-footer border-0 pt-2">
                <button type="button" class="btn btn-quiet" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" data-confirm-accept>Confirm</button>
            </div>
        </div>
    </div>
</div>
