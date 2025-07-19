<div class="tasks-manager-container">
    <div class="add-task-form">
        <h1 class="task-heading">Tasks</h1>
        <button id="open-add-task-modal" class="tasks-btn">Add Task</button>
    </div>

    <div class="today-date">
          <div class="time-box" id="today-date"></div>
  <div class="time-box" id="countdown"></div>
    </div>

       <?php include TASKS_PLUGIN_DIR . 'public/partials/UI/add-task-model.php';?>

    <div class="tasks-list">
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
       <?php include TASKS_PLUGIN_DIR . 'public/partials/UI/task-item.php';?>
              
            <?php
            endwhile;
            wp_reset_postdata();
        else :
            echo '<div class="task-item"><p>No tasks found. Create your first task above!</p></div>';
        endif;
        ?>
    </div>
</div>
