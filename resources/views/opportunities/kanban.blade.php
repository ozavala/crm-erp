@extends('layouts.app')

@section('title', 'Opportunities Kanban Board')

@push('styles')
<style>
    .kanban-board {
        display: flex;
        overflow-x: auto;
        padding-bottom: 1rem;
        gap: 1rem;
    }
    .kanban-column {
        flex: 0 0 320px; /* A bit wider */
        background-color: #f4f5f7;
        border-radius: 5px;
    }
    .kanban-column-header {
        font-weight: bold;
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
    }
    .kanban-cards {
        min-height: 100px; /* For dropping on empty columns */
        padding: 8px;
    }
    .kanban-card {
        background-color: #ffffff;
        border-radius: 3px;
        box-shadow: 0 1px 1px rgba(9,30,66,.15);
        padding: 1rem;
        margin-bottom: 8px;
        cursor: grab;
        border-left: 4px solid transparent;
    }
    .kanban-card:active {
        cursor: grabbing;
    }
    .kanban-card.is-overdue {
        border-left-color: #dc3545; /* Alert for overdue items */
    }
    .kanban-card-title {
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .kanban-card-meta {
        font-size: 0.85rem;
        color: #6c757d;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Opportunities - Kanban View</h1>
        <a href="{{ route('opportunities.index') }}" class="btn btn-outline-primary">List View</a>
    </div>

    <div class="kanban-board">
        @foreach($stages as $stageKey => $stageName)
            <div class="kanban-column">
                <div class="kanban-column-header">{{ $stageName }} ({{ $kanbanData[$stageKey]->count() }})</div>
                <div class="kanban-cards" data-stage="{{ $stageKey }}" id="stage-{{ $stageKey }}">
                    @foreach($kanbanData[$stageKey] as $opportunity)
                        @include('opportunities._kanban_card', ['opportunity' => $opportunity])
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.kanban-cards').forEach(column => {
        new Sortable(column, {
            group: 'opportunities',
            animation: 150,
            ghostClass: 'bg-info',
            onEnd: function (evt) {
                const opportunityId = evt.item.dataset.id;
                const newStage = evt.to.dataset.stage;

                fetch(`/opportunities/${opportunityId}/stage`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ stage: newStage })
                })
                .then(response => response.json())
                .then(data => console.log('Update successful:', data))
                .catch(error => console.error('Update failed:', error));
            },
        });
    });
});
</script>
@endpush