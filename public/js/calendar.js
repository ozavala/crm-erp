document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            // Pass start and end dates to the API for filtering
            const params = new URLSearchParams({
                start: fetchInfo.startStr,
                end: fetchInfo.endStr
            });
            fetch(`/api/calendar-events?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    // Add your authentication token here if needed
                    // 'Authorization': 'Bearer ' + YOUR_AUTH_TOKEN
                }
            })
            .then(response => response.json())
            .then(data => {
                successCallback(data.map(event => ({
                    id: event.id,
                    title: event.title,
                    start: event.start,
                    end: event.end,
                    description: event.description,
                    allDay: event.allDay,
                    google_event_id: event.google_event_id // Include Google Event ID
                })));
            })
            .catch(error => {
                console.error('Error fetching events:', error);
                failureCallback(error);
            });
        },
        dateClick: function(info) {
            let title = prompt('Event Title:');
            if (title) {
                fetch('/api/calendar-events', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        // Add your authentication token here if needed
                        // 'Authorization': 'Bearer ' + YOUR_AUTH_TOKEN
                    },
                    body: JSON.stringify({
                        title: title,
                        start: info.dateStr,
                        end: info.dateStr, // For dateClick, end is same as start initially
                        allDay: info.allDay
                    })
                })
                .then(response => response.json())
                .then(event => {
                    calendar.addEvent(event);
                })
                .catch(error => console.error('Error adding event:', error));
            }
        },
        eventClick: function(info) {
            let event = info.event;
            let newTitle = prompt('Edit Event Title:', event.title);
            if (newTitle !== null) { // Check if user clicked Cancel
                if (newTitle === '') {
                    if (confirm('Are you sure you want to delete this event?')) {
                        fetch(`/api/calendar-events/${event.id}`, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                // 'Authorization': 'Bearer ' + YOUR_AUTH_TOKEN
                            }
                        })
                        .then(() => {
                            event.remove();
                        })
                        .catch(error => console.error('Error deleting event:', error));
                    }
                } else {
                    fetch(`/api/calendar-events/${event.id}`, {
                        method: 'PUT',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            // 'Authorization': 'Bearer ' + YOUR_AUTH_TOKEN
                        },
                        body: JSON.stringify({
                            title: newTitle,
                            start: event.startStr,
                            end: event.endStr,
                            description: event.extendedProps.description,
                            allDay: event.allDay
                        })
                    })
                    .then(response => response.json())
                    .then(updatedEvent => {
                        event.setProp('title', updatedEvent.title);
                        // Update other properties if needed
                    })
                    .catch(error => console.error('Error updating event:', error));
                }
            }
        },
        editable: true,
        eventDrop: function(info) {
            let event = info.event;
            fetch(`/api/calendar-events/${event.id}`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    // 'Authorization': 'Bearer ' + YOUR_AUTH_TOKEN
                },
                body: JSON.stringify({
                    start: event.startStr,
                    end: event.endStr,
                    allDay: event.allDay
                })
            })
            .then(response => response.json())
            .then(updatedEvent => {
                console.log('Event updated after drag:', updatedEvent);
            })
            .catch(error => {
                console.error('Error updating event after drag:', error);
                info.revert();
            });
        },
        eventResize: function(info) {
            let event = info.event;
            fetch(`/api/calendar-events/${event.id}`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    // 'Authorization': 'Bearer ' + YOUR_AUTH_TOKEN
                },
                body: JSON.stringify({
                    start: event.startStr,
                    end: event.endStr,
                    allDay: event.allDay
                })
            })
            .then(response => response.json())
            .then(updatedEvent => {
                console.log('Event updated after resize:', updatedEvent);
            })
            .catch(error => {
                console.error('Error updating event after resize:', error);
                info.revert();
            });
        }
    });
    calendar.render();
});