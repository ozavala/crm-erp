@php
    $isOverdue = $opportunity->expected_close_date && $opportunity->expected_close_date->isPast() && !in_array($opportunity->stage, ['Closed Won', 'Closed Lost']);
@endphp

<div class="kanban-card {{ $isOverdue ? 'is-overdue' : '' }}" data-id="{{ $opportunity->opportunity_id }}">
    <div class="kanban-card-title">
        <a href="{{ route('opportunities.show', $opportunity) }}">{{ $opportunity->name }}</a>
    </div>
    <div class="kanban-card-meta">
        <div>{{ $opportunity->customer->company_name ?: $opportunity->customer->full_name }}</div>
        <div><strong>Value:</strong> ${{ number_format($opportunity->amount, 2) }}</div>
        @if($opportunity->expected_close_date)
            <div>
                <strong>Close:</strong>
                <span class="{{ $isOverdue ? 'text-danger fw-bold' : '' }}">
                    {{ $opportunity->expected_close_date->format('M d, Y') }}
                </span>
            </div>
        @endif
        @if($opportunity->assignedTo)
            <div>Assigned to: {{ $opportunity->assignedTo->full_name }}</div>
        @endif
    </div>
</div>