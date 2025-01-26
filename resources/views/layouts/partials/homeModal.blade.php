 {{-- Create Class Modal --}}
 <x-modal  id="createClassModal">
    <x-slot name="heading">
       Create Class
    </x-slot>
    <div class="msg alert" style="display: none">

    </div>
    <form id="createClass" action="{{ route('createClass.post') }}" method="POST">
        @csrf
        <div class="mt-3">
            <label for="classname">Class Name:</label>
            <input type="text" name="classname" id="classname" class="form-control mt-2">
        </div>
        <div class="mt-3">
            <label for="section">Section:</label>
            <input type="text" name="section" id="section" class="form-control mt-2">
        </div>
        <div class="mt-3">
            <label for="subject">Subject:</label>
            <input type="text" name="subject" id="subject" class="form-control mt-2">
        </div>
        <div class="mt-3 float-end">
            <input type="submit" value="Create" class="btn btn-primary">
        </div>
    </form>
</x-modal>
{{-- Create Class Modal --}}

{{-- Join Class Modal --}}
<x-modal id="joinClassModal">
    <x-slot name="heading">
       Join Class
    </x-slot>
    <p>
        <strong>Class code</strong><br>
        Ask your teacher for the class code, then enter it here.</p>
    <div class="msg alert" style="display: none">

    </div>
    <form id="joinClassForm" action="{{route('join.post')}}">
        @csrf
        <div class="mt-3">
            <label for="classcode">Enter Class Code:</label>
            <input type="text" name="classcode" id="classcode" class="form-control mt-2">
        </div>
        <div class="mt-3 float-end">
            <input type="submit" value="Join" class="btn btn-primary">
        </div>
    </form>
</x-modal>
{{-- Join Class Modal --}}

{{-- Edit Class --}}
<x-modal  id="editClassModal">
    <x-slot name="heading">
       Edit Class
    </x-slot>
    <div class="msg alert" style="display: none">

    </div>
    <form id="editClassForm" method="POST">
        @csrf
        <div class="mt-3">
            <label for="classname">Class Name:</label>
            <input type="text" name="classname" id="editClassname" class="form-control mt-2">
        </div>
        <div class="mt-3">
            <label for="section">Section:</label>
            <input type="text" name="section" id="editSection" class="form-control mt-2">
        </div>
        <div class="mt-3">
            <label for="subject">Subject:</label>
            <input type="text" name="subject" id="editSubject" class="form-control mt-2">
        </div>
        <div class="mt-3 float-end">
            <input type="submit" value="Update" id="edit-class-btn" class="btn btn-primary">
        </div>
    </form>
</x-modal>
{{-- Edit Class --}}

{{-- Archive Class Modal --}}
<x-modal2 id="archiveClassModal">
    <span>Are you sure you want to archive this class?</span>
</x-modal2>
{{-- Archive Class Modal --}}

{{-- Unenroll Class Modal --}}
<x-modal2 id="unenrollClassModal">
    <span>Are you sure you want to unenroll this class?</span>
</x-modal2>
<x-modal2 id="unarchiveClassModal">
    <span>Are you sure you want to unarchive this class?</span>
</x-modal2>
<x-modal2 id="deleteClassModal">
    <span>Are you sure you want to delete this class?</span>
</x-modal2>
{{-- Archive Class Modal --}}