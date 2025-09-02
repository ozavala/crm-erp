 @extends('layouts.app')
    @section('title')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3>{{ __('notes.Notes') }}</h3>
            <a href="{{ route('notes.store') }}" class="btn btn-primary">{{ __('notes.Add New') }}</a>
        </div>
    </div>
    @endsection

 @section('content')
    
    <h3>{{ __('messages.notes.Notes') }}</h3>
    <div class="card">
        <div class="card-header">
            {{ __('messages.notes.Note Details') }}
        </div>
            <p><strong>{{ __('messages.notes.Title:') }}</strong> {{ $note->title }}</p>
            <p><strong>{{ __('messages.notes.Body:') }}</strong> {{ $note->body }}</p>
            <p><strong>{{ __('messages.notes.Created At:') }}</strong> {{ $note->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>{{ __('messages.notes.Updated At:') }}</strong> {{ $note->updated_at->format('Y-m-d H:i:s') }}</p>
            <div class="card-body text-center mt-3 mb-3 mr-3 ml-3 d-flex">
                <div class = "d-grid gap-2 col-6 mx-auto d-sm-block">
                    <a href="{{ route('notes.edit', $note) }}" class="btn btn-warning">{{ __('messages.notes.Edit') }}</a>
                        <form action="{{ route('notes.destroy', $note) }}" method="POST" style="display:inline-block;" onsubmit="return confirm(__('notes.Are you sure you want to delete this note?'));">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">{{ __('messages.notes.Delete') }}</button>
                        </form>
                    <a href="{{ route('notes.index') }}" class="btn btn-secondary">{{ __('messages.notes.Back to Notes') }}</a> 
                </div>
            </div>
        </div>
    </div>
@endsection
           