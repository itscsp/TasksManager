
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

        $checkbox.prop('disabled', true);

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
                    // Revert checkbox
                    $checkbox.prop('checked', !$checkbox.is(':checked'));
                }
            },
            error: function() {
                alert('Network error occurred while updating status');
                $checkbox.prop('checked', !$checkbox.is(':checked'));
            },
            complete: function() {
                $checkbox.prop('disabled', false);
            }
        });
    });

    // Handle subtask status changes
    $('.subtask-status-selector').on('change', function() {
        var taskId = $(this).data('task-id');
        var subtaskIndex = $(this).data('subtask-index');
        var newStatus = $(this).val();
        var statusSpan = $(this).closest('.subtask-item').find('.task-status');
        
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
                    // Update status display
                    statusSpan.removeClass('todo in-progress completed')
                             .addClass(newStatus)
                             .text(newStatus.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase()));
                } else {
                    alert('Error updating subtask status');
                }
            },
            error: function() {
                alert('Network error occurred while updating subtask status');
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