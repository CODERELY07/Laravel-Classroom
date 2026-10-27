<x-modal id="profileModal" title="Profile photo">
    <form action="{{ route('user.changeProfile') }}" data-ajax-form enctype="multipart/form-data" novalidate>
        <div class="modal-body text-center">
            <div class="form-alert text-start"></div>
            <p class="text-muted-2">A photo helps classmates and teachers recognise you.</p>

            <div class="avatar-picker my-3">
                <x-avatar :user="$authUser" size="xl" data-avatar-preview />
                <label for="profile_picture" title="Choose a photo">
                    <i class="fa-solid fa-camera" aria-hidden="true"></i>
                    <span class="visually-hidden">Choose a photo</span>
                </label>
            </div>
            <input type="file" id="profile_picture" name="profile_picture" class="visually-hidden" accept="image/jpeg,image/png,image/gif,image/webp" data-avatar-input>
            <div class="invalid-feedback d-block" data-error-for="profile_picture"></div>
            <p class="form-text mb-0">JPG, PNG, GIF or WebP, up to 2&nbsp;MB.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-quiet" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" data-avatar-save disabled>Save photo</button>
        </div>
    </form>
</x-modal>
