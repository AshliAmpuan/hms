$(document).ready(function () {
    var date = new Date();
    var d = date.getDate(),
        m = date.getMonth(),
        y = date.getFullYear();

    var todayDate = moment().format('YYYY-MM-DD');

    const calendar = () => $('#calendar').fullCalendar({
        editable: true,
        events: "fetch-event.php",
        displayEventTime: true, // Show time for better patient scheduling
        eventRender: function (event, element, view) {
            if (event.allDay === 'true') {
                event.allDay = true;
            } else {
                event.allDay = false;
            }
            
            // Apply color coding based on status
            if (event.color) {
                element.css({
                    'background-color': event.color,
                    'border-color': event.color
                });
            }
            
            var eventDate = event.start ? event.start.format('YYYY-MM-DD') : '';
            if(eventDate === todayDate) {
                element.css({
                    'font-weight': '700',
                    'box-shadow': '0 4px 12px rgba(0,0,0,0.3)',
                    'border-radius': '8px',
                    'border': '2px solid #fff'
                });
            }
            
            // Add tooltip with additional information
            element.attr('title', 
                'Laboratory: ' + event.laboratory_name + 
                '\nTime: ' + (event.time || 'Not set') +
                '\nStatus: ' + (event.status_text || 'Unknown')
            );
        },
        selectable: true,
        selectHelper: true,
        select: function (start, end, allDay) {
            var dateToday = moment(date).format("Y-MM-DD");
            var startStr = $.fullCalendar.formatDate(start, "Y-MM-DD");
            if(startStr >= dateToday) {
                window.location.replace('reserve.php?date=' + startStr);
            }
            calendar.fullCalendar('unselect');
        },
        editable: false, // Disable editing for patient view
        eventDrop: function (event, delta) {
            var start = $.fullCalendar.formatDate(event.start, "Y-MM-DD HH:mm:ss");
            var end = $.fullCalendar.formatDate(event.end, "Y-MM-DD HH:mm:ss");
            $.ajax({
                url: 'edit-event.php',
                data: 'title=' + event.title + '&start=' + start + '&end=' + end + '&id=' + event.id,
                type: "POST",
                success: function (response) {
                    // Show success message
                    alert("Reservation updated successfully!");
                }
            });
        },
        eventClick: function (event) {
            // Show detailed information about the reservation
            var statusText = event.status_text || 'Unknown';
            var timeText = event.time || 'Not specified';
            
            var message = 'Reservation Details:\n\n' +
                         'Laboratory: ' + event.laboratory_name + '\n' +
                         'Date: ' + event.start.format('YYYY-MM-DD') + '\n' +
                         'Time: ' + timeText + '\n' +
                         'Status: ' + statusText;
            
            alert(message);
            
            // Optional: Redirect to reservation details page
            // window.location.href = 'reservation-details.php?id=' + event.reservation_id;
        }
    });

    calendar();
});