<?php

namespace App\Http\Controllers;

use App\Models\Classess;
use App\Models\Material;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MaterialController extends Controller
{
    public const ALLOWED_TYPES = 'jpg,jpeg,png,gif,webp,pdf,doc,docx,ppt,pptx,xls,xlsx,txt';

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'class_id' => 'required|integer|exists:classess,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:5000',
            'file' => 'nullable|file|mimes:'.self::ALLOWED_TYPES.'|max:10240',
        ]);

        $class = Classess::findOrFail($data['class_id']);
        Gate::authorize('teach', $class);
        $this->ensureActive($class);

        $path = $request->file('file')?->store('materials', Material::DISK);

        $class->materials()->create([
            'user_id' => $request->user()->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'file' => $path,
        ]);

        return $this->success('Material posted.', redirectUrl: route('stream', $class->id).'#classwork');
    }

    public function show(Material $material)
    {
        $material->load('user', 'classroom');
        Gate::authorize('view', $material->classroom);

        return view('user.class.material', [
            'material' => $material,
            'hasFile' => $material->fileDisk() !== null,
        ]);
    }

    public function destroy(Material $material): JsonResponse
    {
        $class = $material->classroom;
        Gate::authorize('teach', $class);
        $this->ensureActive($class);

        $material->deleteFile();
        $material->delete();

        return $this->success('Material deleted.', redirectUrl: route('stream', $class->id).'#classwork');
    }

    /** Stream the attachment to class members only. */
    public function download(Request $request, Material $material)
    {
        Gate::authorize('view', $material->classroom);

        $disk = $material->fileDisk();
        abort_unless($disk, 404);

        $name = (Str::slug($material->title) ?: 'attachment').'.'.$material->fileExtension();

        // Images and PDFs open in the browser; anything else downloads.
        $inline = $material->isImage() || $material->fileExtension() === 'pdf';

        return $inline && ! $request->boolean('download')
            ? Storage::disk($disk)->response($material->file, $name, ['X-Content-Type-Options' => 'nosniff'])
            : Storage::disk($disk)->download($material->file, $name);
    }
}
