let currentChat = null;
let currentFilter = 'all';

$(document).ready(function() {
  // Recipients item click handler
  $('.staff-item').on('click', function() {
    if ($(this).hasClass('text-muted')) return; // Don't allow clicking on "No X available" items
    
    $('.staff-item').removeClass('active');
    $(this).addClass('active');
    
    const recipientType = $(this).data('type');
    const recipientId = $(this).data('id');
    const recipientName = $(this).data('name');
    
    currentChat = {
      type: recipientType,
      id: recipientId,
      name: recipientName
    };
    
    startChat(recipientName, recipientType, recipientId);
  });

  // Message input keypress handler
  $('#message-input').on('keypress', function(e) {
    if (e.which === 13) { // Enter key
      e.preventDefault();
      sendMessage();
    }
  });

  // Search functionality
  $('#search-input').on('input', function() {
    const searchTerm = $(this).val().toLowerCase();
    filterRecipients(searchTerm);
  });

  // Filter functionality
  $('.filter-option').on('click', function(e) {
    e.preventDefault();
    $('.filter-option').removeClass('active');
    $(this).addClass('active');
    
    currentFilter = $(this).data('filter');
    applyFilter();
  });
});

function startChat(recipientName, recipientType, recipientId) {
  // Update chat header
  $('#chat-title').text(recipientName);
  
  let roleText = '';
  let badgeClass = '';
  switch(recipientType) {
    case 'patient':
      roleText = 'Patient';
      badgeClass = 'badge-info';
      break;
    case 'doctor':
      roleText = 'Doctor';
      badgeClass = 'badge-success';
      break;
    case 'cashier':
      roleText = 'Cashier';
      badgeClass = 'badge-warning';
      break;
  }
  
  $('#chat-subtitle').text(`${roleText} • Admin Chat`);
  $('#recipient-type-badge').removeClass('badge-info badge-success badge-warning badge-danger')
                            .addClass('badge-danger')
                            .text('ADMIN')
                            .show();
  
  // Enable chat input
  $('#message-input').prop('disabled', false).focus();
  $('#send-btn').prop('disabled', false);
  
  // Load messages from database
  loadMessages(recipientType, recipientId);
  
  // Remove unread count badge
  $(`.staff-item[data-type="${recipientType}"][data-id="${recipientId}"] .unread-count`).remove();
}

function loadMessages(recipientType, recipientId) {
  $('#chat-messages').html('<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading messages...</div>');
  
  $.ajax({
    url: '',
    method: 'POST',
    data: {
      action: 'load_messages',
      recipient_type: recipientType,
      recipient_id: recipientId
    },
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        displayMessages(response.messages);
      } else {
        $('#chat-messages').html('<div class="text-center text-muted mt-5"><p>Failed to load messages</p></div>');
      }
    },
    error: function() {
      $('#chat-messages').html('<div class="text-center text-muted mt-5"><p>Error loading messages</p></div>');
    }
  });
}

function displayMessages(messages) {
  let messagesHtml = '';
  
  if (messages.length === 0) {
    messagesHtml = `
      <div class="chat-empty">
        <i class="fas fa-comments"></i>
        <p>No messages yet. Start the conversation!</p>
        <small class="text-muted">Send the first admin message to begin chatting</small>
      </div>
    `;
  } else {
    messages.forEach(function(msg) {
      const messageClass = msg.sender_type === 'staff' ? 'sent' : 'received';
      const senderLabel = msg.sender_type === 'staff' ? 'Admin' : currentChat.name;
      const messageTypeClass = msg.sender_type === 'staff' ? 'admin-message' : 'user-message';
      
      messagesHtml += `
        <div class="message ${messageClass} ${messageTypeClass}">
          <div class="message-content">${escapeHtml(msg.message)}</div>
          <div class="message-time">${msg.time} • ${senderLabel}</div>
        </div>
      `;
    });
  }
  
  $('#chat-messages').html(messagesHtml);
  scrollToBottom();
}

function sendMessage() {
  const messageText = $('#message-input').val().trim();
  
  if (messageText === '' || !currentChat) {
    return;
  }
  
  // Disable send button temporarily
  $('#send-btn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sending...');
  
  $.ajax({
    url: '',
    method: 'POST',
    data: {
      action: 'send_message',
      recipient_type: currentChat.type,
      recipient_id: currentChat.id,
      message: messageText
    },
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        $('#message-input').val('');
        loadMessages(currentChat.type, currentChat.id);
        
        // Show success notification
        showNotification('Admin message sent successfully!', 'success');
      } else {
        showNotification('Failed to send admin message: ' + response.message, 'error');
      }
    },
    error: function() {
      showNotification('Error sending admin message. Please try again.', 'error');
    },
    complete: function() {
      $('#send-btn').prop('disabled', false).html('<i class="fas fa-paper-plane"></i> Send');
      $('#message-input').focus();
    }
  });
}

function scrollToBottom() {
  const chatMessages = document.getElementById('chat-messages');
  chatMessages.scrollTop = chatMessages.scrollHeight;
}

function escapeHtml(text) {
  const map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };
  return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

function filterRecipients(searchTerm) {
  $('.staff-item').each(function() {
    const name = $(this).data('name');
    if (name && name.toLowerCase().includes(searchTerm)) {
      $(this).show();
    } else {
      $(this).hide();
    }
  });
  
  // Show/hide section headers based on visible items
  updateSectionVisibility();
}

function applyFilter() {
  if (currentFilter === 'all') {
    $('.staff-item, .staff-section-header').show();
  } else {
    $('.staff-item').hide();
    $('.staff-section-header').hide();
    $(`.${currentFilter}-item, .${currentFilter}-section`).show();
  }
  
  // Clear search when changing filter
  $('#search-input').val('');
  
  updateSectionVisibility();
}

function updateSectionVisibility() {
  $('.staff-section-header').each(function() {
    const section = $(this);
    const sectionClass = section.hasClass('patient-section') ? 'patient-item' : 
                        section.hasClass('doctor-section') ? 'doctor-item' : 'cashier-item';
    
    const visibleItems = $(`.${sectionClass}:visible`).not('.text-muted').length;
    
    if (visibleItems > 0) {
      section.show();
    } else {
      section.hide();
    }
  });
}

function showNotification(message, type) {
  const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
  const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
  
  const notification = $(`
    <div class="alert ${alertClass} alert-dismissible fade show notification-alert" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
      <i class="fas ${iconClass} mr-2"></i>
      ${message}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  `);
  
  $('body').append(notification);
  
  // Auto remove after 3 seconds
  setTimeout(function() {
    notification.alert('close');
  }, 3000);
}

// Auto-refresh messages every 15 seconds
setInterval(function() {
  if (currentChat) {
    const currentScrollTop = $('#chat-messages').scrollTop();
    const maxScroll = $('#chat-messages')[0].scrollHeight - $('#chat-messages').height();
    const isAtBottom = currentScrollTop >= maxScroll - 10;
    
    $.ajax({
      url: '',
      method: 'POST',
      data: {
        action: 'load_messages',
        recipient_type: currentChat.type,
        recipient_id: currentChat.id
      },
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          displayMessages(response.messages);
          if (!isAtBottom) {
            $('#chat-messages').scrollTop(currentScrollTop);
          }
        }
      }
    });
  }
}, 15000);

// Mark messages as read when chat is opened
function markMessagesAsRead(recipientType, recipientId) {
  $.ajax({
    url: '',
    method: 'POST',
    data: {
      action: 'mark_read',
      recipient_type: recipientType,
      recipient_id: recipientId
    },
    dataType: 'json'
  });
}