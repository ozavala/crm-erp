@csrf
<div class="row">
    <div class="col-md-8 mb-3">
        <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject', $quotation->subject ?? '') }}" required>
        @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="opportunity_id" class="form-label">Related Opportunity <span class="text-danger">*</span></label>
        <select class="form-select @error('opportunity_id') is-invalid @enderror" id="opportunity_id" name="opportunity_id" required>
            <option value="">Select Opportunity</option>
            @foreach($opportunities as $opportunity)
                <option value="{{ $opportunity->opportunity_id }}" {{ (old('opportunity_id', $quotation->opportunity_id ?? $selectedOpportunityId ?? '') == $opportunity->opportunity_id) ? 'selected' : '' }}>
                    {{ $opportunity->name }}
                </option>
            @endforeach
        </select>
        @error('opportunity_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="quotation_date" class="form-label">Quotation Date <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('quotation_date') is-invalid @enderror" id="quotation_date" name="quotation_date" value="{{ old('quotation_date', ($quotation->quotation_date ?? now())->format('Y-m-d')) }}" required>
        @error('quotation_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="expiry_date" class="form-label">Expiry Date</label>
        <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $quotation->expiry_date ? $quotation->expiry_date->format('Y-m-d') : '') }}">
        @error('expiry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            @foreach($statuses as $key => $value)
                <option value="{{ $key }}" {{ old('status', $quotation->status ?? 'Draft') == $key ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </select>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<hr>
<h4>Line Items</h4>
<div id="quotation-items-container">
    @php
        $quotationItems = old('items', isset($quotation) ? $quotation->items->toArray() : [['product_id' => '', 'item_name' => '', 'item_description' => '', 'quantity' => 1, 'unit_price' => '']]);
        $itemIndex = 0;
    @endphp

    @foreach($quotationItems as $index => $item)
        @php $itemIndex = $index; @endphp
        <div class="row align-items-center mb-2 quotation-item-row border p-2 rounded">
            <input type="hidden" name="items[{{ $index }}][quotation_item_id]" value="{{ $item['quotation_item_id'] ?? '' }}">
            <div class="col-md-3 mb-2">
                <label class="form-label">Product/Service</label>
                <select name="items[{{ $index }}][product_id]" class="form-select product-select">
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->product_id }}" data-price="{{ $product->price }}" data-description="{{ $product->description }}" {{ ($item['product_id'] ?? '') == $product->product_id ? 'selected' : '' }}>
                            {{ $product->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label">Item Name <span class="text-danger">*</span></label>
                <input type="text" name="items[{{ $index }}][item_name]" class="form-control item-name" value="{{ $item['item_name'] ?? '' }}" required>
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label">Quantity <span class="text-danger">*</span></label>
                <input type="number" name="items[{{ $index }}][quantity]" class="form-control item-quantity" value="{{ $item['quantity'] ?? 1 }}" min="1" required>
            </div>
            <div class="col-md-2 mb-2">
                <label class="form-label">Unit Price <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="items[{{ $index }}][unit_price]" class="form-control item-unit-price" value="{{ $item['unit_price'] ?? '' }}" min="0" required>
            </div>
            <div class="col-md-1 mb-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-item-btn">&times;</button>
            </div>
            <div class="col-md-12 mb-2">
                <label class="form-label">Description</label>
                <textarea name="items[{{ $index }}][item_description]" class="form-control item-description" rows="1">{{ $item['item_description'] ?? '' }}</textarea>
            </div>
        </div>
    @endforeach
</div>
<button type="button" id="add-item-btn" class="btn btn-success btn-sm mt-2">Add Item</button>
@error('items') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
@foreach ($errors->get('items.*') as $message)
    <div class="text-danger small mt-1">{{ $message[0] }}</div>
@endforeach

<hr>

<div class="row justify-content-end">
    <div class="col-md-4">
        <div class="row mb-2">
            <label for="discount_type" class="col-sm-4 col-form-label">Discount Type</label>
            <div class="col-sm-8">
                <select name="discount_type" id="discount_type" class="form-select @error('discount_type') is-invalid @enderror">
                    <option value="">None</option>
                    <option value="percentage" {{ old('discount_type', $quotation->discount_type ?? '') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                    <option value="fixed" {{ old('discount_type', $quotation->discount_type ?? '') == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                </select>
                @error('discount_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="row mb-2">
            <label for="discount_value" class="col-sm-4 col-form-label">Discount Value</label>
            <div class="col-sm-8">
                <input type="number" step="0.01" name="discount_value" id="discount_value" class="form-control @error('discount_value') is-invalid @enderror" value="{{ old('discount_value', $quotation->discount_value ?? 0) }}">
                @error('discount_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="row mb-2">
            <label for="tax_percentage" class="col-sm-4 col-form-label">Tax (%)</label>
            <div class="col-sm-8">
                <input type="number" step="0.01" name="tax_percentage" id="tax_percentage" class="form-control @error('tax_percentage') is-invalid @enderror" value="{{ old('tax_percentage', $quotation->tax_percentage ?? 0) }}">
                @error('tax_percentage') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
    </div>
</div>

<div class="mb-3">
    <label for="terms_and_conditions" class="form-label">Terms & Conditions</label>
    <textarea class="form-control @error('terms_and_conditions') is-invalid @enderror" id="terms_and_conditions" name="terms_and_conditions" rows="3">{{ old('terms_and_conditions', $quotation->terms_and_conditions ?? '') }}</textarea>
    @error('terms_and_conditions') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="notes" class="form-label">Notes</label>
    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $quotation->notes ?? '') }}</textarea>
    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary">{{ isset($quotation->quotation_id) ? 'Update Quotation' : 'Create Quotation' }}</button>
    <a href="{{ route('quotations.index') }}" class="btn btn-secondary">Cancel</a>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const itemsContainer = document.getElementById('quotation-items-container');
    const addItemBtn = document.getElementById('add-item-btn');
    let itemRowIndex = {{ $itemIndex + 1 }};

    addItemBtn.addEventListener('click', function () {
        const newRow = document.createElement('div');
        newRow.classList.add('row', 'align-items-center', 'mb-2', 'quotation-item-row', 'border', 'p-2', 'rounded');
        newRow.innerHTML = `
            <input type="hidden" name="items[${itemRowIndex}][quotation_item_id]" value="">
            <div class="col-md-3 mb-2"><label class="form-label">Product/Service</label><select name="items[${itemRowIndex}][product_id]" class="form-select product-select"><option value="">Select Product</option>@foreach($products as $product)<option value="{{ $product->product_id }}" data-price="{{ $product->price }}" data-description="{{ $product->description }}">{{ $product->name }}</option>@endforeach</select></div>
            <div class="col-md-3 mb-2"><label class="form-label">Item Name <span class="text-danger">*</span></label><input type="text" name="items[${itemRowIndex}][item_name]" class="form-control item-name" required></div>
            <div class="col-md-3 mb-2"><label class="form-label">Quantity <span class="text-danger">*</span></label><input type="number" name="items[${itemRowIndex}][quantity]" class="form-control item-quantity" value="1" min="1" required></div>
            <div class="col-md-2 mb-2"><label class="form-label">Unit Price <span class="text-danger">*</span></label><input type="number" step="0.01" name="items[${itemRowIndex}][unit_price]" class="form-control item-unit-price" min="0" required></div>
            <div class="col-md-1 mb-2 d-flex align-items-end"><button type="button" class="btn btn-danger remove-item-btn">&times;</button></div>
            <div class="col-md-12 mb-2"><label class="form-label">Description</label><textarea name="items[${itemRowIndex}][item_description]" class="form-control item-description" rows="1"></textarea></div>
        `;
        itemsContainer.appendChild(newRow);
        itemRowIndex++;
    });

    itemsContainer.addEventListener('click', function (event) {
        if (event.target.classList.contains('remove-item-btn')) {
            event.target.closest('.quotation-item-row').remove();
        }
    });

    itemsContainer.addEventListener('change', function(event) {
        if (event.target.classList.contains('product-select')) {
            const selectedOption = event.target.options[event.target.selectedIndex];
            const row = event.target.closest('.quotation-item-row');
            if (selectedOption.value) {
                row.querySelector('.item-name').value = selectedOption.text.trim();
                row.querySelector('.item-unit-price').value = selectedOption.dataset.price || '';
                row.querySelector('.item-description').value = selectedOption.dataset.description || '';
            }
        }
    });
});
</script>
@endpush