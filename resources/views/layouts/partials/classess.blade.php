@if (collect($classess)->isEmpty())
<div class="mt-3 p-2">
    <h1 class="text-center">Welcome to Classroom!</h1>
    <p class="text-center">This platform is designed to help you manage and share your classroom materials.</p> 
</div>
@else
@foreach ($classess as $class)
    @if($class->status == 'ok')
    <x-class-card classname="{{$class->classname}}" section="{{$class->section}}" teacherName="{{$class->teacher_id}}">
        @if (auth()->user()->role === 'teacher')
            <li type="button" class="cursor-pointer show-modal text-dark edit-class" data-toggle="modal" data-target="#editClassModal"  data-id="{{$class->id}}"  data-classname={{$class->classname}} data-section={{$class->section}} data-subject={{$class->subject}}>Edit class</li>
            <li type="button" class="cursor-pointer show-modal text-dark archive-class" data-toggle="modal" data-target="#archiveClassModal" data-id="{{$class->id}}">Archive class</li>
        @else
            <li type="button" class="cursor-pointer show-modal text-dark unenroll-class" data-id="{{$class->id}}" data-toggle="modal" data-target="#unenrollClassModal">Unenroll class</li>
        @endif
    </x-class-card>
    @elseif ($class->status == 'archive')
    <x-class-card classname="{{$class->classname}}" section="{{$class->section}}" teacherName="{{$class->teacher_id}}">
        @if (auth()->user()->role === 'teacher')
            <li type="button" class="cursor-pointer show-modal text-dark unarchive-class" data-toggle="modal" data-target="#unarchiveClassModal" data-id="{{$class->id}}">Unarchive class</li>
            <li type="button" class="cursor-pointer show-modal text-dark delete-class" data-toggle="modal" data-target="#deleteClassModal"  data-id="{{$class->id}}"  data-classname={{$class->classname}} data-section={{$class->section}} data-subject={{$class->subject}}>Delete class</li>
        @else
            <li type="button" class="cursor-pointer show-modal text-dark unenroll-class" data-id="{{$class->id}}" data-toggle="modal" data-target="#unenrollClassModal">Unenroll class</li>
        @endif
    </x-class-card>
    @endif
@endforeach
@endif

