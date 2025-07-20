jQuery(document).ready(function($) {
    // Accordion logic for task items, ignore Archives accordion
    $('.accordion-header').not('.archives-accordion-header').on('click keypress', function(e) {
        // Prevent toggle if click/keypress originated from the status checkbox or its label
        var $target = $(e.target);
        if (
            $target.closest('.status-checkbox-label').length > 0 ||
            $target.hasClass('status-checkbox')
        ) {
            return;
        }
        if (e.type === 'click' || (e.type === 'keypress' && (e.which === 13 || e.which === 32))) {
            var $item = $(this).closest('.accordion-item').not('.archives-accordion-item');
            if ($item.hasClass('open')) {
                $item.removeClass('open');
                $item.find('.accordion-content').slideUp(250);
            } else {
                // Close all others except archives
                $('.accordion-item.open').not('.archives-accordion-item').removeClass('open').find('.accordion-content').slideUp(250);
                $item.addClass('open');
                $item.find('.accordion-content').slideDown(350);
            }
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
        var statusSpan = $checkbox.closest('.task-item').find('.task-status');
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
    $(document).on('change', 'select[name="subtask_status[]"], .subtask-status-selector', function() {
        var $select = $(this);
        var newStatus = $select.val();
        var $subtaskItem = $select.closest('.subtask-item');
        var statusSpan = $subtaskItem.find('.task-status');
        // For admin meta box
        var subtaskIndex = $select.data('subtask-index') !== undefined ? $select.data('subtask-index') : $subtaskItem.index();
        var taskId = $select.data('task-id') || $subtaskItem.data('task-id');

        $select.prop('disabled', true);

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
                }
            },
            error: function() {
                alert('Network error occurred while updating subtask status');
            },
            complete: function() {
                $select.prop('disabled', false);
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

});