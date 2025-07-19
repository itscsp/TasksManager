<div class="tasks-manager-container">
    <div class="add-task-form">
        <h2>Add New Task</h2>
        <form id="new-task-form">
            <p>
                <label for="task_title">Title:</label>
                <input type="text" id="task_title" name="task_title" required>
            </p>
            <p>
                <label for="task_description">Description:</label>
                <textarea id="task_description" name="task_description" rows="4"></textarea>
            </p>
            <p>
                <label for="task_project">Project:</label>
                <?php
                wp_dropdown_categories(array(
                    'taxonomy' => 'project',
                    'name' => 'task_project',
                    'show_option_none' => 'Select Project',
                    'option_none_value' => '',
                    'hide_empty' => false,
                ));
                ?>
            </p>
            <p>
                <label for="task_status">Status:</label>
                <select id="task_status" name="task_status">
                    <option value="todo">Todo</option>
                    <option value="in-progress">In Progress</option>
                    <option value="completed">Completed</option>
                </select>
            </p>
            <button type="submit" class="button">Add Task</button>
        </form>
    </div>

    <div class="tasks-list">
        <h2>Your Tasks</h2>
        <?php
        $args = array(
            'post_type' => 'task',
            'posts_per_page' => -1,
            'author' => get_current_user_id(), // Only show current user's tasks
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => '_task_status',
                    'compare' => 'EXISTS'
                ),
                array(
                    'key' => '_task_status',
                    'compare' => 'NOT EXISTS'
                )
            )
        );
        $tasks = new WP_Query($args);

        if ($tasks->have_posts()) :
            while ($tasks->have_posts()) : $tasks->the_post();
                $task_id = get_the_ID();
                $status = get_post_meta($task_id, '_task_status', true);
                $status = $status ? $status : 'todo';
                $subtasks = get_post_meta($task_id, '_task_subtasks', true);
                $subtasks = is_array($subtasks) ? $subtasks : array();
                ?>
                <div class="task-item" data-task-id="<?php echo $task_id; ?>">
                    <h3><?php the_title(); ?></h3>
                    <div class="task-content">
                        <?php the_content(); ?>
                    </div>
                    <div class="task-meta">
                        <p>Status: <span class="task-status <?php echo $status; ?>"><?php echo ucfirst(str_replace('-', ' ', $status)); ?></span></p>
                        <p>Project: <?php echo get_the_term_list($task_id, 'project', '', ', '); ?></p>
                        <p>Created: <?php echo get_the_date(); ?></p>
                    </div>
                    
                    <!-- Task Actions -->
                    <div class="task-actions">
                        <button class="btn toggle-subtask-form" data-task-id="<?php echo $task_id; ?>">Add Subtask</button>
                        <select class="status-selector" data-task-id="<?php echo $task_id; ?>">
                            <option value="todo" <?php selected($status, 'todo'); ?>>Todo</option>
                            <option value="in-progress" <?php selected($status, 'in-progress'); ?>>In Progress</option>
                            <option value="completed" <?php selected($status, 'completed'); ?>>Completed</option>
                        </select>
                    </div>

                    <!-- Add Subtask Form -->
                    <div class="add-subtask-form" id="subtask-form-<?php echo $task_id; ?>">
                        <h4>Add Subtask</h4>
                        <form class="subtask-form" data-task-id="<?php echo $task_id; ?>">
                            <input type="text" name="subtask_title" placeholder="Subtask Title" required>
                            <textarea name="subtask_description" placeholder="Subtask Description" rows="2"></textarea>
                            <div>
                                <button type="submit" class="btn">Add Subtask</button>
                                <button type="button" class="btn secondary cancel-subtask">Cancel</button>
                            </div>
                        </form>
                    </div>

                    <!-- Subtasks List -->
                    <?php if (!empty($subtasks)) : ?>
                        <div class="subtasks">
                            <h4>Subtasks (<?php echo count($subtasks); ?>):</h4>
                            <ul>
                                <?php foreach ($subtasks as $index => $subtask) : ?>
                                    <li class="subtask-item">
                                        <div class="subtask-content">
                                            <strong><?php echo esc_html($subtask['title']); ?></strong>
                                            <?php if (!empty($subtask['description'])) : ?>
                                                <p><?php echo esc_html($subtask['description']); ?></p>
                                            <?php endif; ?>
                                            <small>Status: <span class="task-status <?php echo $subtask['status']; ?>"><?php echo ucfirst(str_replace('-', ' ', $subtask['status'])); ?></span></small>
                                            <?php if (!empty($subtask['created'])) : ?>
                                                <small> | Created: <?php echo date('M j, Y', strtotime($subtask['created'])); ?></small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="subtask-actions">
                                            <select class="subtask-status-selector" data-task-id="<?php echo $task_id; ?>" data-subtask-index="<?php echo $index; ?>">
                                                <option value="todo" <?php selected($subtask['status'], 'todo'); ?>>Todo</option>
                                                <option value="in-progress" <?php selected($subtask['status'], 'in-progress'); ?>>In Progress</option>
                                                <option value="completed" <?php selected($subtask['status'], 'completed'); ?>>Completed</option>
                                            </select>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            <?php
            endwhile;
            wp_reset_postdata();
        else :
            echo '<div class="task-item"><p>No tasks found. Create your first task above!</p></div>';
        endif;
        ?>
    </div>
</div>
