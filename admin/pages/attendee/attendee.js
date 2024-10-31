
document.addEventListener('DOMContentLoaded', function() {
    // Handle item selection.
    document.querySelectorAll('ul li').forEach(function(li) {
        li.addEventListener('click', function() {
            this.classList.toggle('selected');
        });
    });

    // Handle add related.
    document.getElementById('add-related-attendee').addEventListener('click', function(event) {
        event.preventDefault();
        const selectedItems = document.querySelectorAll('#all-attendees li.selected');
        if (selectedItems) {
            const relatedAttendeeList = document.getElementById('related-attendees');
            selectedItems.forEach(item => {
                relatedAttendeeList.appendChild(item);
                item.classList.toggle('selected');
            });
            updateRelatedAttendeesInput();
        }
    });

    // Handle remove related.
    document.getElementById('remove-related-attendee').addEventListener('click', function(event) {
        event.preventDefault();
        const selectedItems = document.querySelectorAll('#related-attendees li.selected');
        if (selectedItems) {
            selectedItems.forEach(item => {
                document.getElementById('all-attendees').appendChild(item);
                item.classList.toggle('selected');
            });
            updateRelatedAttendeesInput();
        }
    });

    function updateRelatedAttendeesInput() {
        const relatedAttendeeItems = document.querySelectorAll('#related-attendees li');
        let selectedIds = [];
        relatedAttendeeItems.forEach(relatedAttendeeItem => {
            selectedIds.push(relatedAttendeeItem.getAttribute('data-id'));
        });
        document.getElementById('related_attendee_ids').value = selectedIds.join(',');
    }

    updateRelatedAttendeesInput();
});