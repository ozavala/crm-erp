<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="customer_id" class="form-label">Customer/Company</label>
            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                <option value="">Select a Customer</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->customer_id }}" {{ old('customer_id', $selectedCustomerId) == $customer->customer_id ? 'selected' : '' }}>
                        {{ $customer->company_name ?: $customer->full_name }}
                    </option>
                @endforeach
            </select>
            @error('customer_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $contact->first_name) }}" required>
            @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $contact->last_name) }}" required>
            @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="col-md-6">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $contact->title) }}">
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $contact->email) }}">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Phone</label>
            <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $contact->phone) }}">
            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="mt-3">
    <button type="submit" class="btn btn-primary">{{ $contact->exists ? 'Update Contact' : 'Create Contact' }}</button>
    <a href="{{ $contact->exists ? route('customers.show', $contact->customer_id) : (request()->has('customer_id') ? route('customers.show', request('customer_id')) : route('contacts.index')) }}" class="btn btn-secondary">Cancel</a>
</div>