{{-- Modals used on the class list pages. --}}
@if (auth()->user()->isTeacher())
    <x-modal id="createClassModal" title="Create a class">
        <form action="{{ route('createClass.post') }}" data-ajax-form novalidate>
            <div class="modal-body">
                <div class="form-alert"></div>
                <x-field name="classname" label="Class name" placeholder="e.g. Grade 10 Biology" maxlength="100" required />
                <x-field name="section" label="Section" placeholder="e.g. Section B · Mornings" maxlength="100" required />
                <x-field name="subject" label="Subject" placeholder="e.g. Science" maxlength="100" required />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-quiet" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create class</button>
            </div>
        </form>
    </x-modal>

    <x-modal id="editClassModal" title="Edit class details">
        <form action="" data-ajax-form data-on-success="classes-changed" novalidate>
            <div class="modal-body">
                <div class="form-alert"></div>
                <x-field name="classname" label="Class name" maxlength="100" required />
                <x-field name="section" label="Section" maxlength="100" required />
                <x-field name="subject" label="Subject" maxlength="100" required />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-quiet" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </x-modal>
@endif

<x-modal id="joinClassModal" title="Join a class">
    <form action="{{ route('join.post') }}" data-ajax-form novalidate>
        <div class="modal-body">
            <div class="form-alert"></div>
            <p class="text-muted-2">Ask your teacher for the class code, then enter it here.</p>
            <x-field name="classcode" label="Class code" placeholder="e.g. a1b2c3d" autocomplete="off" autocapitalize="off" spellcheck="false" maxlength="20" required />
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-quiet" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Join</button>
        </div>
    </form>
</x-modal>
