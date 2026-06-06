<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::latest()->paginate(12);
        return view('gallery.index', compact('galleries'));
    }

    public function create()
    {
        // Not used - using modal instead
        return redirect()->route('admin.gallery.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'photo' => 'required|file|mimes:jpeg,jpg,png,gif,webp,svg,bmp,tiff,tif,ico,heic,heif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/svg+xml,image/bmp,image/tiff,image/x-icon|max:20480',
        ]);

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('gallery', 'public');
            $validated['photo'] = $photoPath;
        }

        Gallery::create($validated);

        return redirect()->route('admin.gallery.index')->with('success', 'Gallery created successfully.');
    }

    public function show(string $id)
    {
        $gallery = Gallery::findOrFail($id);
        return view('gallery.show', compact('gallery'));
    }

    public function edit(string $id)
    {
        // Not used - using modal instead
        return redirect()->route('admin.gallery.index');
    }

    public function update(Request $request, string $id)
    {
        $gallery = Gallery::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'photo' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp,svg,bmp,tiff,tif,ico,heic,heif|mimetypes:image/jpeg,image/png,image/gif,image/webp,image/svg+xml,image/bmp,image/tiff,image/x-icon|max:20480',
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($gallery->photo && Storage::disk('public')->exists($gallery->photo)) {
                Storage::disk('public')->delete($gallery->photo);
            }
            $validated['photo'] = $request->file('photo')->store('gallery', 'public');
        }

        $gallery->update($validated);

        return redirect()->route('admin.gallery.index')->with('success', 'Gallery updated successfully.');
    }

    public function destroy(string $id)
    {
        $gallery = Gallery::findOrFail($id);

        // Delete photo
        if ($gallery->photo && Storage::disk('public')->exists($gallery->photo)) {
            Storage::disk('public')->delete($gallery->photo);
        }

        $gallery->delete();

        return redirect()->route('admin.gallery.index')->with('success', 'Gallery deleted successfully.');
    }
}
