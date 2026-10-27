{{-- Teacher-only dialogs on the class page. --}}
<x-modal id="createQuestionModal" title="New question" size="lg">
    <form action="{{ route('question.store') }}" data-ajax-form novalidate>
        <input type="hidden" name="class_id" value="{{ $class->id }}">
        <div class="modal-body">
            <div class="form-alert"></div>
            <div class="row g-3">
                <div class="col-md-6">
                    <x-field name="title" label="Title" placeholder="e.g. Chapter 3 review" maxlength="255" required />
                    <x-field name="description" label="Instructions" type="textarea" rows="4" maxlength="5000" placeholder="What should students do?" required />
                    <div class="row g-2">
                        <div class="col-6"><x-field name="points" label="Points" type="number" min="1" max="1000" value="10" required /></div>
                        <div class="col-6"><x-field name="duedate" label="Due date" type="date" :min="now()->toDateString()" required /></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-end justify-content-between gap-2 mb-2">
                        <label class="form-label mb-0" for="question-count">Questions</label>
                        <div class="input-group input-group-sm" style="max-width: 150px">
                            <span class="input-group-text">Items</span>
                            <input type="number" id="question-count" class="form-control" value="1" min="1" max="50" data-question-count>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-2" data-question-list style="max-height: 340px; overflow-y: auto"></div>
                    <div class="invalid-feedback d-block" data-error-for="question"></div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-quiet" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Post question</button>
        </div>
    </form>
</x-modal>

<x-modal id="createMaterialModal" title="New material">
    <form action="{{ route('material.store') }}" data-ajax-form enctype="multipart/form-data" novalidate>
        <input type="hidden" name="class_id" value="{{ $class->id }}">
        <div class="modal-body">
            <div class="form-alert"></div>
            <x-field name="title" label="Title" placeholder="e.g. Week 4 slides" maxlength="255" required />
            <x-field name="description" label="Description" type="textarea" rows="4" maxlength="5000" required />
            <x-field name="file" label="Attachment (optional)" type="file"
                     accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt"
                     hint="PDF, Office documents, images or text, up to 10 MB." />
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-quiet" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Post material</button>
        </div>
    </form>
</x-modal>

<x-modal id="addPeopleModal" title="Add people">
    <form action="{{ route('stream.store', $class->id) }}" data-ajax-form data-people-form data-search-url="{{ route('stream.search', $class->id) }}" novalidate>
        <div class="modal-body">
            <div class="form-alert"></div>
            <label for="peopleSearch" class="form-label">Search by email</label>
            <input type="search" id="peopleSearch" class="form-control" placeholder="Type at least 3 characters" autocomplete="off" data-people-search>
            <div class="invalid-feedback d-block" data-error-for="added_users"></div>
            <ul class="search-results" data-people-results aria-live="polite"></ul>
            <p class="form-label mt-3 mb-2" data-people-selected-label hidden>To be added</p>
            <div class="chip-list" data-people-selected></div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-quiet" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Add to class</button>
        </div>
    </form>
</x-modal>
