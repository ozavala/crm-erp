@csrf
<div class="mb-3">
    <label for="name" class="form-label">Warehouse Name <span class="text-danger">*</span></label>
    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $warehouse->name ?? '') }}" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="location" class="form-label">Location (e.g., City, Area)</label>
    <input type="text" class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location', $warehouse->location ?? '') }}">
    @error('location')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="address" class="form-label">Full Address</label>
    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $warehouse->address ?? '') }}</textarea>
    @error('address')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label for="is_active" class="form-label">Status <span class="text-danger">*</span></label>
    <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
        <option value="1" {{ old('is_active', $warehouse->is_active ?? '1') == '1' ? 'selected' : '' }}>Active</option>
        <option value="0" {{ old('is_active', $warehouse->is_active ?? '1') == '0' ? 'selected' : '' }}>Inactive</option>
    </select>
    @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<button type="submit" class="btn btn-primary">{{ isset($warehouse->warehouse_id) ? 'Update Warehouse' : 'Create Warehouse' }}</button>
<a href="{{ route('warehouses.index') }}" class="btn btn-secondary">Cancel</a>