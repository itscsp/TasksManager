<?php
/**
 * Template for the tasks manager shortcode
 *
 * @package TasksManager
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}
?>
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
                <textarea id="task_description" name="task_description"></textarea>
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
                    <option value="completed">Completed</option>
                </select>
            </p>
            <button type="submit" class="button">Add Task</button>
        </form>
    </div>

    <div class="tasks-list">
        <h2>Tasks</h2>
        <?php
        $args = array(
            'post_type' => 'task',
            'posts_per_page' => -1,
        );
        $tasks = new WP_Query($args);

        if ($tasks->have_posts()) :
            while ($tasks->have_posts()) : $tasks->the_post();
                $status = get_post_meta(get_the_ID(), '_task_status', true);
                $subtasks = get_post_meta(get_the_ID(), '_task_subtasks', true);
                ?>
                <div class="task-item">
                    <h3><?php the_title(); ?></h3>
                    <div class="task-content">
                        <?php the_content(); ?>
                    </div>
                    <div class="task-meta">
                        <p>Status: <?php echo ucfirst($status); ?></p>
                        <p>Project: <?php echo get_the_term_list(get_the_ID(), 'project', '', ', '); ?></p>
                    </div>
                    <?php if (!empty($subtasks)) : ?>
                        <div class="subtasks">
                            <h4>Subtasks:</h4>
                            <ul>
                                <?php foreach ($subtasks as $subtask) : ?>
                                    <li>
                                        <strong><?php echo esc_html($subtask['title']); ?></strong>
                                        <p><?php echo esc_html($subtask['description']); ?></p>
                                        <p>Status: <?php echo ucfirst($subtask['status']); ?></p>
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
            echo '<p>No tasks found.</p>';
        endif;
        ?>
    </div>
</div>
