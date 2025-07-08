@section('title', __('feedback.Feedback Details'))
    <h1>{{ __('feedback.Feedback Details') }}</h1>
            <p><strong>{{ __('feedback.Type:') }}</strong> {{ $feedback->type }}</p>
            <p><strong>{{ __('feedback.Message:') }}</strong> {{ $feedback->message }}</p>
            <p><strong>{{ __('feedback.Created At:') }}</strong> {{ $feedback->created_at->format('Y-m-d H:i:s') }}</p>
        <a href="{{ route('feedback.index') }}" class="btn btn-secondary">{{ __('feedback.Back to List') }}</a>
