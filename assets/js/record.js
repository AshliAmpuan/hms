$(document).ready(function() {
  // Initialize DataTable
  $('#records-table').DataTable({
    "order": [[1, "desc"]] // Sort by vaccination date descending
  });
});

// Function to handle pet filter changes
function filterByPet(petId) {
  let url = window.location.pathname;
  if (petId && petId !== '') {
    url += '?pet_filter=' + petId;
  }
  location.href = url;
}