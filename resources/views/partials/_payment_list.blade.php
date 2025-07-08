<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">{{ __('partials.Payment History') }}</h5>
    </div>
    <div class="card-body">
        @if(!isset($payments) || $payments->isEmpty())
            <p>{{ __('partials.No payments have been recorded for this document.') }}</p>
        @else
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('partials.Date') }}</th>
                            <th>{{ __('partials.Amount') }}</th>
                            <th>{{ __('partials.Method') }}</th>
                            <th>{{ __('partials.Recorded By') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                <td>${{ number_format($payment->amount, 2) }}</td>
                                <td>{{ $payment->payment_method }}</td>
                                <td>{{ $payment->createdBy->full_name ?? 'N/A' }}</td>
                                <td class="text-end">
                                    <form action="{{ route('payments.destroy', $payment->payment_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this payment? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="{{ __('partials.Delete Payment') }}"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
