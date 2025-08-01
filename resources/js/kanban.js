document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.removed', ({ el, component }) => {
        if (el.classList.contains('kanban-card')) {
            // Optional: Add any cleanup for removed cards
        }
    });

    Livewire.hook('morph.added', ({ el, component }) => {
        if (el.classList.contains('kanban-card')) {
            // Optional: Add any initialization for added cards
        }
    });
});