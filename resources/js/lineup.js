// Alpine.js lineup board interactions (lineup-page only - loaded as its own bundle entry)
document.addEventListener('alpine:init', () => {
    window.Alpine.data('lineupInteractions', () => ({
        openPicker: null,
        dragPlayerId: null,
        openPlayerPicker(slotIndex) {
            this.openPicker = this.openPicker === slotIndex ? null : slotIndex;
        },
        closePicker() {
            this.openPicker = null;
        },
        onDragStart(event, playerId) {
            this.dragPlayerId = playerId;
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', playerId);
        },
        onDrop(event, slotIndex) {
            event.preventDefault();
            const playerId = event.dataTransfer.getData('text/plain') || this.dragPlayerId;
            if (playerId) {
                this.$wire.assignToPosition(parseInt(playerId), slotIndex);
            }
            this.dragPlayerId = null;
            this.openPicker = null;
        },
    }));
});
