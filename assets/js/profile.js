// Profile Page JavaScript

$(document).ready(function() {
    
    // Form validation
    const form = $('.needs-validation');
    
    // Custom form validation
    form.on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        $(this).addClass('was-validated');
    });
    
    // Real-time validation for required fields
    $('input[required], textarea[required]').on('blur', function() {
        validateField($(this));
    });
    
    $('input[required], textarea[required]').on('input', function() {
        if ($(this).hasClass('is-invalid')) {
            validateField($(this));
        }
    });
    
    function validateField(field) {
        const value = field.val().trim();
        const fieldType = field.attr('type');
        
        // Reset classes
        field.removeClass('is-valid is-invalid');
        
        if (value === '') {
            field.addClass('is-invalid');
            return false;
        }
        
        // Email validation
        if (fieldType === 'email') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                field.addClass('is-invalid');
                return false;
            }
        }
        
        // Phone validation (basic)
        if (fieldType === 'tel' && value !== '') {
            const phoneRegex = /^[\+]?[0-9\s\-\(\)]{10,}$/;
            if (!phoneRegex.test(value)) {
                field.addClass('is-invalid');
                return false;
            }
        }
        
        field.addClass('is-valid');
        return true;
    }
    
    // Profile picture hover effect
    $('.profile-widget-picture').hover(
        function() {
            $(this).css('transform', 'scale(1.05)');
        },
        function() {
            $(this).css('transform', 'scale(1)');
        }
    );
    
    // Social media button animations
    $('.btn-social-icon').hover(
        function() {
            $(this).css('transform', 'translateY(-2px) scale(1.1)');
        },
        function() {
            $(this).css('transform', 'translateY(0) scale(1)');
        }
    );
    
    // Auto-resize textarea
    $('textarea').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
    
    // Character counter for bio
    const bioTextarea = $('textarea[name="bio"]');
    const maxLength = 500;
    
    if (bioTextarea.length) {
        // Add character counter
        bioTextarea.after('<div class="character-counter"><span class="current">0</span>/' + maxLength + ' characters</div>');
        
        bioTextarea.on('input', function() {
            const currentLength = $(this).val().length;
            $('.character-counter .current').text(currentLength);
            
            if (currentLength > maxLength) {
                $('.character-counter').addClass('text-danger');
                $(this).addClass('is-invalid');
            } else {
                $('.character-counter').removeClass('text-danger');
                $(this).removeClass('is-invalid');
            }
        });
        
        // Initialize counter
        bioTextarea.trigger('input');
    }
    
    // Phone number formatting
    $('input[name="contact_number"]').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        
        // Format phone number (adjust pattern as needed)
        if (value.length >= 10) {
            if (value.startsWith('63')) {
                // Philippine format: +63 XXX XXX XXXX
                value = value.replace(/(\d{2})(\d{3})(\d{3})(\d{4})/, '+$1 $2 $3 $4');
            } else {
                // Generic format: XXX-XXX-XXXX
                value = value.replace(/(\d{3})(\d{3})(\d{4})/, '$1-$2-$3');
            }
        }
        
        $(this).val(value);
    });
    
    // Smooth scroll to form if validation fails
    form.on('invalid', function() {
        setTimeout(function() {
            const firstInvalid = $('.is-invalid:first');
            if (firstInvalid.length) {
                $('html, body').animate({
                    scrollTop: firstInvalid.offset().top - 100
                }, 500);
                firstInvalid.focus();
            }
        }, 100);
    });
    
    // Save button loading state
    $('input[name="editProfile"]').on('click', function() {
        const btn = $(this);
        const originalText = btn.val();
        
        // Validate form before showing loading
        if (form[0].checkValidity()) {
            btn.val('Saving...').prop('disabled', true);
            btn.addClass('btn-loading');
            
            // Re-enable button after 5 seconds (fallback)
            setTimeout(function() {
                btn.val(originalText).prop('disabled', false);
                btn.removeClass('btn-loading');
            }, 5000);
        }
    });
    
    // Profile card animations
    $('.card').css('opacity', '0').animate({
        opacity: 1
    }, 600);
    
    // Tooltip initialization (if Bootstrap tooltips are used)
    $('[data-toggle="tooltip"]').tooltip();
    
    // Form field focus effects
    $('.form-control').on('focus', function() {
        $(this).parent().addClass('field-focused');
    }).on('blur', function() {
        $(this).parent().removeClass('field-focused');
    });
    
    // Auto-save draft functionality (optional)
    let saveTimeout;
    $('.form-control, textarea').on('input', function() {
        clearTimeout(saveTimeout);
        saveTimeout = setTimeout(function() {
            saveDraft();
        }, 2000);
    });
    
    function saveDraft() {
        const formData = {
            firstname: $('input[name="firstname"]').val(),
            lastname: $('input[name="lastname"]').val(),
            email: $('input[name="email"]').val(),
            contact_number: $('input[name="contact_number"]').val(),
            bio: $('textarea[name="bio"]').val()
        };
        
        // Save to localStorage (or send to server)
        localStorage.setItem('profile_draft', JSON.stringify(formData));
        
        // Show save indicator
        showSaveIndicator('Draft saved');
    }
    
    // Load draft on page load
    function loadDraft() {
        const draft = localStorage.getItem('profile_draft');
        if (draft) {
            const data = JSON.parse(draft);
            // Only load if fields are empty
            Object.keys(data).forEach(key => {
                const field = $(`[name="${key}"]`);
                if (field.val() === '' && data[key] !== '') {
                    field.val(data[key]);
                }
            });
        }
    }
    
    // Clear draft on successful save
    function clearDraft() {
        localStorage.removeItem('profile_draft');
    }
    
    function showSaveIndicator(message) {
        const indicator = $('<div class="save-indicator">' + message + '</div>');
        $('body').append(indicator);
        
        setTimeout(function() {
            indicator.fadeOut(function() {
                $(this).remove();
            });
        }, 2000);
    }
    
    // Initialize draft loading
    loadDraft();
    
    // Profile picture upload preview (if file input is added)
    $(document).on('change', 'input[type="file"][name="profile_picture"]', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('.profile-widget-picture').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        }
    });
    
});

// Additional utility functions
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    const re = /^[\+]?[0-9\s\-\(\)]{10,}$/;
    return re.test(phone);
}

function showNotification(message, type = 'success') {
    const notification = $(`
        <div class="alert alert-${type} alert-dismissible fade show notification" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `);
    
    $('body').append(notification);
    
    setTimeout(function() {
        notification.alert('close');
    }, 5000);
}

// Google Analytics tracking for profile updates
function trackProfileUpdate() {
    if (typeof gtag !== 'undefined') {
        gtag('event', 'profile_update', {
            'event_category': 'user_interaction',
            'event_label': 'profile_form'
        });
    }
}