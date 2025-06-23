// Doctor Messages JavaScript

let currentChat = null;

$(document).ready(function() {
  // Patient item click handler
  $(document).on('click', '.patient-item', function() {
    $('.patient-item').removeClass('active');
    $(this).addClass('active');
    
    const patientId = $(this).data('id');
    const patientName = $(this).data('name');
    
    currentChat = {
      id: patientId,
      name: patientName
    };
    
    startChat(patientName, patientId);
  });

  // Message input keypress handler
  $('#message-input').on('keypress', function(e) {
    if (e.which === 13) { // Enter key
      e.preventDefault();
      sendMessage();
    }
  });

  // Patient search functionality
  $('#patient-search').on('input', function() {
    const searchTerm = $(this).val().toLowerCase();
    $('.patient-item').each(function() {
      const patientName = $(this).data('name').toLowerCase();
      const patientInfo = $(this).find('.patient-info').text().toLowerCase();
      if (patientName.includes(searchTerm) || patientInfo.includes(searchTerm)) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });

  // Auto-select patient if coming from dashboard
  if (window.selectedPatientId) {
    const selectedPatientElement = $(`.patient-item[data-id="${window.selectedPatientId}"]`);
    if (selectedPatientElement.length > 0) {
      // Automatically click the selected patient
      selectedPatientElement.click();
    } else {
      // If patient not found in the list, show a message
      console.log('Selected patient not found in the list');
    }
  }

  // Update unread counts periodically
  updateUnreadCounts();
  setInterval(updateUnreadCounts, 30000); // Every 30 seconds

  // Auto-refresh messages every 10 seconds
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
          patient_id: currentChat.id
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
  }, 10000);
});

function startChat(patientName, patientId) {
  // Update chat header
  $('#chat-title').text(patientName);
  $('#chat-subtitle').text('Patient');
  
  // Enable chat input
  $('#message-input').prop('disabled', false).focus();
  $('#send-btn').prop('disabled', false);
  
  // Load messages from database
  loadMessages(patientId);
  
  // Remove unread count badge
  $(`.patient-item[data-id="${patientId}"] .unread-count`).remove();
  
  // Update URL without refreshing page (optional)
  if (window.history && window.history.pushState) {
    const newUrl = new URL(window.location);
    newUrl.searchParams.set('patient_id', patientId);
    if (window.selectedReservationId) {
      newUrl.searchParams.set('reservation_id', window.selectedReservationId);
    }
    window.history.pushState({}, '', newUrl);
  }
}

function loadMessages(patientId) {
  $('#chat-messages').html('<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading messages...</div>');
  
  $.ajax({
    url: '',
    method: 'POST',
    data: {
      action: 'load_messages',
      patient_id: patientId
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
      <div class="text-center text-muted mt-5">
        <i class="fas fa-comments fa-2x mb-3"></i>
        <p>No messages yet. Start the conversation!</p>
      </div>
    `;
  } else {
    messages.forEach(function(msg) {
      const messageClass = msg.sender_type === 'staff' ? 'sent' : 'received';
      messagesHtml += `
        <div class="message ${messageClass}">
          <div class="message-content">${escapeHtml(msg.message)}</div>
          <div class="message-time">${msg.time}</div>
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
  $('#send-btn').prop('disabled', true).text('Sending...');
  
  $.ajax({
    url: '',
    method: 'POST',
    data: {
      action: 'send_message',
      patient_id: currentChat.id,
      message: messageText
    },
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        $('#message-input').val('');
        loadMessages(currentChat.id);
      } else {
        alert('Failed to send message: ' + response.message);
      }
    },
    error: function() {
      alert('Error sending message. Please try again.');
    },
    complete: function() {
      $('#send-btn').prop('disabled', false).text('Send');
      $('#message-input').focus();
    }
  });
}

function updateUnreadCounts() {
  $.ajax({
    url: '',
    method: 'POST',
    data: {
      action: 'get_unread_counts'
    },
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        // Remove all existing unread count badges
        $('.unread-count').remove();
        
        // Add new unread count badges
        Object.keys(response.counts).forEach(function(patientId) {
          const count = response.counts[patientId];
          if (count > 0) {
            const badge = `<div class="unread-count">${count}</div>`;
            $(`.patient-item[data-id="${patientId}"]`).append(badge);
          }
        });
      }
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