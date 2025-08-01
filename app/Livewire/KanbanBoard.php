<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Route;

class KanbanBoard extends Component
{
    public $allColumns;
    public $columns;
    public $search = '';
    public $wipLimitReached = false;

    // Mapping of card IDs to their corresponding resource routes
    private $routeMap = [
        'users' => 'crm-users',
        'roles' => 'user-roles',
        'tasks' => 'tasks',
        // Add other mappings as needed
    ];

    public function mount()
    {
        $this->allColumns = [
            [
                'id' => 'administration',
                'title' => 'Administration',
                'wipLimit' => 3, // Example WIP limit
                'cards' => [
                    ['id' => 'users', 'title' => 'Users', 'icon' => 'fa-users', 'description' => 'Manage system users', 'notifications' => 5, 'shortcut_url' => '#', 'category' => 'critical', 'record_id' => 1],
                    ['id' => 'roles', 'title' => 'Roles and Perm.', 'icon' => 'fa-user-tag', 'description' => 'Define user roles and permissions', 'notifications' => 0, 'shortcut_url' => '#', 'category' => 'settings', 'record_id' => 1],
                    ['id' => 'audit', 'title' => 'Audit', 'icon' => 'fa-clipboard-list', 'description' => 'View system audit logs', 'notifications' => 0, 'shortcut_url' => '#', 'category' => 'frequent', 'record_id' => null],
                ]
            ],
            [
                'id' => 'configuration',
                'title' => 'Configuration',
                'wipLimit' => 2, // Example WIP limit
                'cards' => [
                    ['id' => 'preferences', 'title' => 'Preferences', 'icon' => 'fa-sliders-h', 'description' => 'Configure application settings', 'notifications' => 0, 'shortcut_url' => '#', 'category' => 'settings', 'record_id' => null],
                    ['id' => 'integrations', 'title' => 'Integrations', 'icon' => 'fa-puzzle-piece', 'description' => 'Manage external integrations', 'notifications' => 2, 'shortcut_url' => '#', 'category' => 'frequent', 'record_id' => null],
                    ['id' => 'api-keys', 'title' => 'API Keys', 'icon' => 'fa-key', 'description' => 'Manage API access keys', 'notifications' => 0, 'shortcut_url' => '#', 'category' => 'critical', 'record_id' => null],
                ]
            ],
            [
                'id' => 'functions',
                'title' => 'Functions',
                'wipLimit' => 4, // Example WIP limit
                'cards' => [
                    ['id' => 'tasks', 'title' => 'Tasks', 'icon' => 'fa-tasks', 'description' => 'Manage your tasks', 'notifications' => 10, 'shortcut_url' => '#', 'category' => 'frequent', 'record_id' => 1],
                    ['id' => 'calendar', 'title' => 'Calendar', 'icon' => 'fa-calendar-alt', 'description' => 'View and manage calendar events', 'notifications' => 3, 'shortcut_url' => '#', 'category' => 'frequent', 'record_id' => null],
                    ['id' => 'information', 'title' => 'Information', 'icon' => 'fa-info-circle', 'description' => 'Access system information', 'notifications' => 0, 'shortcut_url' => '#', 'category' => 'settings', 'record_id' => null],
                ]
            ],
        ];
        $this->applySearch();
    }

    public function updatedSearch()
    {
        $this->applySearch();
    }

    private function applySearch()
    {
        $searchTerm = strtolower($this->search);
        $filteredColumns = [];

        foreach ($this->allColumns as $column) {
            $filteredCards = [];
            foreach ($column['cards'] as $card) {
                if (empty($searchTerm) || 
                    str_contains(strtolower($card['title']), $searchTerm) ||
                    str_contains(strtolower($card['description']), $searchTerm))
                {
                    $filteredCards[] = $card;
                }
            }
            if (!empty($filteredCards) || empty($searchTerm)) {
                $newColumn = $column;
                $newColumn['cards'] = $filteredCards;
                $filteredColumns[] = $newColumn;
            }
        }
        $this->columns = $filteredColumns;
    }

    public function onCardDrop($cardId, $newColumnId, $newPosition)
    {
        $this->wipLimitReached = false; // Reset warning

        // Find the card and its original column in allColumns
        $card = null;
        $originalColumnIndex = null;
        $newColumnIndex = null;

        foreach ($this->allColumns as $colIndex => &$column) {
            if ($column['id'] == $newColumnId) {
                $newColumnIndex = $colIndex;
            }
            foreach ($column['cards'] as $key => $c) {
                if ($c['id'] == $cardId) {
                    $card = $c;
                    $originalColumnIndex = $colIndex;
                    break 2;
                }
            }
        }

        // Check WIP limit before moving
        if ($newColumnIndex !== null && isset($this->allColumns[$newColumnIndex]['wipLimit'])) {
            $currentCardCount = count($this->allColumns[$newColumnIndex]['cards']);
            if ($currentCardCount >= $this->allColumns[$newColumnIndex]['wipLimit']) {
                $this->wipLimitReached = true;
                $this->dispatch('wip-limit-reached', columnId: $newColumnId); // Dispatch event for frontend feedback
                $this->applySearch(); // Re-render to revert the drag visually
                return; // Prevent the card from being moved
            }
        }

        // Perform the move in allColumns
        if ($card && $originalColumnIndex !== null && $newColumnIndex !== null) {
            // Remove card from original column
            foreach ($this->allColumns[$originalColumnIndex]['cards'] as $key => $c) {
                if ($c['id'] == $cardId) {
                    array_splice($this->allColumns[$originalColumnIndex]['cards'], $key, 1);
                    break;
                }
            }
            // Add card to the new column at the new position
            array_splice($this->allColumns[$newColumnIndex]['cards'], $newPosition, 0, [$card]);
        }

        // Reapply search to update the displayed columns
        $this->applySearch();
    }

    public function editCard($cardId)
    {
        $card = $this->findCardById($cardId);
        if (!$card) {
            return;
        }

        $resourceName = $this->routeMap[$cardId] ?? null;

        if ($resourceName && $card['record_id'] !== null) {
            return redirect()->route($resourceName . '.edit', $card['record_id']);
        } elseif ($cardId === 'preferences') {
            return redirect()->route('settings.edit');
        } else {
            // Fallback for cards without a specific edit route or record_id
            session()->flash('message', 'Edit functionality not available for this card.');
            return redirect()->route('dashboard'); // Or a more appropriate fallback
        }
    }

    public function viewCard($cardId)
    {
        $card = $this->findCardById($cardId);
        if (!$card) {
            return;
        }

        if ($cardId === 'calendar') {
            return redirect()->route('calendar');
        }

        $resourceName = $this->routeMap[$cardId] ?? null;

        if ($resourceName && $card['record_id'] !== null) {
            return redirect()->route($resourceName . '.show', $card['record_id']);
        } else {
            // Fallback for cards without a specific view route or record_id
            session()->flash('message', 'View functionality not available for this card.');
            return redirect()->route('dashboard'); // Or a more appropriate fallback
        }
    }

    private function findCardById($cardId)
    {
        foreach ($this->allColumns as $column) {
            foreach ($column['cards'] as $card) {
                if ($card['id'] === $cardId) {
                    return $card;
                }
            }
        }
        return null;
    }

    public function render()
    {
        return view('livewire.kanban-board');
    }
}