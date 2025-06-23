/**
 * Vaccination Records Management JavaScript
 * Handles doctor-side vaccination records functionality
 */

$(document).ready(function() {
    // Initialize DataTable
    $('#vaccination-table').DataTable({
        "pageLength": 25,
        "ordering": true,
        "info": true,
        "searching": true,
        "responsive": true,
        "language": {
            "emptyTable": "No vaccination records found matching your criteria",
            "info": "Showing _START_ to _END_ of _TOTAL_ vaccination records",
            "infoEmpty": "Showing 0 to 0 of 0 vaccination records",
            "infoFiltered": "(filtered from _MAX_ total vaccination records)",
            "lengthMenu": "Show _MENU_ records per page",
            "search": "Search records:",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        },
        "order": [[0, "desc"]], // Sort by date descending
        "columnDefs": [
            {
                "targets": [7], // Actions column
                "orderable": false,
                "searchable": false
            }
        ]
    });

    // Initialize Select2 for better dropdown experience
    $('.select2').select2({
        placeholder: function() {
            return $(this).data('placeholder');
        },
        allowClear: true,
        width: '100%'
    });

    // Patient selection change handler - load pets for selected patient
    $('#patient_filter').on('change', function() {
        const patientId = $(this).val();
        const petSelect = $('#pet_filter');
        
        // Clear pet dropdown
        petSelect.empty().append('<option value="">All Pets</option>');
        
        if (patientId) {
            // Load pets for selected patient
            loadPetsForPatient(patientId);
        }
    });

    // Auto-submit form when filters change
    $('#patient_filter, #pet_filter, #date_range').on('change', function() {
        $('#filterForm').submit();
    });

    // Save vaccination record handler
    $('#saveVaccinationBtn').on('click', function() {
        saveVaccinationRecord();
    });
});

/**
 * Load pets for a specific patient
 */
function loadPetsForPatient(patientId) {
    if (!patientId || patientId <= 0) {
        return;
    }

    const petSelect = $('#pet_filter');
    
    // Show loading state
    petSelect.prop('disabled', true);
    petSelect.append('<option>Loading pets...</option>');

    $.ajax({
        url: 'get_patient_pets.php',
        type: 'POST',
        data: { patient_id: patientId },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            petSelect.prop('disabled', false);
            petSelect.empty().append('<option value="">All Pets</option>');
            
            if (response && response.success && response.pets) {
                response.pets.forEach(function(pet) {
                    const petInfo = pet.pet_name + (pet.breed ? ' (' + pet.breed + ')' : '');
                    petSelect.append('<option value="' + pet.id + '">' + petInfo + '</option>');
                });
            }
        },
        error: function(xhr, status, error) {
            petSelect.prop('disabled', false);
            petSelect.empty().append('<option value="">All Pets</option>');
            console.error('Error loading pets:', error);
        }
    });
}

/**
 * View medical notes for a vaccination record
 */
function viewMedicalNotes(recordId) {
    if (!recordId || recordId <= 0) {
        showAlert('Invalid record ID', 'error');
        return;
    }
    
    $('#medicalNotesModal').modal('show');
    
    $('#medicalNotesModalBody').html(`
        <div class="text-center">
            <div class="spinner-border" role="status"></div>
            <p class="mt-2">Loading medical notes...</p>
        </div>
    `);
    
    $.ajax({
        url: 'get_medical_notes.php',
        type: 'POST',
        data: { record_id: recordId },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            if (response && response.success) {
                const record = response.record;
                let notesHtml = `
                    <div class="card">
                        <div class="card-body">
                            <h6><strong>Visit Details:</strong></h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Date:</strong> ${formatDate(record.tdate)}</p>
                                    <p><strong>Patient:</strong> ${record.patient_name || 'Unknown'}</p>
                                    <p><strong>Pet:</strong> ${record.pet_name || 'Unknown'}</p>
                                    <p><strong>Species:</strong> ${record.species || 'Not specified'}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Vaccination Notes:</strong> ${record.vaccination_notes || 'No vaccination notes'}</p>
                                    <p><strong>Weight:</strong> ${record.weight_lbs ? record.weight_lbs + ' kg' : 'Not recorded'}</p>
                                    <p><strong>Temperature:</strong> ${record.temperature_celsius ? record.temperature_celsius + '°C' : 'Not recorded'}</p>
                                </div>
                            </div>
                            <hr>
                            <h6><strong>Medical Notes:</strong></h6>
                            <div class="border p-3 bg-light rounded">
                                ${record.results ? record.results.replace(/\n/g, '<br>') : 'No medical notes available.'}
                            </div>
                        </div>
                    </div>
                `;
                $('#medicalNotesModalBody').html(notesHtml);
            } else {
                $('#medicalNotesModalBody').html('<div class="alert alert-danger">Error loading medical notes: ' + (response.message || 'Unknown error') + '</div>');
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'Failed to load medical notes.';
            if (status === 'timeout') {
                errorMessage = 'Request timed out. Please try again.';
            } else if (xhr.status === 404) {
                errorMessage = 'Medical notes service not found.';
            }
            $('#medicalNotesModalBody').html('<div class="alert alert-danger">' + errorMessage + '</div>');
        }
    });
}

/**
 * Edit vaccination record
 */
function editVaccinationRecord(recordId) {
    if (!recordId || recordId <= 0) {
        showAlert('Invalid record ID', 'error');
        return;
    }
    
    $('#editVaccinationModal').modal('show');
    
    $('#editVaccinationModalBody').html(`
        <div class="text-center">
            <div class="spinner-border" role="status"></div>
            <p class="mt-2">Loading vaccination record...</p>
        </div>
    `);
    
    $.ajax({
        url: 'get_vaccination_record.php',
        type: 'POST',
        data: { record_id: recordId },
        dataType: 'json',
        timeout: 10000,
        success: function(response) {
            if (response && response.success) {
                const record = response.record;
                let editHtml = `
                    <form id="editVaccinationForm">
                        <input type="hidden" id="edit_record_id" value="${record.id}">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6><strong>Patient & Pet Information:</strong></h6>
                                <p><strong>Patient:</strong> ${record.patient_name || 'Unknown'}</p>
                                <p><strong>Pet:</strong> ${record.pet_name || 'Unknown'} (${record.species || 'Unknown species'})</p>
                                <p><strong>Date:</strong> ${formatDate(record.tdate)}</p>
                            </div>
                            <div class="col-md-6">
                                <h6><strong>Health Metrics:</strong></h6>
                                <div class="form-group">
                                    <label for="edit_weight">Weight (kg):</label>
                                    <input type="number" class="form-control" id="edit_weight" 
                                           value="${record.weight_lbs || ''}" step="0.1" min="0">
                                </div>
                                <div class="form-group">
                                    <label for="edit_temperature">Temperature (°C):</label>
                                    <input type="number" class="form-control" id="edit_temperature" 
                                           value="${record.temperature_celsius || ''}" step="0.1" min="30" max="50">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_vaccination_notes">Vaccination Notes:</label>
                            <textarea class="form-control" id="edit_vaccination_notes" rows="3" 
                                      placeholder="Enter vaccination details, vaccine type, dosage, etc.">${record.vaccination_notes || ''}</textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_medical_notes">Medical Notes:</label>
                            <textarea class="form-control" id="edit_medical_notes" rows="4" 
                                      placeholder="Enter detailed medical observations and notes">${record.results || ''}</textarea>
                        </div>
                    </form>
                `;
                $('#editVaccinationModalBody').html(editHtml);
            } else {
                $('#editVaccinationModalBody').html('<div class="alert alert-danger">Error loading record: ' + (response.message || 'Unknown error') + '</div>');
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'Failed to load vaccination record.';
            if (status === 'timeout') {
                errorMessage = 'Request timed out. Please try again.';
            }
            $('#editVaccinationModalBody').html('<div class="alert alert-danger">' + errorMessage + '</div>');
        }
    });
}

/**
 * Save vaccination record changes
 */
function saveVaccinationRecord() {
    const recordId = $('#edit_record_id').val();
    const weight = $('#edit_weight').val();
    const temperature = $('#edit_temperature').val();
    const vaccinationNotes = $('#edit_vaccination_notes').val();
    const medicalNotes = $('#edit_medical_notes').val();
    
    if (!recordId) {
        showAlert('Invalid record ID', 'error');
        return;
    }
    
    // Disable save button and show loading
    const saveBtn = $('#saveVaccinationBtn');
    const originalText = saveBtn.html();
    saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
    
    $.ajax({
        url: 'update_vaccination_record.php',
        type: 'POST',
        data: {
            record_id: recordId,
            weight_lbs: weight,
            temperature_celsius: temperature,
            vaccination_notes: vaccinationNotes,
            medical_notes: medicalNotes
        },
        dataType: 'json',
        timeout: 15000,
        success: function(response) {
            saveBtn.prop('disabled', false).html(originalText);
            
            if (response && response.success) {
                showAlert('Vaccination record updated successfully!', 'success');
                $('#editVaccinationModal').modal('hide');
                // Refresh the page to show updated data
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            } else {
                showAlert('Error updating record: ' + (response.message || 'Unknown error'), 'error');
            }
        },
        error: function(xhr, status, error) {
            saveBtn.prop('disabled', false).html(originalText);
            let errorMessage = 'Failed to update vaccination record.';
            if (status === 'timeout') {
                errorMessage = 'Request timed out. Please try again.';
            }
            showAlert(errorMessage, 'error');
        }
    });
}

/**
 * Print vaccination record
 */
function printVaccinationRecord(recordId) {
    if (!recordId || recordId <= 0) {
        showAlert('Invalid record ID', 'error');
        return;
    }
    
    // Open print page in new window
    const printUrl = 'print_vaccination_record.php?record_id=' + recordId;
    const printWindow = window.open(printUrl, '_blank', 'width=800,height=600,scrollbars=yes');
    
    if (!printWindow) {
        showAlert('Please allow popups to print vaccination records', 'warning');
    }
}

/**
 * Show alert message
 */
function showAlert(message, type = 'info') {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    
    const alertHtml = `
        <div class="alert ${alertClass[type]} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;
    
    // Remove existing alerts
    $('.alert').remove();
    
    // Add new alert at the top of the main content
    $('.main-content .section').prepend(alertHtml);
    
    // Auto-hide success alerts after 5 seconds
    if (type === 'success') {
        setTimeout(function() {
            $('.alert-success').fadeOut();
        }, 5000);
    }
}

/**
 * Format date for display
 */
function formatDate(dateString) {
    if (!dateString) return 'Not specified';
    
    const date = new Date(dateString);
    const options = { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    };
    
    return date.toLocaleDateString('en-US', options);
}

/**
 * Export vaccination records to CSV
 */
function exportVaccinationRecords() {
    // Get current filter values
    const patientId = $('#patient_filter').val();
    const petId = $('#pet_filter').val();
    const dateRange = $('#date_range').val();
    
    // Build export URL with filters
    let exportUrl = 'export_vaccination_records.php?format=csv';
    
    if (patientId) exportUrl += '&patient_id=' + patientId;
    if (petId) exportUrl += '&pet_id=' + petId;
    if (dateRange) exportUrl += '&date_range=' + dateRange;
    
    // Trigger download
    window.location.href = exportUrl;
}

/**
 * Generate vaccination report
 */
function generateVaccinationReport() {
    // Get current filter values
    const patientId = $('#patient_filter').val();
    const petId = $('#pet_filter').val();
    const dateRange = $('#date_range').val();
    
    // Build report URL with filters
    let reportUrl = 'vaccination_report.php?';
    
    const params = [];
    if (patientId) params.push('patient_id=' + patientId);
    if (petId) params.push('pet_id=' + petId);
    if (dateRange) params.push('date_range=' + dateRange);
    
    reportUrl += params.join('&');
    
    // Open report in new window
    const reportWindow = window.open(reportUrl, '_blank', 'width=1000,height=700,scrollbars=yes');
    
    if (!reportWindow) {
        showAlert('Please allow popups to generate reports', 'warning');
    }
}