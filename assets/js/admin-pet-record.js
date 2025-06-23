$(document).ready(function() {
  // Initialize DataTable
  $('#records-table').DataTable({
    "order": [[0, "desc"]] // Sort by vaccination date descending (first visible column is now index 0)
  });

  // Initialize page state
  const selectedPatient = $('select[name="patient_filter"]').val();
  const selectedPet = $('#pet_filter').data('selected-pet') || '';
  
  if (selectedPatient) {
    loadPetFilter(selectedPatient);
    
    // Set selected pet if exists
    if (selectedPet) {
      setTimeout(function() {
        $('#pet_filter').val(selectedPet);
      }, 500);
    }
  } else {
    // Ensure pet filter is disabled when no patient is selected
    $('#pet_filter').prop('disabled', true);
  }
});

// Function to handle patient filter changes
function filterRecords(patientId) {
  const petFilter = $('#pet_filter');
  
  if (patientId && patientId !== '') {
    petFilter.prop('disabled', false); // Enable pet filter
    loadPetFilter(patientId); // Load pets for the selected patient
  } else {
    petFilter.prop('disabled', true); // Disable pet filter
    petFilter.html('<option value="">All Pets</option>'); // Reset pet filter
  }
  
  // Build URL with current filters
  let url = '?patient_filter=' + patientId;
  location.href = url;
}

// Function to load pets for the pet filter dropdown (pets belonging to selected patient)
function loadPetFilter(patientId) {
  const petFilter = $('#pet_filter');
  
  if (!patientId) {
    petFilter.html('<option value="">All Pets</option>').prop('disabled', true);
    return;
  }

  $.post('pet.php', { patient_id: patientId }, function (data) {
    if (data.success && data.pets.length > 0) {
      let options = '<option value="">All Pets</option>';
      data.pets.forEach(pet => {
        options += `<option value="${pet.id}">${pet.name}</option>`;
      });
      petFilter.html(options).prop('disabled', false);
    } else {
      petFilter.html('<option value="">No pets found for this patient</option>').prop('disabled', true);
    }
  }, 'json').fail(function() {
    petFilter.html('<option value="">Error loading pets</option>').prop('disabled', true);
  });
}

// Function to handle pet filter changes
function filterByPet(petId) {
  const patientId = $('select[name="patient_filter"]').val();
  
  let url = '?patient_filter=' + patientId;
  if (petId && petId !== '') {
    url += '&pet_filter=' + petId;
  }
  location.href = url;
}

// Function to load pets for edit modal
function loadEditPets(patientId) {
  const petSelect = $('#edit_pet_select');
  if (!patientId) {
    petSelect.html('<option value="">Select patient first</option>').prop('disabled', true);
    return;
  }

  $.post('pet.php', { patient_id: patientId }, function (data) {
    if (data.success && data.pets.length > 0) {
      let options = '<option value="">Choose Pet</option>';
      data.pets.forEach(pet => {
        options += `<option value="${pet.id}">${pet.name}</option>`;
      });
      petSelect.html(options).prop('disabled', false);
    } else {
      petSelect.html('<option value="">No pets found for this patient</option>').prop('disabled', true);
    }
  }, 'json').fail(function() {
    petSelect.html('<option value="">Error loading pets</option>').prop('disabled', true);
  });
}

// Function to edit record (populate edit modal)
function editRecord(recordId) {
  // Fetch record data and populate edit modal
  $.post('get_vaccination_record.php', { record_id: recordId }, function(data) {
    if (data.success) {
      const record = data.record;
      $('#edit_record_id').val(record.id);
      $('#edit_patient_select').val(record.patient_id);
      $('#edit_vaccination_date').val(record.vaccination_date);
      $('#edit_doctor_id').val(record.doctor_id);
      $('#edit_weight').val(record.weight_lbs);
      $('#edit_temperature').val(record.temperature_celsius);
      $('#edit_vaccination_notes').val(record.vaccination_notes);
      $('#edit_doctor_remark').val(record.doctor_remark);
      
      // Load pets for the selected patient
      loadEditPets(record.patient_id);
      
      // Set selected pet after pets are loaded
      setTimeout(function() {
        $('#edit_pet_select').val(record.pet_id);
      }, 500);
      
      $('#editModal').modal('show');
    } else {
      alert('Error loading record data');
    }
  }, 'json').fail(function() {
    alert('Error loading record data');
  });
}

// Function to delete record
function deleteRecord(recordId) {
  if (confirm('Are you sure you want to delete this vaccination record?')) {
    $.post('delete_vaccination_record.php', { record_id: recordId }, function(data) {
      if (data.success) {
        alert('Record deleted successfully');
        location.reload();
      } else {
        alert('Error deleting record: ' + (data.message || 'Unknown error'));
      }
    }, 'json').fail(function() {
      alert('Error deleting record');
    });
  }
}

function filterRecords(patientId) {
      if(patientId) {
        window.location.href = '?patient_filter=' + patientId;
      } else {
        window.location.href = window.location.pathname;
      }
    }

    function filterByPet(petId) {
      const currentUrl = new URL(window.location);
      if(petId) {
        currentUrl.searchParams.set('pet_filter', petId);
      } else {
        currentUrl.searchParams.delete('pet_filter');
      }
      window.location.href = currentUrl.toString();
    }