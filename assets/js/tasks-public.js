jQuery(document).ready(function($) {
    // Handle main task form submission
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
                    // Clear form and reload page
                    $('#new-task-form')[0].reset();
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

    // Toggle subtask form visibility
    $('.toggle-subtask-form').on('click', function() {
        var taskId = $(this).data('task-id');
        var form = $('#subtask-form-' + taskId);
        
        if (form.hasClass('show')) {
            form.removeClass('show');
            $(this).text('Add Subtask');
        } else {
            // Hide other open forms
            $('.add-subtask-form').removeClass('show');
            $('.toggle-subtask-form').text('Add Subtask');
            
            // Show this form
            form.addClass('show');
            $(this).text('Hide Form');
            form.find('input[name="subtask_title"]').focus();
        }
    });

    // Cancel subtask form
    $('.cancel-subtask').on('click', function() {
        var form = $(this).closest('.add-subtask-form');
        var taskId = form.data('task-id') || form.attr('id').replace('subtask-form-', '');
        
        form.removeClass('show');
        form.find('form')[0].reset();
        $('.toggle-subtask-form[data-task-id="' + taskId + '"]').text('Add Subtask');
    });

    // Handle subtask form submission
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
                    // Clear form and reload page
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

    // Handle task status changes
    $('.status-selector').on('change', function() {
        var taskId = $(this).data('task-id');
        var newStatus = $(this).val();
        var statusSpan = $(this).closest('.task-item').find('.task-status');
        
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
                    // Update status display
                    statusSpan.removeClass('todo in-progress completed')
                             .addClass(newStatus)
                             .text(newStatus.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase()));
                } else {
                    alert('Error updating task status');
                    // Revert the select
                    $(this).val(statusSpan.attr('class').split(' ')[1]);
                }
            },
            error: function() {
                alert('Network error occurred while updating status');
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
