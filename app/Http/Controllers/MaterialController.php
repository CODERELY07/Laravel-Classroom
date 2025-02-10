<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Material;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request):JsonResponse
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:classess,id', 
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:10240', 
        ]);
        $filePath = null;
        // Store file in the storage and in the database
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('materials', 'public'); 
        }

        DB::table('materials')->insert([
            'class_id' => $validated['class_id'],
            'user_id' => auth()->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'file' => $filePath, 
            'created_at' => now(), 
            'updated_at' => now(),    
        ]);

        return response()->json(['success' => 'Material created successfully', 'redirectUrl' => route('stream', [$request->class_id])]);
    }
    public function show(string $id)
    {
        $materials = Material::findOrFail($id);
        return view('user.class.material', ['materials' => $materials]);
      
    }
    // show material File
    public function showMaterialFile($file){
        return response()->file(storage_path("app/public/materials/{$file}"));
    }
   
}
