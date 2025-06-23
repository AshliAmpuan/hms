$(document).ready(function () {
    var date = new Date();
    var d = date.getDate(),
        m = date.getMonth(),
        y = date.getFullYear();

    var todayDate = moment().format('YYYY-MM-DD');

    const calendar = () => $('#calendar').fullCalendar({
        editable: true,
        events: "fetch-event.php",
        displayEventTime: false,
        eventRender: function (event, element, view) {
            if (event.allDay === 'true') {
                event.allDay = true;
            } else {
                event.allDay = false;
            }
            var eventDate = event.start ? event.start.format('YYYY-MM-DD') : '';
            if(eventDate === todayDate) {
                element.css({
                    'background': '#28a745',
                    'color': '#fff',
                    'font-weight': '600',
                    'box-shadow': '0 4px 12px rgba(40,167,69,0.4)',
                    'border-radius': '8px'
                });
            }
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
        editable: true,
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
            // Event click handler - you can add custom logic here if needed
        }
    });

    calendar();
});