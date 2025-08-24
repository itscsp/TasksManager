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
        <div style="display: flex; align-items: center; gap: 8px;">
            <?php
            // Calculate days left
            $end_date = get_post_meta($task_id, '_task_end_date', true);
            if ($end_date) {
                $today = new DateTime();
                $end_date_obj = new DateTime($end_date);
                $days_diff = $today->diff($end_date_obj)->days;
                $is_overdue = $today > $end_date_obj;
                
                if ($is_overdue) {
                    echo '<span class="days-left-label overdue">';
                    echo $days_diff === 0 ? 'Due Today' : $days_diff . ' days overdue';
                    echo '</span>';
                } else {
                    $label_class = $days_diff <= 1 ? 'urgent' : ($days_diff <= 3 ? 'warning' : 'normal');
                    echo '<span class="days-left-label ' . $label_class . '">';
                    echo $days_diff === 0 ? 'Due Today' : $days_diff . ' days left';
                    echo '</span>';
                }
            }
            ?>
            <span class="accordion-icon" aria-hidden="true"></span>
        </div>
    </div>
    <div class="inner-container accordion-content" style="display: none;">
        <div class="task-content">
            <?php
            $start_date = get_post_meta($task_id, '_task_start_date', true);
            $end_date = get_post_meta($task_id, '_task_end_date', true);
            $is_future_task = (get_post_status() === 'future' || get_post_status() === 'private') && strtotime(get_the_date('Y-m-d')) > time();
            ?>
            <div class="task-meta">
                <p class="task-meta-inner">
                    <span>
                        <?php 
                        $project_terms = get_the_terms($task_id, 'project');
                        if ($project_terms && !is_wp_error($project_terms)): 
                        ?>
                            <span class="task-project">project: <span class="project-text"><?php echo get_the_term_list($task_id, 'project', '', ', '); ?></span></span>
                        <?php endif; ?>
                        <span class="task-due-date <?php echo $is_future_task ? 'scheduled' : ''; ?>">
                            <?php if ($start_date && $end_date): ?>
                                Active: <?php echo esc_html($start_date); ?> to <?php echo esc_html($end_date); ?>
                            <?php elseif ($start_date): ?>
                                Starts: <?php echo esc_html($start_date); ?>
                            <?php elseif ($end_date): ?>
                                Ends: <?php echo esc_html($end_date); ?>
                            <?php else: ?>
                                <?php echo $is_future_task ? 'Scheduled for: ' : 'Due: '; ?>
                                <?php echo $task_date; ?>
                            <?php endif; ?>
                        </span>
                    </span>
                    <a href="<?php echo get_edit_post_link($task_id); ?>" class="task-edit-small" target="_blank" rel="noopener noreferrer">Edit</a>
                </p>
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
                <div style="display: flex; gap: 10px; margin-left: auto;">
                    <button class="tasks-btn open-add-subtask-modal" data-task-id="<?php echo $task_id; ?>">Add</button>
                </div>
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