<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $opportunity->name) }}" required>
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="customer_id" class="form-label">Customer</label>
            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                <option value="">Select a Customer</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->customer_id }}" {{ old('customer_id', $selectedCustomerId ?? $opportunity->customer_id) == $customer->customer_id ? 'selected' : '' }}>
                        {{ $customer->company_name ?: $customer->full_name }}
                    </option>
                @endforeach
            </select>
            @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="contact_id" class="form-label">Contact</label>
            <select class="form-select @error('contact_id') is-invalid @enderror" id="contact_id" name="contact_id">
                <option value="">Select a Contact (optional)</option>
                @if($opportunity->exists && $contacts->isNotEmpty())
                    {{-- For edit mode, pre-populate contacts for the selected customer --}}
                    @foreach($contacts as $contact)
                        <option value="{{ $contact->contact_id }}" {{ old('contact_id', $opportunity->contact_id) == $contact->contact_id ? 'selected' : '' }}>
                            {{ $contact->first_name }} {{ $contact->last_name }}
                        </option>
                    @endforeach
                @endif
            </select>
            @error('contact_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="stage" class="form-label">Stage</label>
            <select class="form-select @error('stage') is-invalid @enderror" id="stage" name="stage" required>
                <option value="">Select Stage</option>
                @foreach($stages as $key => $value)
                    <option value="{{ $key }}" {{ old('stage', $opportunity->stage) == $key ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>
            @error('stage')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount</label>
            <input type="number" step="0.01" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', $opportunity->amount) }}">
            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="expected_close_date" class="form-label">Close Date</label>
            <input type="date" class="form-control @error('expected_close_date') is-invalid @enderror" id="expected_close_date" name="expected_close_date" value="{{ old('expected_close_date', $opportunity->expected_close_date?->format('Y-m-d')) }}">
            @error('expected_close_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="probability" class="form-label">Probability (%)</label>
            <input type="number" class="form-control @error('probability') is-invalid @enderror" id="probability" name="probability" value="{{ old('probability', $opportunity->probability) }}" min="0" max="100">
            @error('probability')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="assigned_to_user_id" class="form-label">Assigned To</label>
            <select class="form-select @error('assigned_to_user_id') is-invalid @enderror" id="assigned_to_user_id" name="assigned_to_user_id" required>
                <option value="">Select User</option>
                @foreach($crmUsers as $user)
                    <option value="{{ $user->user_id }}" {{ old('assigned_to_user_id', $opportunity->assigned_to_user_id) == $user->user_id ? 'selected' : '' }}>
                        {{ $user->full_name }}
                    </option>
                @endforeach
            </select>
            @error('assigned_to_user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="5">{{ old('description', $opportunity->description) }}</textarea>
            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="mt-3">
    <button type="submit" class="btn btn-primary">{{ $opportunity->exists ? 'Update Opportunity' : 'Create Opportunity' }}</button>
    <a href="{{ route('opportunities.index') }}" class="btn btn-secondary">Cancel</a>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const customerSelect = document.getElementById('customer_id');
        const contactSelect = document.getElementById('contact_id');
        const initialContactId = "{{ old('contact_id', $opportunity->contact_id) }}";

        function loadContacts(customerId) {
            contactSelect.innerHTML = '<option value="">Loading contacts...</option>';
            if (!customerId) {
                contactSelect.innerHTML = '<option value="">Select a Customer first</option>';
                return;
            }

            fetch(`/customers/${customerId}/contacts`)
                .then(response => response.json())
                .then(contacts => {
                    contactSelect.innerHTML = '<option value="">Select a Contact (optional)</option>';
                    contacts.forEach(contact => {
                        const option = document.createElement('option');
                        option.value = contact.id;
                        option.textContent = contact.name;
                        if (contact.id == initialContactId) {
                            option.selected = true;
                        }
                        contactSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching contacts:', error);
                    contactSelect.innerHTML = '<option value="">Error loading contacts</option>';
                });
        }

        // Initial load if a customer is already selected (e.g., on edit or pre-filled create)
        if (customerSelect.value) {
            loadContacts(customerSelect.value);
        }

        customerSelect.addEventListener('change', function () {
            loadContacts(this.value);
        });
    });
</script>
@endpush