@extends('layouts.app')

@title('Invoice Details')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12"> 
            <div class="card">
                <div class="card-body">
                    <h2 class="card-title">Invoice Details:  {{ $invoice->subject }}</h2>
                    <p class="card-text">Invoice Number: {{ $invoice->invoice_number }}</p>
                    <p class="card-text">Customer: {{ $invoice->customer->full_name }}</p>
                    <p class="card-text">Total Amount: {{ $invoice->total_amount }}</p>
                    <p class="card-text">Status: {{ $invoice->status }}</p>
                    <p class="card-text">Due Date: {{ $invoice->due_date }}</p> 
                    <p class="card-text">Created At: {{ $invoice->created_at }}</p>
                    <p class="card-text">Updated At: {{ $invoice->updated_at }}</p>
                    <a href="{{ route('invoices.index') }}" class="btn btn-primary">Back to Invoices</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    // Add any custom scripts here if needed
</script>
@endsection