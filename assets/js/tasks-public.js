jQuery(document).ready(function($) {
    $('#new-task-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = $(this).serialize();
        
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
                    window.location.reload();
                } else {
                    alert('Error adding task');
                }
            }
        });
    });
});
