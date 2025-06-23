// patients.js

function viewPets(patientId, ownerName) {
  $('#petModalLabel').html('<i class="fas fa-paw"></i> Pets of ' + ownerName);
  $('#petModal').modal('show');

  $('#petModalBody').html(`
    <div class="text-center">
      <div class="spinner-border" role="status">
        <span class="sr-only">Loading...</span>
      </div>
      <p class="mt-2">Loading pet information...</p>
    </div>
  `);

  console.log('Patient ID:', patientId);

  $.ajax({
    url: 'get_pets.php',
    type: 'POST',
    data: { patient_id: patientId },
    dataType: 'json',
    success: function(response) {
      console.log('AJAX Response:', response);

      if (response && response.success) {
        if (response.pets && response.pets.length > 0) {
          let petHtml = '';
          response.pets.forEach(function(pet) {
            let sexDisplay = pet.sex === 'M' ? 'Male' : (pet.sex === 'F' ? 'Female' : 'Unknown');
            let birthDate = pet.birth_date ? new Date(pet.birth_date).toLocaleDateString() : 'N/A';

            petHtml += `
              <div class="pet-card">
                <h6 class="mb-3">${pet.pet_name}</h6>
                <div class="row">
                  <div class="col-md-6">
                    <div class="pet-info"><span><strong>Species:</strong></span><span>${pet.species}</span></div>
                    <div class="pet-info"><span><strong>Breed:</strong></span><span>${pet.breed || 'N/A'}</span></div>
                    <div class="pet-info"><span><strong>Age:</strong></span><span>${pet.age || 'N/A'} Years old</span></div>
                  </div>
                  <div class="col-md-6">
                    <div class="pet-info"><span><strong>Sex:</strong></span><span>${sexDisplay}</span></div>
                    <div class="pet-info"><span><strong>Weight:</strong></span><span>${pet.weight || 'N/A'} lbs</span></div>
                    <div class="pet-info"><span><strong>Birth Date:</strong></span><span>${birthDate}</span></div>
                  </div>
                </div>
              </div>
            `;
          });
          $('#petModalBody').html(petHtml);
        } else {
          $('#petModalBody').html('<div class="no-pets">No pets found for this patient.</div>');
        }
      } else {
        let errorMsg = response && response.message ? response.message : 'Unknown error occurred';
        $('#petModalBody').html('<div class="alert alert-danger">Error: ' + errorMsg + '</div>');
      }
    },
    error: function(xhr, status, error) {
      console.error('AJAX Error Details:', { status, error, responseText: xhr.responseText, statusCode: xhr.status });

      let errorMessage = 'Error loading pet information. ';
      if (xhr.status === 404) errorMessage += 'File not found (get_pets.php).';
      else if (xhr.status === 500) errorMessage += 'Server error occurred.';
      else if (xhr.status === 0) errorMessage += 'Network connection failed.';
      else errorMessage += 'Status: ' + xhr.status;

      $('#petModalBody').html('<div class="alert alert-danger">' + errorMessage + '<br><small>Check browser console for details.</small></div>');
    }
  });
}
