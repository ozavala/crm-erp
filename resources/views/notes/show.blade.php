@section('title', __('notes.Note Details'))
    <h1>{{ __('notes.Note') }}</h1>
        <div class="card-header">
            {{ __('notes.Note Details') }}
        </div>
            <p><strong>{{ __('notes.Title:') }}</strong> {{ $note->title }}</p>
            <p><strong>{{ __('notes.Body:') }}</strong> {{ $note->body }}</p>
            <p><strong>{{ __('notes.Created At:') }}</strong> {{ $note->created_at->format('Y-m-d H:i:s') }}</p>
            <p><strong>{{ __('notes.Updated At:') }}</strong> {{ $note->updated_at->format('Y-m-d H:i:s') }}</p>
                <a href="{{ route('notes.edit', $note) }}" class="btn btn-warning">{{ __('notes.Edit') }}</a>
                <form action="{{ route('notes.destroy', $note) }}" method="POST" style="display:inline-block;" onsubmit="return confirm(__('notes.Are you sure you want to delete this note?'));">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('notes.Delete') }}</button>
                </form>
            <a href="{{ route('notes.index') }}" class="btn btn-secondary">{{ __('notes.Back to Notes') }}</a> 