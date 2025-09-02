@csrf
<div class="row">
    <div class="col-md-8 mb-3">
        <label for="subject" class="form-label">{{ __('quotations.subject') }} <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject', $quotation->subject ?? $selectedOpportunity?->name ?? '') }}" required>
        @error('subject') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="opportunity_id" class="form-label">{{ __('quotations.related_opportunity') }} <span class="text-danger">*</span></label>
        <select class="form-select @error('opportunity_id') is-invalid @enderror" id="opportunity_id" name="opportunity_id" required>
            <option value="">{{ __('quotations.select_opportunity') }}</option>
            @foreach($opportunities as $opportunity)
                <option value="{{ $opportunity->opportunity_id }}"
                        data-name="{{ $opportunity->name }}"
                        {{ (old('opportunity_id', $quotation->opportunity_id ?? $selectedOpportunity?->opportunity_id ?? '') == $opportunity->opportunity_id) ? 'selected' : '' }}>
                    {{ $opportunity->name }}
                </option>
            @endforeach
        </select>
        @error('opportunity_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label for="quotation_date" class="form-label">{{ __('quotations.quotation_date') }} <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('quotation_date') is-invalid @enderror" id="quotation_date" name="quotation_date" value="{{ old('quotation_date', ($quotation->quotation_date ?? now())->format('Y-m-d')) }}" required>
        @error('quotation_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="expiry_date" class="form-label">{{ __('quotations.expiry_date') }}</label>
        <input type="date" class="form-control @error('expiry_date') is-invalid @enderror" id="expiry_date" name="expiry_date" value="{{ old('expiry_date', $quotation->expiry_date ? $quotation->expiry_date->format('Y-m-d') : '') }}">
        @error('expiry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4 mb-3">
        <label for="status" class="form-label">{{ __('quotations.status') }} <span class="text-danger">*</span></label>
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            @foreach($statuses as $key => $value)
                <option value="{{ $key }}" {{ old('status', $quotation->status ?? 'Draft') == $key ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </select>
        @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<hr>
<h4>{{ __('quotations.line_items') }}</h4>
<div id="quotation-items-container">
    @php
        // Use old input if available, otherwise use items from the model. Default to one empty item for new forms.
        $items = old('items', $quotation->items->isNotEmpty() ? $quotation->items : [new \App\Models\QuotationItem()]);
    @endphp

    @foreach($items as $index => $item)
        @php
            // Ensure item is an object for consistent access, especially for old() data which is an array.
            $item = is_array($item) ? (object) $item : $item;
        @endphp
        <div class="row align-items-center mb-2 quotation-item-row border p-2 rounded">
            <input type="hidden" name="items[{{ $index }}][quotation_item_id]" value="{{ $item->quotation_item_id ?? '' }}">
            <div class="col-md-3 mb-2 product-search-container position-relative">
                <label class="form-label">{{ __('quotations.product_service') }}</label>
                <input type="text" class="form-control product-search" 
                       placeholder="{{ __('quotations.search_product_placeholder', ['default' => 'Search product...']) }}" 
                       value="{{ optional($item->product)->name ?? $item->item_name ?? '' }}" 
                       autocomplete="off">
                <input type="hidden" name="items[{{ $index }}][product_id]" class="product-id" value="{{ $item->product_id ?? '' }}">
                <div class="search-results-container"></div>
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label">{{ __('quotations.item_name') }} <span class="text-danger">*</span></label>
                <input type="text" name="items[{{ $index }}][item_name]" class="form-control item-name" value="{{ $item->item_name ?? '' }}" required>
            </div>
            <div class="col-md-3 mb-2">
                <label class="form-label">{{ __('quotations.quantity') }} <span class="text-danger">*</span></label>
                <input type="number" name="items[{{ $index }}][quantity]" class="form-control item-quantity" value="{{ $item->quantity ?? 1 }}" min="1" required>
            </div>
            <div class="col-md-2 mb-2">
                <label class="form-label">{{ __('quotations.unit_price') }} <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="items[{{ $index }}][unit_price]" class="form-control item-unit-price" value="{{ $item->unit_price ?? '' }}" min="0" required>
            </div>
            <div class="col-md-1 mb-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-item-btn">&times;</button>
            </div>
            <div class="col-md-12 mb-2">
                <label class="form-label">{{ __('quotations.item_description') }}</label>
                <textarea name="items[{{ $index }}][item_description]" class="form-control item-description" rows="1">{{ $item->item_description ?? '' }}</textarea>
            </div>
        </div>
    @endforeach
</div>
<button type="button" id="add-item-btn" class="btn btn-success btn-sm mt-2">{{ __('quotations.add_item') }}</button>
@error('items') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
@foreach ($errors->get('items.*') as $message)
    <div class="text-danger small mt-1">{{ $message[0] }}</div>
@endforeach

<hr>

<div class="row justify-content-end">
    <div class="col-md-5">
        <div class="row mb-2">
            <label for="discount_type" class="col-sm-4 col-form-label">{{ __('quotations.discount_type') }}</label>
            <div class="col-sm-8">
                <select name="discount_type" id="discount_type" class="form-select @error('discount_type') is-invalid @enderror">
                    <option value="">{{ __('quotations.none') }}</option>
                    <option value="percentage" {{ old('discount_type', $quotation->discount_type ?? '') == 'percentage' ? 'selected' : '' }}>{{ __('quotations.percentage') }}</option>
                    <option value="fixed" {{ old('discount_type', $quotation->discount_type ?? '') == 'fixed' ? 'selected' : '' }}>{{ __('quotations.fixed_amount') }}</option>
                </select>
                @error('discount_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="row mb-2">
            <label for="discount_value" class="col-sm-4 col-form-label">{{ __('quotations.discount_value') }}</label>
            <div class="col-sm-8">
                <input type="number" step="0.01" name="discount_value" id="discount_value" class="form-control @error('discount_value') is-invalid @enderror" value="{{ old('discount_value', $quotation->discount_value ?? 0) }}">
                @error('discount_value') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <div class="row mb-2">
            <label for="tax_percentage" class="col-sm-4 col-form-label">{{ __('quotations.tax_percentage') }}</label>
            <div class="col-sm-8">
                <input type="number" step="0.01" name="tax_percentage" id="tax_percentage" class="form-control @error('tax_percentage') is-invalid @enderror" value="{{ old('tax_percentage', $quotation->tax_percentage ?? 0) }}">
                @error('tax_percentage') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>
        <hr>
        <div class="row mb-2">
            <strong class="col-sm-4 col-form-label">{{ __('quotations.subtotal') }}</strong>
            <div class="col-sm-8">
                <span id="display-subtotal">$0.00</span>
            </div>
        </div>
        <div class="row mb-2">
            <strong class="col-sm-4 col-form-label">{{ __('quotations.discount_amount') }}</strong>
            <div class="col-sm-8">
                <span id="display-discount">$0.00</span>
            </div>
        </div>
        <div class="row mb-2">
            <strong class="col-sm-4 col-form-label">{{ __('quotations.tax_amount') }}</strong>
            <div class="col-sm-8">
                <span id="display-tax">$0.00</span>
            </div>
        </div>
        <div class="row mb-2">
            <strong class="col-sm-4 col-form-label">{{ __('quotations.total_amount') }}</strong>
            <div class="col-sm-8">
                <span id="display-total" class="fw-bold fs-5">$0.00</span>
            </div>
        </div>
    </div>
</div>

<div class="mb-3">
    <label for="terms_and_conditions" class="form-label">{{ __('quotations.terms_and_conditions') }}</label>
    <textarea class="form-control @error('terms_and_conditions') is-invalid @enderror" id="terms_and_conditions" name="terms_and_conditions" rows="3">{{ old('terms_and_conditions', $quotation->terms_and_conditions ?? '') }}</textarea>
    @error('terms_and_conditions') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mb-3">
    <label for="notes" class="form-label">{{ __('quotations.notes') }}</label>
    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $quotation->notes ?? '') }}</textarea>
    @error('notes') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>

<div class="mt-4">
    <button type="submit" class="btn btn-primary">{{ isset($quotation->quotation_id) ? __('quotations.update_quotation') : __('quotations.create_quotation') }}</button>
    <a href="{{ route('quotations.index') }}" class="btn btn-secondary">{{ __('quotations.cancel') }}</a>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const itemsContainer = document.getElementById('quotation-items-container');
    const addItemBtn = document.getElementById('add-item-btn');
    const opportunitySelect = document.getElementById('opportunity_id');
    const subjectInput = document.getElementById('subject');
    let itemRowIndex = document.querySelectorAll('.quotation-item-row').length;

    const translations = <?php echo json_encode([
        'product_service' => __('quotations.product_service'),
        'item_name' => __('quotations.item_name'),
        'quantity' => __('quotations.quantity'),
        'unit_price' => __('quotations.unit_price'),
        'item_description' => __('quotations.item_description'),
        'search_placeholder' => __('quotations.search_product_placeholder')
    ]); ?>;

    // --- Helper Functions ---
    function debounce(func, delay) {
        let timeout;
        return function(...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat(document.documentElement.lang || 'en-US', { style: 'currency', currency: 'USD' }).format(amount);
    }

    // --- Main Calculation Function ---
    function updateTotals() {
        let subtotal = 0;
        document.querySelectorAll('.quotation-item-row').forEach(row => {
            const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.item-unit-price').value) || 0;
            subtotal += quantity * unitPrice;
        });

        const discountType = document.getElementById('discount_type').value;
        const discountValue = parseFloat(document.getElementById('discount_value').value) || 0;
        let discountAmount = 0;
        if (discountValue > 0) {
            if (discountType === 'percentage') {
                discountAmount = (subtotal * discountValue) / 100;
            } else if (discountType === 'fixed') {
                discountAmount = discountValue;
            }
        }

        const subtotalAfterDiscount = subtotal - discountAmount;
        const taxPercentage = parseFloat(document.getElementById('tax_percentage').value) || 0;
        const taxAmount = (subtotalAfterDiscount * taxPercentage) / 100;
        const totalAmount = subtotalAfterDiscount + taxAmount;

        document.getElementById('display-subtotal').textContent = formatCurrency(subtotal);
        document.getElementById('display-discount').textContent = formatCurrency(discountAmount);
        document.getElementById('display-tax').textContent = formatCurrency(taxAmount);
        document.getElementById('display-total').textContent = formatCurrency(totalAmount);
    }

    // --- Product Search Functions ---
    async function searchProducts(term, resultsContainer) {
        if (term.length < 2) {
            resultsContainer.innerHTML = '';
            return;
        }
        try {
            const response = await fetch(`/api/products/search?q=${encodeURIComponent(term)}`, {
                headers: { 
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + (localStorage.getItem('api_token') || '')
                }
            });
            if (!response.ok) throw new Error('Network response was not ok');
            const products = await response.json();
            
            let resultsHtml = '<ul class="list-group position-absolute w-100" style="z-index: 1000;">';
            if (products.length > 0) {
                products.forEach(product => {
                    resultsHtml += `<li class="list-group-item list-group-item-action search-result-item" 
                                        data-product-id="${product.product_id}" 
                                        data-name="${product.name}" 
                                        data-price="${product.price}" 
                                        data-description="${product.description || ''}">
                                        <strong>${product.name}</strong> (${product.sku})
                                    </li>`;
                });
            } else {
                resultsHtml += '<li class="list-group-item">No products found</li>';
            }
            resultsHtml += '</ul>';
            resultsContainer.innerHTML = resultsHtml;
        } catch (error) {
            console.error('Error fetching products:', error);
            resultsContainer.innerHTML = '<div class="list-group-item text-danger">Error loading results</div>';
        }
    }

    const debouncedSearch = debounce(searchProducts, 300);

    // --- Row Management Functions ---
    function createItemRow(index) {
        const newRow = document.createElement('div');
        newRow.classList.add('row', 'align-items-center', 'mb-2', 'quotation-item-row', 'border', 'p-2', 'rounded');
        newRow.innerHTML = `
            <input type="hidden" name="items[${index}][quotation_item_id]" value="">
            <div class="col-md-3 mb-2 product-search-container position-relative">
                <label class="form-label">${translations.product_service}</label>
                <input type="text" class="form-control product-search" placeholder="${translations.search_placeholder}" autocomplete="off">
                <input type="hidden" name="items[${index}][product_id]" class="product-id">
                <div class="search-results-container"></div>
            </div>
            <div class="col-md-3 mb-2"><label class="form-label">${translations.item_name} <span class="text-danger">*</span></label><input type="text" name="items[${index}][item_name]" class="form-control item-name" required></div>
            <div class="col-md-3 mb-2"><label class="form-label">${translations.quantity} <span class="text-danger">*</span></label><input type="number" name="items[${index}][quantity]" class="form-control item-quantity" value="1" min="1" required></div>
            <div class="col-md-2 mb-2"><label class="form-label">${translations.unit_price} <span class="text-danger">*</span></label><input type="number" step="0.01" name="items[${index}][unit_price]" class="form-control item-unit-price" value="0.00" min="0" required></div>
            <div class="col-md-1 mb-2 d-flex align-items-end"><button type="button" class="btn btn-danger remove-item-btn">&times;</button></div>
            <div class="col-md-12 mb-2"><label class="form-label">${translations.item_description}</label><textarea name="items[${index}][item_description]" class="form-control item-description" rows="1"></textarea></div>
        `;
        return newRow;
    }

    // --- Event Listeners ---
    itemsContainer.addEventListener('input', function(e) {
        const target = e.target;
        if (target.classList.contains('product-search')) {
            const resultsContainer = target.closest('.product-search-container').querySelector('.search-results-container');
            debouncedSearch(target.value, resultsContainer);
        }
        if (target.classList.contains('item-quantity') || target.classList.contains('item-unit-price')) {
            updateTotals();
        }
    });

    itemsContainer.addEventListener('click', function(e) {
        const target = e.target;
        if (target.classList.contains('search-result-item')) {
            const product = target.dataset;
            const row = target.closest('.quotation-item-row');

            row.querySelector('.product-search').value = product.name;
            row.querySelector('.product-id').value = product.productId;
            row.querySelector('.item-name').value = product.name;
            row.querySelector('.item-unit-price').value = parseFloat(product.price).toFixed(2);
            row.querySelector('.item-description').value = product.description;

            target.closest('.search-results-container').innerHTML = '';
            updateTotals();
        }
        if (target.classList.contains('remove-item-btn')) {
            target.closest('.quotation-item-row').remove();
            updateTotals();
        }
    });
    
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.product-search-container')) {
            document.querySelectorAll('.search-results-container').forEach(container => {
                container.innerHTML = '';
            });
        }
    });

    addItemBtn.addEventListener('click', () => {
        const newRow = createItemRow(itemRowIndex++);
        itemsContainer.appendChild(newRow);
    });

    opportunitySelect.addEventListener('change', function (e) {
        const selectedOption = e.target.options[e.target.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            subjectInput.value = '';
            return;
        }
        subjectInput.value = selectedOption.dataset.name;
    });

    document.getElementById('discount_type').addEventListener('change', updateTotals);
    document.getElementById('discount_value').addEventListener('input', updateTotals);
    document.getElementById('tax_percentage').addEventListener('input', updateTotals);
    
    updateTotals(); // Initial calculation
});
</script>
@endpush