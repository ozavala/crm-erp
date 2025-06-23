<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'body' => 'required|string',
            'noteable_id' => 'required|integer',
            'noteable_type' => 'required|string',
        ]);

        // Construct the full model class name
        $modelClass = 'App\\Models\\' . $request->input('noteable_type');

        if (!class_exists($modelClass)) {
            return back()->with('error', 'Invalid entity type provided.');
        }

        $noteable = $modelClass::findOrFail($request->input('noteable_id'));

        $noteable->notes()->create([
            'body' => $request->input('body'),
            'created_by_user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Note added successfully.');
    }

    public function destroy(Note $note)
    {
        $note->delete(); // Soft delete

        return back()->with('success', 'Note deleted successfully.');
    }
}