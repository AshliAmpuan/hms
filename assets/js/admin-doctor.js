$(document).ready(function() {
    $('#js-example-basic-single').select2();
});

$('#clinic').on('change', function() {
    var clinic = $('#clinic').val();

    $.ajax({
        url: 'doctor_lab.php?clinic=' + clinic,
        type: 'get',
        success: function(response) {
            $('#laboratory').empty();
            $("#laboratory").append(response);
        }
    });
});