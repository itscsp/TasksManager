jQuery(document).ready(function($) {
    // Initialize flatpickr
    if (typeof flatpickr !== 'undefined') {
        // Initialize date pickers with better configuration
        const datePickerConfig = {
            dateFormat: "Y-m-d",
            minDate: "today",
            disableMobile: false,
            allowInput: true,
            clickOpens: true,
            position: "auto center",
            positionElement: undefined, // Let flatpickr handle positioning
            static: false,
            defaultHour: 0,
            enableTime: false,
            appendTo: document.body, // Append to body for better positioning
            onChange: function(selectedDates, dateStr, instance) {
                if (selectedDates[0]) {
                    selectedDates[0].setHours(0, 0, 0, 0);
                }
            },
            onOpen: function(selectedDates, dateStr, instance) {
                // Prevent modal from closing when date picker opens
                instance.calendarContainer.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
                // Ensure proper z-index
                instance.calendarContainer.style.zIndex = '10000';
            },
            onReady: function(selectedDates, dateStr, instance) {
                // Add class to calendar for custom styling
                instance.calendarContainer.classList.add('tasks-datepicker');
            }
        };

        // Initialize start date picker
        if (document.getElementById('task_start_date')) {
            flatpickr("#task_start_date", datePickerConfig);
        }

        // Initialize end date picker
        if (document.getElementById('task_end_date')) {
            flatpickr("#task_end_date", datePickerConfig);
        }

        // Initialize regular task date picker if exists
        if (document.getElementById('task_date')) {
            flatpickr("#task_date", datePickerConfig);
        }
    } else {
        console.error('Flatpickr is not loaded');
    }

    // Global click handler to prevent modal closing when interacting with flatpickr
    $(document).on('click', '.flatpickr-calendar, .flatpickr-calendar *', function(e) {
        e.stopPropagation();
    });

    // Prevent modal close on ESC when flatpickr is open
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape') {
            var openPickers = document.querySelectorAll('.flatpickr-calendar.open');
            if (openPickers.length > 0) {
                e.stopPropagation();
            }
        }
    });
});

 


jQuery(document).ready(function($) {
    // Handle Today's Tasks Accordion
    $('.accordion-header:not(.archives-accordion-header):not(.future-tasks-accordion-header)').on('click keypress', function(e) {
        if (e.type !== 'click' && (e.type !== 'keypress' || (e.which !== 13 && e.which !== 32))) {
            return;
        }

        var $target = $(e.target);
        if ($target.closest('.status-checkbox-label').length > 0 || $target.hasClass('status-checkbox')) {
            return;
        }

        var $item = $(this).closest('.accordion-item');
        var wasOpen = $item.hasClass('open');

        // Close all regular task accordions
        $('.accordion-item:not(.archives-accordion-item):not(.future-tasks-accordion-item).open')
            .removeClass('open')
            .find('.accordion-content')
            .slideUp(250);

        if (!wasOpen) {
            $item.addClass('open')
                .find('.accordion-content')
                .slideDown(350);
        }
    });

    // Handle Future Tasks Accordion
        $('.future-tasks-accordion-header').on('click keypress', function(e) {
            if (e.type !== 'click' && (e.type !== 'keypress' || (e.which !== 13 && e.which !== 32))) {
                return;
            }

            var $target = $(e.target);
            if ($target.closest('.status-checkbox-label').length > 0 || $target.hasClass('status-checkbox')) {
                return;
            }

            var $item = $(this).closest('.future-tasks-accordion-item');
            var wasOpen = $item.hasClass('open');

            // Only close other open future tasks accordions, not all accordions
            $('.future-tasks-accordion-item.open').not($item)
                .removeClass('open')
                .find('> .accordion-content')
                .slideUp(250);

            if (!wasOpen) {
                $item.addClass('open')
                    .find('> .accordion-content')
                    .slideDown(350);
            } else {
                $item.removeClass('open')
                    .find('> .accordion-content')
                    .slideUp(250);
            }
        });
    // Optionally, open the first item by default:
    // $('.accordion-item').not('.archives-accordion-item').first().addClass('open').find('.accordion-content').show();
});

jQuery(document).ready(function($) {
    // Modal open/close logic
    function openModal(modal) {
        $(modal).fadeIn(150).css('display', 'flex');
        $(modal).find('input, textarea, select').first().focus();
    }
    function closeModal(modal) {
        $(modal).fadeOut(120);
    }

    // Add Task Modal
    $('#open-add-task-modal').on('click', function() {
        openModal('#add-task-modal');
    });
    $('#close-add-task-modal').on('click', function() {
        closeModal('#add-task-modal');
    });
    // Close modal on overlay click
    $('#add-task-modal').on('click', function(e) {
        // Don't close modal if clicking on flatpickr calendar
        if ($(e.target).closest('.flatpickr-calendar').length > 0) {
            return;
        }
        if (e.target === this) closeModal(this);
    });

    // Add Subtask Modal (per task)
    $('.open-add-subtask-modal').on('click', function() {
        var taskId = $(this).data('task-id');
        openModal('#add-subtask-modal-' + taskId);
    });
    $('.close-add-subtask-modal').on('click', function() {
        var taskId = $(this).data('task-id');
        closeModal('#add-subtask-modal-' + taskId);
    });
    $('.add-subtask-modal').on('click', function(e) {
        // Don't close modal if clicking on flatpickr calendar
        if ($(e.target).closest('.flatpickr-calendar').length > 0) {
            return;
        }
        if (e.target === this) closeModal(this);
    });

    // Handle main task form submission (from modal)
    $('#new-task-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true).text('Adding...');
        $.ajax({
            url: tasksAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'add_task',
                nonce: tasksAjax.nonce,
                task_data: formData
            },
            success: function(response) {
                if (response.success) {
                    $('#new-task-form')[0].reset();
                    closeModal('#add-task-modal');
                    window.location.reload();
                } else {
                    alert('Error adding task: ' + (response.data || 'Unknown error'));
                }
            },
            error: function() {
                alert('Network error occurred while adding task');
            },
            complete: function() {
                submitButton.prop('disabled', false).text('Add Task');
            }
        });
    });

    // Handle subtask form submission (from modal)
    $('.subtask-form').on('submit', function(e) {
        e.preventDefault();
        var taskId = $(this).data('task-id');
        var title = $(this).find('input[name="subtask_title"]').val();
        var description = $(this).find('textarea[name="subtask_description"]').val();
        var submitButton = $(this).find('button[type="submit"]');
        submitButton.prop('disabled', true).text('Adding...');
        $.ajax({
            url: tasksAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'add_subtask',
                nonce: tasksAjax.nonce,
                task_id: taskId,
                subtask_title: title,
                subtask_description: description
            },
            success: function(response) {
                if (response.success) {
                    closeModal('#add-subtask-modal-' + taskId);
                    window.location.reload();
                } else {
                    alert('Error adding subtask: ' + (response.data || 'Unknown error'));
                }
            },
            error: function() {
                alert('Network error occurred while adding subtask');
            },
            complete: function() {
                submitButton.prop('disabled', false).text('Add Subtask');
            }
        });
    });


    // Handle task status checkbox
    $('.status-checkbox').on('change', function() {
        var $checkbox = $(this);
        var taskId = $checkbox.data('task-id');
        var newStatus = $checkbox.is(':checked') ? 'completed' : 'todo';
        // Only target the main task status, not subtask statuses inside the same task item
        var statusSpan = $checkbox.closest('.task-item')
                                  .find('.task-status')
                                  .filter(function() {
                                      return $(this).closest('.subtask-item').length === 0;
                                  })
                                  .first();
        var $box = $checkbox.siblings('.custom-checkbox-box');

        $checkbox.prop('disabled', true);

        // Toggle checkmark SVG in custom box
        if ($checkbox.is(':checked')) {
            if ($box.find('.custom-checkbox-check').length === 0) {
                $box.html('<svg class="custom-checkbox-check" stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="22" width="22" xmlns="http://www.w3.org/2000/svg"><path d="M17.47 250.9C88.82 328.1 158 397.6 224.5 485.5c72.3-143.8 146.3-288.1 268.4-444.37L460 26.06C356.9 135.4 276.8 238.9 207.2 361.9c-48.4-43.6-126.62-105.3-174.38-137z"></path></svg>');
            }
        } else {
            $box.empty();
        }

        $.ajax({
            url: tasksAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'update_task_status',
                nonce: tasksAjax.nonce,
                task_id: taskId,
                status: newStatus
            },
            success: function(response) {
                if (response.success) {
                    statusSpan.removeClass('todo in-progress completed')
                             .addClass(newStatus)
                             .text(newStatus.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase()));
                } else {
                    alert('Error updating task status');
                    // Revert checkbox and checkmark
                    $checkbox.prop('checked', !$checkbox.is(':checked'));
                    if ($checkbox.is(':checked')) {
                        $box.html('<svg class="custom-checkbox-check" stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="22" width="22" xmlns="http://www.w3.org/2000/svg"><path d="M17.47 250.9C88.82 328.1 158 397.6 224.5 485.5c72.3-143.8 146.3-288.1 268.4-444.37L460 26.06C356.9 135.4 276.8 238.9 207.2 361.9c-48.4-43.6-126.62-105.3-174.38-137z"></path></svg>');
                    } else {
                        $box.empty();
                    }
                }
            },
            error: function() {
                alert('Network error occurred while updating status');
                $checkbox.prop('checked', !$checkbox.is(':checked'));
                if ($checkbox.is(':checked')) {
                    $box.html('<svg class="custom-checkbox-check" stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="22" width="22" xmlns="http://www.w3.org/2000/svg"><path d="M17.47 250.9C88.82 328.1 158 397.6 224.5 485.5c72.3-143.8 146.3-288.1 268.4-444.37L460 26.06C356.9 135.4 276.8 238.9 207.2 361.9c-48.4-43.6-126.62-105.3-174.38-137z"></path></svg>');
                } else {
                    $box.empty();
                }
            },
            complete: function() {
                $checkbox.prop('disabled', false);
            }
        });
    });



    // Handle subtask status change (admin and frontend)
    $(document).on('change', '.subtask-status-checkbox', function() {
        var $checkbox = $(this);
        var newStatus = $checkbox.is(':checked') ? 'completed' : 'todo';
        var $subtaskItem = $checkbox.closest('.subtask-item');
        var statusSpan = $subtaskItem.find('.task-status');
        // For admin meta box
        var subtaskIndex = $checkbox.data('subtask-index') !== undefined ? $checkbox.data('subtask-index') : $subtaskItem.index();
        var taskId = $checkbox.data('task-id') || $subtaskItem.data('task-id');

        $checkbox.prop('disabled', true);

        $.ajax({
            url: tasksAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'update_subtask_status',
                nonce: tasksAjax.nonce,
                task_id: taskId,
                subtask_index: subtaskIndex,
                status: newStatus
            },
            success: function(response) {
                if (response.success) {
                    if (statusSpan.length) {
                        statusSpan.removeClass('todo in-progress completed')
                                .addClass(newStatus)
                                .text(newStatus.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase()));
                    }
                } else {
                    alert('Error updating subtask status');
                    // Revert checkbox state if there's an error
                    $checkbox.prop('checked', !$checkbox.is(':checked'));
                }
            },
            error: function() {
                alert('Network error occurred while updating subtask status');
                // Revert checkbox state on error
                $checkbox.prop('checked', !$checkbox.is(':checked'));
            },
            complete: function() {
                $checkbox.prop('disabled', false);
            }
        });
    });

    // Add some visual feedback for form interactions
    $('input, textarea, select').on('focus', function() {
        $(this).closest('.task-item, .add-task-form, .add-subtask-form').addClass('focused');
    }).on('blur', function() {
        $(this).closest('.task-item, .add-task-form, .add-subtask-form').removeClass('focused');
    });


});


//Date and timer functionality
jQuery(document).ready(function($) {

    function updateDateAndCountdown() {
        const now = new Date();
        const dayName = now.toLocaleDateString('en-US', { weekday: 'short' });
        const day = now.getDate();
        const month = now.toLocaleDateString('en-US', { month: 'long' });
        const year = now.getFullYear();

        $('#today-date').html(`Today: <span>${dayName} ${day} ${month} ${year}</span>`);

        // End of day (midnight)
        const endOfDay = new Date();
        endOfDay.setHours(23, 59, 59, 999);

        const timeLeft = endOfDay - now;

        const hours = Math.floor((timeLeft / (1000 * 60 * 60)) % 24);
        const minutes = Math.floor((timeLeft / (1000 * 60)) % 60);
        const seconds = Math.floor((timeLeft / 1000) % 60);

        $('#countdown').html(`<span>${hours}h ${minutes}m ${seconds}s</span>`);
    }

    updateDateAndCountdown();
    setInterval(updateDateAndCountdown, 1000);

    // Motivational Quotes System
    const motivationalQuotes = [
        {
            text: "The way to get started is to quit talking and begin doing.",
            author: "Walt Disney"
        },
        {
            text: "The future depends on what you do today.",
            author: "Mahatma Gandhi"
        },
        {
            text: "Success is not final, failure is not fatal: it is the courage to continue that counts.",
            author: "Winston Churchill"
        },
        {
            text: "It does not matter how slowly you go as long as you do not stop.",
            author: "Confucius"
        },
        {
            text: "Everything you've ever wanted is on the other side of fear.",
            author: "George Addair"
        },
        {
            text: "Believe you can and you're halfway there.",
            author: "Theodore Roosevelt"
        },
        {
            text: "The only impossible journey is the one you never begin.",
            author: "Tony Robbins"
        },
        {
            text: "In the middle of difficulty lies opportunity.",
            author: "Albert Einstein"
        },
        {
            text: "A year from now you may wish you had started today.",
            author: "Karen Lamb"
        },
        {
            text: "The secret of getting ahead is getting started.",
            author: "Mark Twain"
        },
        {
            text: "Don't watch the clock; do what it does. Keep going.",
            author: "Sam Levenson"
        },
        {
            text: "The way to achieve your own success is to be willing to help somebody else get it first.",
            author: "Iyanla Vanzant"
        }
    ];

    function displayDailyQuote() {
        // Get today's date as a seed for consistent daily quote
        const today = new Date();
        
        // Better way to calculate day of year (1-365/366)
        const start = new Date(today.getFullYear(), 0, 1);
        const dayOfYear = Math.floor((today - start) / (1000 * 60 * 60 * 24)) + 1;
        
        // Use day of year to select quote (ensures same quote all day)
        const quoteIndex = (dayOfYear - 1) % motivationalQuotes.length;
        const todaysQuote = motivationalQuotes[quoteIndex];
        
        // Debug info (remove this later)
        console.log('Day of year:', dayOfYear, 'Quote index:', quoteIndex, 'Total quotes:', motivationalQuotes.length);
        
        // Update the quote with fade effect
        const quoteText = $('#daily-quote');
        
        if (quoteText.length) {
            // Check if quote is different before fading
            if (quoteText.text() !== todaysQuote.text) {
                quoteText.fadeOut(300, function() {
                    $(this).text(todaysQuote.text).fadeIn(500);
                });
            } else {
                // Just set the text without animation if it's the same
                quoteText.text(todaysQuote.text);
            }
        }
    }

    // Display today's quote on page load
    displayDailyQuote();

    // Add click functionality to manually change quote (for testing)
    $('.tasks-quote-strip').on('click', function() {
        // Get a random quote for testing purposes
        const randomIndex = Math.floor(Math.random() * motivationalQuotes.length);
        const randomQuote = motivationalQuotes[randomIndex];
        
        const quoteText = $('#daily-quote');
        
        quoteText.fadeOut(200, function() {
            $(this).text(randomQuote.text).fadeIn(400);
        });
        
        console.log('Manual quote change - Index:', randomIndex, 'Quote:', randomQuote.text);
    });

});