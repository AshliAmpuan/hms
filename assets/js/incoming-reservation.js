/**
 * Incoming Reservation Page JavaScript
 * Custom JavaScript functionality for managing veterinary reservations
 */

/**
 * Assign doctor to a reservation
 * @param {number} reservationId - The reservation ID
 * @param {number} categoryId - The category ID
 */
function assignDoctor(reservationId, categoryId) {
  $('#reservation_id').val(reservationId);
  $('#category_id').val(categoryId);
  $('#assignDoctorModal .modal-title').text('Assign Doctor to Reservation');
  
  // Load all doctors
  loadAllDoctors();
}

/**
 * Reassign doctor to a reservation
 * @param {number} reservationId - The reservation ID
 * @param {number} categoryId - The category ID
 */
function reassignDoctor(reservationId, categoryId) {
  $('#reservation_id').val(reservationId);
  $('#category_id').val(categoryId);
  $('#assignDoctorModal .modal-title').text('Reassign Doctor to Reservation');
  
  // Load all doctors
  loadAllDoctors();
}

/**
 * Load all available doctors into the select dropdown
 */
function loadAllDoctors() {
  $.post('get_doctors.php', { load_all: true }, function(data) {
    if(data.success && data.doctors.length > 0) {
      let options = '<option value="" selected disabled>Choose Veterinarian...</option>';
      data.doctors.forEach(doctor => {
        // Show only doctor's full name
        options += `<option value="${doctor.id}">${doctor.fullname}</option>`;
      });
      $('#doctor_select').html(options);
    } else {
      $('#doctor_select').html('<option value="" disabled>No doctors found</option>');
    }
  }, 'json').fail(function() {
    $('#doctor_select').html('<option value="" disabled>Error loading doctors</option>');
  });
}

/**
 * Approve a reservation
 * @param {number} reservationId - The reservation ID
 */
function approveReservation(reservationId) {
  if(confirm('Are you sure you want to approve this reservation?')) {
    // Create a form and submit
    let form = $('<form method="POST"></form>');
    form.append('<input type="hidden" name="approve_reservation" value="1">');
    form.append('<input type="hidden" name="reservation_id" value="' + reservationId + '">');
    $('body').append(form);
    form.submit();
  }
}

// Document ready functions
$(document).ready(function() {
  // Initialize DataTables for both tables
  $('#pending-table').DataTable({
    "responsive": true,
    "autoWidth": false,
  });
  
  $('#cancelled-table').DataTable({
    "responsive": true,
    "autoWidth": false,
  });
  
  /**
   * Filter functionality for tabs
   */
  
  // Show all appointments
  $('#all-tab').click(function() {
    $('tbody tr').show();
  });
  
  // Show only pending appointments
  $('#pending-tab').click(function() {
    $('tbody tr').hide();
    $('.status-pending').show();
  });
  
  // Show only cancelled appointments
  $('#cancelled-tab').click(function() {
    $('tbody tr').hide();
    $('.status-cancelled').show();
  });
  
  /**
   * Auto-refresh functionality
   * Refreshes the page every 30 seconds to show latest data
   */
  setInterval(function() {
    location.reload();
  }, 30000);
  
  /**
   * Initialize tooltips if needed
   */
  $('[data-toggle="tooltip"]').tooltip();
  
  /**
   * Handle modal close event
   */
  $('#assignDoctorModal').on('hidden.bs.modal', function () {
    $('#assignDoctorForm')[0].reset();
    $('#doctor_select').html('<option value="" selected disabled>Choose Veterinarian...</option>');
  });
});