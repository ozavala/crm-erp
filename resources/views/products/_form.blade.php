@csrf
<div class="row">
    <div class="col-md-8 mb-3">
        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $product->name ?? '') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="sku" class="form-label">SKU (Stock Keeping Unit)</label>
        <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku', $product->sku ?? '') }}">
        @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="mb-3">
    <label for="description" class="form-label">Description</label>
    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $product->description ?? '') }}</textarea>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="price" class="form-label">Price ($) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', isset($product->price) ? number_format($product->price, 2, '.', '') : '') }}" required>
        @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="cost" class="form-label">Cost ($) (Optional)</label>
        <input type="number" step="0.01" class="form-control @error('cost') is-invalid @enderror" id="cost" name="cost" value="{{ old('cost', isset($product->cost) ? number_format($product->cost, 2, '.', '') : '') }}">
        @error('cost') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="is_service" class="form-label">Type <span class="text-danger">*</span></label>
        <select class="form-select @error('is_service') is-invalid @enderror" id="is_service" name="is_service" required>
            <option value="0" {{ old('is_service', $product->is_service ?? '0') == '0' ? 'selected' : '' }}>Product (Track Stock)</option>
            <option value="1" {{ old('is_service', $product->is_service ?? '0') == '1' ? 'selected' : '' }}>Service (No Stock)</option>
        </select>
        @error('is_service') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3" id="quantity_on_hand_field" style="{{ old('is_service', $product->is_service ?? '0') == '1' ? 'display:none;' : '' }}">
        <label for="quantity_on_hand" class="form-label">Quantity on Hand</label>
        <input type="number" class="form-control @error('quantity_on_hand') is-invalid @enderror" id="quantity_on_hand" name="quantity_on_hand" value="{{ old('quantity_on_hand', $product->quantity_on_hand ?? 0) }}">
        @error('quantity_on_hand') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="is_active" class="form-label">Status <span class="text-danger">*</span></label>
        <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active" required>
            <option value="1" {{ old('is_active', $product->is_active ?? '1') == '1' ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('is_active', $product->is_active ?? '1') == '0' ? 'selected' : '' }}>Inactive</option>
        </select>
        @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<hr class="my-4">

<h4>Product Features</h4>
<div id="product-features-container">
    @php
        $existingFeatures = old('features', isset($product) ? $product->features->mapWithKeys(function ($feature) {
            return [ $loop->index => ['feature_id' => $feature->feature_id, 'value' => $feature->pivot->value] ];
        })->all() : []);
        $featureIndex = 0;
    @endphp

    @if(!empty($existingFeatures))
        @foreach($existingFeatures as $index => $featureData)
            @php $featureIndex = $index; @endphp
            <div class="row align-items-end mb-2 product-feature-row">
                <div class="col-md-5">
                    <label class="form-label">Feature</label>
                    <select name="features[{{ $index }}][feature_id]" class="form-select">
                        <option value="">Select Feature</option>
                        @foreach($productFeatures as $pf)
                            <option value="{{ $pf->feature_id }}" {{ ($featureData['feature_id'] ?? '') == $pf->feature_id ? 'selected' : '' }}>{{ $pf->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Value</label>
                    <input type="text" name="features[{{ $index }}][value]" class="form-control" value="{{ $featureData['value'] ?? '' }}">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-feature-btn">Remove</button>
                </div>
            </div>
        @endforeach
    @endif
</div>
<button type="button" id="add-feature-btn" class="btn btn-success btn-sm mt-2">Add Feature</button>

@error('features') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
@foreach ($errors->get('features.*') as $message)
    <div class="text-danger small mt-1">{{ $message[0] }}</div>
@endforeach

<hr class="my-4">

<div id="inventory-management-section" style="{{ old('is_service', $product->is_service ?? '0') == '1' ? 'display:none;' : '' }}">
    <h4>Inventory Levels per Warehouse</h4>
    @if(isset($warehouses) && $warehouses->count() > 0)
        @foreach($warehouses as $warehouse)
            @php
                $currentQuantity = old('inventory.'.$warehouse->warehouse_id.'.quantity', $product->warehouses->find($warehouse->warehouse_id)->pivot->quantity ?? 0);
            @endphp
            <div class="row mb-2 align-items-center">
                <label for="inventory_{{ $warehouse->warehouse_id }}" class="col-md-3 col-form-label">{{ $warehouse->name }}</label>
                <div class="col-md-3">
                    <input type="number" class="form-control @error('inventory.'.$warehouse->warehouse_id.'.quantity') is-invalid @enderror"
                           id="inventory_{{ $warehouse->warehouse_id }}"
                           name="inventory[{{ $warehouse->warehouse_id }}][quantity]"
                           value="{{ $currentQuantity }}" min="0">
                    @error('inventory.'.$warehouse->warehouse_id.'.quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        @endforeach
    @else
        <p>No active warehouses found. <a href="{{ route('warehouses.create') }}">Create a warehouse</a> to manage inventory.</p>
    @endif
    <hr class="my-4">
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary">{{ isset($product->product_id) ? 'Update' : 'Create' }} Product/Service</button>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">Cancel</a>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const typeSelect = document.getElementById('is_service');
        const quantityField = document.getElementById('quantity_on_hand_field');
        const quantityInput = document.getElementById('quantity_on_hand');
        const inventoryManagementSection = document.getElementById('inventory-management-section');
        let featureRowIndex = {{ $featureIndex + 1 }}; // Start index for new rows

        function toggleQuantityField() {
            if (typeSelect.value === '1') { // Service
                quantityField.style.display = 'none';
                if (inventoryManagementSection) inventoryManagementSection.style.display = 'none';
                quantityInput.value = 0; // Or clear it, or set to null if your backend handles it
            } else { // Product
                quantityField.style.display = 'block';
                if (inventoryManagementSection) inventoryManagementSection.style.display = 'block';
            }
        }

        typeSelect.addEventListener('change', toggleQuantityField);
        // Initial check in case of old input or editing
        toggleQuantityField();

        // Product Features
        const featuresContainer = document.getElementById('product-features-container');
        const addFeatureBtn = document.getElementById('add-feature-btn');

        addFeatureBtn.addEventListener('click', function() {
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'align-items-end', 'mb-2', 'product-feature-row');
            newRow.innerHTML = `
                <div class="col-md-5">
                    <label class="form-label">Feature</label>
                    <select name="features[${featureRowIndex}][feature_id]" class="form-select">
                        <option value="">Select Feature</option>
                        @foreach($productFeatures as $pf)
                            <option value="{{ $pf->feature_id }}">{{ $pf->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Value</label>
                    <input type="text" name="features[${featureRowIndex}][value]" class="form-control">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-feature-btn">Remove</button>
                </div>
            `;
            featuresContainer.appendChild(newRow);
            featureRowIndex++;
        });

        featuresContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-feature-btn')) {
                event.target.closest('.product-feature-row').remove();
            }
        });
    });
</script>
@endpush