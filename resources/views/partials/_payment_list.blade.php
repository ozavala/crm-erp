<div class="card">
    <div class="card-header">
        <h5>{{ __('payments.Payments') }}</h5>
    </div>
    <div class="card-body">
        @if(!isset($payments) || $payments->isEmpty())
            <p>{{ __('partials.No payments have been recorded for this document.') }}</p>
        @else
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>{{ __('payments.Payment Date') }}</th>
                            <th>{{ __('payments.Amount') }}</th>
                            <th>{{ __('payments.Payment Method') }}</th>
                            <th>{{ __('payments.Recorded By') }}</th>
                            <th>{{ __('payments.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->payment_date->format('Y-m-d') }}</td>
                                <td>${{ number_format($payment->amount, 2) }}</td>
                                <td>{{ __('payments.' . $payment->payment_method) }}</td>
                                <td>{{ $payment->createdBy->full_name ?? __('N/A') }}</td>
                                <td class="text-end">
                                    <form action="{{ route('payments.destroy', $payment) }}" method="POST" style="display:inline-block;" onsubmit="return confirm(__('payments.Are you sure you want to delete this payment?'));">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">{{ __('payments.Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">{{ __('payments.No payments found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
