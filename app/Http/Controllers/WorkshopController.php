<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\Http\Request;

class WorkshopController extends Controller
{
    public function index()
    {
        $workshops = Workshop::latest()->paginate(12);
        return view('workshop.index', compact('workshops'));
    }

    public function create()
    {
        return view('workshop.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
        ]);

        Workshop::create($validated);

        return redirect()->route('workshop.index')->with('success', 'Workshop created successfully.');
    }

    public function show(string $id)
    {
        $workshop = Workshop::findOrFail($id);
        return view('workshop.show', compact('workshop'));
    }

    public function edit(string $id)
    {
        $workshop = Workshop::findOrFail($id);
        return view('workshop.edit', compact('workshop'));
    }

    public function update(Request $request, string $id)
    {
        $workshop = Workshop::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'description' => 'required|string',
        ]);

        $workshop->update($validated);

        return redirect()->route('workshop.index')->with('success', 'Workshop updated successfully.');
    }

    public function destroy(string $id)
    {
        $workshop = Workshop::findOrFail($id);
        $workshop->delete();

        return redirect()->route('workshop.index')->with('success', 'Workshop deleted successfully.');
    }
}
