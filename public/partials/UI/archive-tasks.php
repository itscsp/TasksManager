<?php

// Get archived tasks (older than today)
$today = date('Y-m-d');
$archive_args = array(
    'post_type' => 'task',
    'posts_per_page' => -1,
    'author' => get_current_user_id(),
    'post_status' => array('private', 'publish'), // Include both private and any legacy published tasks
    'meta_query' => array(
        'relation' => 'OR',
        // Tasks with end date before today
        array(
            'key'     => '_task_end_date',
            'value'   => $today,
            'compare' => '<',
            'type'    => 'DATE'
        ),
        // Tasks with no end date, use post_date
        array(
            'key'     => '_task_end_date',
            'compare' => 'NOT EXISTS',
        )
    )
);

$archive_tasks = new WP_Query($archive_args);

// Display Archives Section in Accordion
echo '<div class="accordion-item archives-accordion-item archives-section">';
echo '<div class="accordion-header archives-accordion-header">';
echo '<h3 class="archives-heading">Archives</h3>';
echo '<span class="accordion-icon"></span>';
echo '</div>';
echo '<div class="accordion-content">';
if ($archive_tasks->have_posts()) {
    $current_date = '';
    while ($archive_tasks->have_posts()) {
        $archive_tasks->the_post();
        $task_id = get_the_ID();
        $task_date = get_the_date('Y-m-d');
        $formatted_date = get_the_date('F j, Y');
        $status = get_post_meta($task_id, '_task_status', true);
        $status = $status ? $status : 'todo';
        $subtasks = get_post_meta($task_id, '_task_subtasks', true);
        $subtasks = is_array($subtasks) ? $subtasks : array();
        // Check if we need a new date heading
        if ($current_date !== $task_date) {
            if ($current_date !== '') {
                echo '</div>'; // Close previous date section
            }
            $current_date = $task_date;
            echo '<div class="date-section">';
            echo '<h3 class="date-heading">' . $formatted_date . '</h3>';
        }
        // Include the task item with archive mode
        $show_add_subtask = false; // Disable add subtask button for archive items
        $show_sheduled_task = false;
        include TASKS_PLUGIN_DIR . 'public/partials/UI/task-item.php';
    }
    if ($current_date !== '') {
        echo '</div>'; // Close last date section
    }
} else {
    echo '<div class="task-item no-tasks"><p>No archived tasks found</p></div>';
}

wp_reset_postdata();
echo '</div>'; // Close accordion-content
echo '</div>'; // Close accordion-item/archives-section
