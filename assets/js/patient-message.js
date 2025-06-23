let currentChat = null;

$(document).ready(function() {
  // Staff item click handler
  $('.staff-item').on('click', function() {
    $('.staff-item').removeClass('active');
    $(this).addClass('active');
    
    const staffType = $(this).data('type');
    const staffId = $(this).data('id');
    const staffName = $(this).data('name');
    
    currentChat = {
      type: staffType,
      id: staffId,
      name: staffName
    };
    
    startChat(staffName, staffType, staffId);
  });

  // Message input keypress handler
  $('#message-input').on('keypress', function(e) {
    if (e.which === 13) { // Enter key
      e.preventDefault();
      sendMessage();
    }
  });
});

function startChat(staffName, staffType, staffId) {
  // Update chat header
  $('#chat-title').text(staffName);
  $('#chat-subtitle').text(staffType === 'doctor' ? 'Doctor' : 'Cashier');
  
  // Enable chat input
  $('#message-input').prop('disabled', false).focus();
  $('#send-btn').prop('disabled', false);
  
  // Load messages from database
  loadMessages(staffType, staffId);
  
  // Remove unread count badge
  $(`.staff-item[data-type="${staffType}"][data-id="${staffId}"] .unread-count`).remove();
}

function loadMessages(staffType, staffId) {
  $('#chat-messages').html('<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i> Loading messages...</div>');
  
  $.ajax({
    url: '',
    method: 'POST',
    data: {
      action: 'load_messages',
      staff_type: staffType,
      staff_id: staffId
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
      const messageClass = msg.sender_type === 'patient' ? 'sent' : 'received';
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
      staff_type: currentChat.type,
      staff_id: currentChat.id,
      message: messageText
    },
    dataType: 'json',
    success: function(response) {
      if (response.success) {
        $('#message-input').val('');
        loadMessages(currentChat.type, currentChat.id);
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
        staff_type: currentChat.type,
        staff_id: currentChat.id
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