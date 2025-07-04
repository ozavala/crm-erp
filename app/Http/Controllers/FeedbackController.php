<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Http\Requests\StoreFeedbackRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('view-feedback');

        $feedbackItems = Feedback::with('user')->latest()->paginate(15);
        return view('feedback.index', compact('feedbackItems'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('feedback.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFeedbackRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = Auth::id();
        $validatedData['status'] = 'New'; // Default status

        Feedback::create($validatedData);

        return redirect()->route('dashboard')->with('success', 'Thank you for your feedback!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Feedback $feedback)
    {
        $user = Auth::user();
        // Allow user to see their own feedback, or admin to see all.
        if ($user->user_id !== $feedback->user_id && !$user->hasPermissionTo('view-feedback')) {
            abort(403);
        }

        $feedback->load('user');
        return view('feedback.show', compact('feedback'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Feedback $feedback)
    {
        Gate::authorize('edit-feedback');

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(['New', 'In Progress', 'Completed', 'Wont Fix'])],
        ]);

        $feedback->update($validated);

        return redirect()->route('feedback.show', $feedback->feedback_id)->with('success', 'Feedback status updated.');
    }
}