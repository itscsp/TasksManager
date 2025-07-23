<div class="task-item accordion-item" data-status="<?php echo $status; ?>" data-task-id="<?php echo $task_id; ?>">
    <div class="outer-container accordion-header" tabindex="0">
        <div class="task-item-heading-outer" style="display: flex; align-items: center; gap: 16px;">
            <div class="status-checkbox-group" data-task-id="<?php echo $task_id; ?>">
                <label class="status-checkbox-label custom-checkbox-label">
                    <input type="checkbox" class="status-checkbox custom-checkbox-input" data-task-id="<?php echo $task_id; ?>" <?php if ($status === 'completed') echo 'checked'; ?>>
                    <span class="custom-checkbox-box">
                        <?php if ($status === 'completed') : ?>
                            <svg class="custom-checkbox-check" stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="22" width="22" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.47 250.9C88.82 328.1 158 397.6 224.5 485.5c72.3-143.8 146.3-288.1 268.4-444.37L460 26.06C356.9 135.4 276.8 238.9 207.2 361.9c-48.4-43.6-126.62-105.3-174.38-137z"></path>
                            </svg>
                        <?php endif; ?>
                    </span>
                </label>
            </div>
            <h3 class="task-item-heading" style="margin: 0 8px 0 0; flex: 1; "><?php the_title(); ?></h3>
        </div>
        <span class="accordion-icon" aria-hidden="true"></span>
    </div>
    <div class="inner-container accordion-content" style="display: none;">
        <div class="task-content">
            <div class="task-meta">
                <p>
                <?php if (!isset($show_sheduled_task) || $show_sheduled_task === true): ?>
                    <span class="task-due-date <?php echo get_post_status() === 'future' ? 'scheduled' : ''; ?>">
                        <?php echo get_post_status() === 'future' ? 'Scheduled for: ' : 'Due: '; ?>
                        <?php echo $task_date; ?>
                    </span>
                <?php endif; ?>
                </p>
                <p>project: <?php echo get_the_term_list($task_id, 'project', '', ', '); ?></p>
            </div>
            <div class="description">
                <p class="mb-0 label">Description: </p>
                <p>
                    <?php the_content(); ?>
                </p>
            </div>
        </div>
        <?php if (!isset($show_add_subtask) || $show_add_subtask !== false): ?>
            <div class="subtask-header">
                <h4>Add Subtasks</h4>
                <button class="tasks-btn open-add-subtask-modal" data-task-id="<?php echo $task_id; ?>" style="margin-left: auto;">Add</button>
            </div>
            <?php include TASKS_PLUGIN_DIR . 'public/partials/UI/sub-task-model.php'; ?>
        <?php endif; ?>

        <?php include TASKS_PLUGIN_DIR . 'public/partials/UI/subtask-item.php'; ?>

        <!-- Set up and display comments -->
        <?php
        // Setup comment arguments
        global $wp_query;
        $task_comments = get_comments(array(
            'post_id' => $task_id,
            'order' => 'DESC'
        ));
        
        // Temporarily modify the global comment query
        $temp_comments = $wp_query->comments;
        $temp_comment_count = $wp_query->comment_count;
        
        $wp_query->comments = $task_comments;
        $wp_query->comment_count = count($task_comments);
        
        // Include the comments template
        include TASKS_PLUGIN_DIR . 'public/partials/UI/comments.php';
        
        // Restore the original comment query
        $wp_query->comments = $temp_comments;
        $wp_query->comment_count = $temp_comment_count;
        ?>

    </div>
</div>