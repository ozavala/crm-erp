@extends('layouts.app')

@section('title', __('feedback.Leave Feedback'))

@section('content')
<div class="container">
    <h1>{{ __('feedback.Leave Feedback') }}</h1>
    <p class="text-muted">{{ __('feedback.We appreciate you taking the time to help us improve the application.') }}</p>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('feedback.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="type" class="form-label">{{ __('feedback.Type') }}</label>
                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                        <option value="">{{ __('feedback.Select Type') }}</option>
                        <option value="bug" {{ old('type') == 'bug' ? 'selected' : '' }}>{{ __('feedback.Bug') }}</option>
                        <option value="suggestion" {{ old('type') == 'suggestion' ? 'selected' : '' }}>{{ __('feedback.Suggestion') }}</option>
                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>{{ __('feedback.Other') }}</option>
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
                    <label for="message" class="form-label">{{ __('feedback.Message') }}</label>
                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="4" required>{{ old('message') }}</textarea>
                    @error('message')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">{{ __('feedback.Submit Feedback') }}</button>
                    <a href="{{ route('feedback.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection