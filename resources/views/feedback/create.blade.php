@extends('layouts.app')

@section('title', __('feedback.Submit Feedback'))

@section('content')
<div class="container">
    <h1>{{ __('feedback.Submit Feedback or Suggestion') }}</h1>
    <p class="text-muted">{{ __('feedback.We appreciate you taking the time to help us improve the application.') }}</p>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('feedback.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="type" class="form-label">{{ __('feedback.Feedback Type') }} <span class="text-danger">*</span></label>
                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                        <option value="Suggestion" {{ old('type') == 'Suggestion' ? 'selected' : '' }}>{{ __('feedback.Suggestion') }}</option>
                        <option value="Feature Request" {{ old('type') == 'Feature Request' ? 'selected' : '' }}>{{ __('feedback.Feature Request') }}</option>
                        <option value="Bug Report" {{ old('type') == 'Bug Report' ? 'selected' : '' }}>{{ __('feedback.Bug Report') }}</option>
                    </select>
                    @error('type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label">{{ __('feedback.Title / Summary') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">{{ __('feedback.Description') }} <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">{{ __('feedback.Submit Feedback') }}</button>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">{{ __('feedback.Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection