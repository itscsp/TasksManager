jQuery(document).ready(function($) {
    // Handle comment form submission
    $('.ajax-comment-form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitButton = $form.find('button[type="submit"]');
        var taskId = $form.data('task-id');
        var comment = $form.find('textarea[name="comment"]').val();
        
        if (!comment.trim()) {
            alert('Please enter a comment');
            return;
        }

        $submitButton.prop('disabled', true).text('Posting...');

        $.ajax({
            url: tasksAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'add_task_comment',
                nonce: tasksAjax.nonce,
                task_id: taskId,
                comment: comment
            },
            success: function(response) {
                if (response.success) {
                    // Create new comment HTML
                    var commentHtml = `
                        <li class="task-comment">
                            <article class="comment-body">
                                <div class="comment-meta">
                                    <div>
                                    <img src="${response.data.avatar}" class="avatar" width="32" height="32" alt="">
                                   </div>
                                    <div>
                                    <div class="comment-author">
                                        ${response.data.author}
                                    </div>
                                    <div class="comment-metadata">
                                        <time>${response.data.date}</time>
                                    </div>
                                     <div>
                                </div>
                                <div class="comment-content">
                                    ${response.data.content}
                                </div>
                            </article>
                        </li>
                    `;

                    // Add new comment to list
                    var $commentsList = $form.closest('.task-comments-section').find('.comment-list');
                    if ($commentsList.length === 0) {
                        // Create comment list if it doesn't exist
                        $form.after('<div class="task-comments-list"><ul class="comment-list"></ul></div>');
                        $commentsList = $form.closest('.task-comments-section').find('.comment-list');
                    }
                    
                    $commentsList.prepend(commentHtml);
                    
                    // Clear the form
                    $form.find('textarea[name="comment"]').val('');
                    
                    // Show success message
                    var $successMessage = $('<div class="comment-success">Comment added successfully!</div>');
                    $form.before($successMessage);
                    setTimeout(function() {
                        $successMessage.fadeOut(function() {
                            $(this).remove();
                        });
                    }, 3000);
                } else {
                    alert('Error adding comment: ' + (response.data || 'Unknown error'));
                }
            },
            error: function() {
                alert('Network error occurred while adding comment');
            },
            complete: function() {
                $submitButton.prop('disabled', false).text('Post Comment');
            }
        });
    });
});
