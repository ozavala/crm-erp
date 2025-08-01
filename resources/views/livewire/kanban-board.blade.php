<div class="kanban-container">
    @if ($wipLimitReached)
        <div class="wip-limit-warning">
            WIP Limit Reached! Cannot add more cards to this column.
        </div>
    @endif

    <div class="kanban-search">
        <input type="text" wire:model.live="search" placeholder="Search cards..." class="kanban-search-input">
    </div>

    <div wire:sortable="onCardDrop" style="display: flex; gap: 20px;">
        @foreach ($columns as $column)
            <div wire:key="{{ $column['id'] }}" class="kanban-column @if(count($column['cards']) >= $column['wipLimit']) wip-limit-exceeded @endif">
                <div class="kanban-column-title">{{ $column['title'] }} ({{ count($column['cards']) }}/{{ $column['wipLimit'] }})</div>
                <div wire:sortable-group.item="{{ $column['id'] }}" class="kanban-cards">
                    @foreach ($column['cards'] as $card)
                        <div wire:key="{{ $card['id'] }}" wire:sortable.item="{{ $card['id'] }}" class="kanban-card {{ $card['category'] ?? '' }}">
                            <div class="card-header">
                                <i class="fa {{ $card['icon'] }}"></i>
                                <span class="card-title">{{ $card['title'] }}</span>
                                @if ($card['notifications'] > 0)
                                    <span class="card-notifications">{{ $card['notifications'] }}</span>
                                @endif
                            </div>
                            <div class="card-body">
                                <p class="card-description">{{ $card['description'] }}</p>
                            </div>
                            <div class="card-footer">
                                <a href="{{ $card['shortcut_url'] }}" class="shortcut-button">Go</a>
                                <button class="quick-action-button edit-button" wire:click="editCard('{{ $card['id'] }}')">Edit</button>
                                <button class="quick-action-button view-button" wire:click="viewCard('{{ $card['id'] }}')">View</button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
