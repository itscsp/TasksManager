<?php
// Get future tasks
$future_tasks_args = array(
    'post_type' => 'task',
    'posts_per_page' => -1,
    'post_status' => array('future'),
    'date_query' => array(
        array(
            'after' => date('Y-m-d H:i:s'),
            'inclusive' => false,
        ),
    ),
    'orderby' => 'date',
    'order' => 'ASC'
);

$future_tasks = new WP_Query($future_tasks_args);

if ($future_tasks->have_posts()) : ?>
    <div class="accordion-item future-tasks-accordion-item">
        <div class="accordion-header future-tasks-accordion-header">
            <h3 class="future-tasks-heading">Future Tasks <span class="task-count">(<?php echo $future_tasks->found_posts; ?>)</span></h3>
            <span class="accordion-icon">

            </span>
        </div>
        <div class="accordion-content future-tasks-content">
            <?php while ($future_tasks->have_posts()) : $future_tasks->the_post();
                $task_id = get_the_ID();
                $status = get_post_meta($task_id, '_task_status', true);
                $status = $status ? $status : 'todo';
                $task_date = get_the_date('F j, Y');
                $subtasks = get_post_meta($task_id, '_task_subtasks', true);
                $subtasks = is_array($subtasks) ? $subtasks : array();
            ?>
                <?php
                $show_add_subtask = true; // Enable add subtask button for future tasks
                $show_sheduled_task = true;
                include TASKS_PLUGIN_DIR . 'public/partials/UI/task-item.php';

                ?>
                </li>
            <?php endwhile; ?>
            </ul>
        </div>
    </div>
<?php endif;
wp_reset_postdata(); ?>