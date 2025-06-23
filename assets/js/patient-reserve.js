// Reservation Page JavaScript

$(document).ready(function () {
    // Initialize calendar
    initializeCalendar();
    
    // Event handlers
    setupEventHandlers();
    
    // Initialize PayPal buttons
    initializePayPal();
});

// Calendar initialization
function initializeCalendar() {
    var date = new Date();
    var d = date.getDate(),
        m = date.getMonth(),
        y = date.getFullYear();
        
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
        },
        selectable: true,
        selectHelper: true,
        eventDidMount: function(info) {
            $(info.el).find('.fc-event-title.fc-sticky').text('60');
        },
        select: function (start, end, allDay) {
            var dateToday = moment(date).format("Y-MM-DD");
            var start = $.fullCalendar.formatDate(start, "Y-MM-DD");
            if (start >= dateToday) {
                $('#date').val(start);
                window.location.replace('reserve.php?date=' + start);
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
                    displayMessage("Updated Successfully");
                }
            });
        },
        eventClick: function (event) {
            $('#checkdate').val(event.start._i);
        }
    });

    calendar();
}

// Setup all event handlers
function setupEventHandlers() {
    // Payment method change handler
    $('#mop').on('change', function() {
        var mop = $('#mop').val();
        if(mop == '1') {
            $('#modalfooter').css('display', 'block');
            $('#paypal-button-container').css('display', 'none');
        } else if(mop == '2') {
            $('#paypal-button-container').css('display', 'block');
            $('#modalfooter').css('display', 'none');
        }
    });

    // Clinic change handler - loads categories
    $('#clinic').on('change', function() {
        var clinic = $('#clinic').val();
        $.ajax({
            url: 'category.php?clinic=' + clinic,
            type: 'get',
            success: function(response) {
                $('#category').empty();
                $("#category").append(response);    
            }
        });
    });

    // Category change handler - loads laboratory/services
    $('#category').on('change', function() {
        var category = $('#category').val();
        var pet = $('#pet').val();
        $.ajax({
            url: 'laboratory.php?category_id=' + category + '&pet_id=' + pet,
            type: 'get',
            success: function(response) {
                $('#laboratory').empty();
                $("#laboratory").append(response);    
            }
        });
    });
}

// Initialize PayPal payment buttons
function initializePayPal() {
    // Get total price from hidden input
    var totalPrice = $('#totalPrice').val();
    
    if (typeof paypal !== 'undefined' && totalPrice) {
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    "purchase_units": [{
                        "amount": {
                            "currency_code": "PHP",
                            "value": parseFloat(totalPrice),
                        },
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    // Get date from URL parameter or hidden input
                    var date = getDateParameter();
                    
                    var requestData = { 
                        date: date,
                        mop: $('#mop').val(),
                    };
                    
                    $.ajax({
                        type: "POST",
                        url: 'payrol-transaction-complete.php',
                        data: requestData,
                        success: function(response) {
                            alert('Payment Success!');
                            location.reload(true);
                        },
                        error: function(xhr, status, error) {
                            alert('Payment processing failed. Please try again.');
                            console.error('Payment error:', error);
                        }
                    });
                });
            },
            onError: function(err) {
                alert('PayPal error occurred. Please try again.');
                console.error('PayPal error:', err);
            }
        }).render('#paypal-button-container');
    }
}

// Helper function to get date parameter from URL
function getDateParameter() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('date') || $('#checkdate1').val() || '';
}

// Custom calendar styling
function applyCalendarStyles() {
    $('<style>')
        .prop('type', 'text/css')
        .html(`
            .fc-title {
                color: white;
            }
        `)
        .appendTo('head');
}