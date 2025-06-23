// Users Management JavaScript
let userToDelete = null;

function filterByRole(roleId) {
  if(roleId) {
    window.location.href = '?role_filter=' + roleId;
  } else {
    window.location.href = window.location.pathname;
  }
}

function confirmDeleteUser(userId, userType) {
  userToDelete = {
    id: userId,
    type: userType
  };
  $('#deleteModal').modal('show');
}

// Handle confirm delete using jQuery for modal
$(document).ready(function() {
  $('#confirmDelete').on('click', function() {
    if(userToDelete) {
      var xhr = new XMLHttpRequest();
      xhr.open('POST', 'delete_user.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      
      xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
          try {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
              $('#deleteModal').modal('hide');
              alert('User deleted successfully!');
              location.reload();
            } else {
              alert('Error deleting user: ' + response.message);
            }
          } catch (e) {
            alert('Error deleting user. Please try again.');
          }
        }
      };
      
      var params = 'user_id=' + userToDelete.id + '&user_type=' + userToDelete.type;
      xhr.send(params);
    }
  });
});