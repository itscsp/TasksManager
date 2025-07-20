<?php

        // Get today's tasks
        $today_args = array(
            'post_type' => 'task',
            'posts_per_page' => -1,
            'author' => get_current_user_id(),
            'date_query' => array(
                array(
                    'year' => date('Y'),
                    'month' => date('m'),
                    'day' => date('d')
                )
            ),
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
        $today_tasks = new WP_Query($today_args);
        
        // Display Today's Tasks Section
        echo '<div class="today-section">';
        
        if ($today_tasks->have_posts()) {
            while ($today_tasks->have_posts()) {
                $today_tasks->the_post();
                $task_id = get_the_ID();
                $status = get_post_meta($task_id, '_task_status', true);
                $status = $status ? $status : 'todo';
                $subtasks = get_post_meta($task_id, '_task_subtasks', true);
                $subtasks = is_array($subtasks) ? $subtasks : array();
                
                include TASKS_PLUGIN_DIR . 'public/partials/UI/task-item.php';
            }
        } else {
            echo '<div class="task-item no-tasks"><p>No tasks for today</p></div>';
        }
        wp_reset_postdata();
        echo '</div>'; // Close today section
        ?>