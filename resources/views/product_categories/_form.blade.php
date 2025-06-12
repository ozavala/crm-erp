@csrf
<div class="mb-3">
    <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $productCategory->name ?? '') }}" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="parent_category_id" class="form-label">Parent Category (Optional)</label>
    <select class="form-select @error('parent_category_id') is-invalid @enderror" id="parent_category_id" name="parent_category_id">
        <option value="">None</option>
        @foreach($categories as $category)
            {{-- Prevent selecting self or a category that has this category as an ancestor to avoid loops --}}
            @if(!isset($productCategory) || ($productCategory->category_id !== $category->category_id && !$category->isDescendantOf($productCategory ?? null)))
                <option value="{{ $category->category_id }}" {{ old('parent_category_id', $productCategory->parent_category_id ?? '') == $category->category_id ? 'selected' : '' }}>
                    {{ $category->name }}
                </option>
            @endif
        @endforeach
    </select>
    @error('parent_category_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description (Optional)</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $productCategory->description ?? '') }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<button type="submit" class="btn btn-primary">{{ isset($productCategory->category_id) ? 'Update Category' : 'Create Category' }}</button>
<a href="{{ route('product-categories.index') }}" class="btn btn-secondary">Cancel</a>