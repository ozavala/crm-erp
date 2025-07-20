@csrf
<div class="mb-3">
    <label for="name" class="form-label">{{ __('Feature Name') }} <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $productFeature->name ?? '') }}" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">{{ __('Description') }} ({{ __('Optional') }})</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $productFeature->description ?? '') }}</textarea>
    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<button type="submit" class="btn btn-primary">{{ isset($productFeature) ? __('Update Feature') : __('Create Feature') }}</button>
<a href="{{ route('product-features.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>