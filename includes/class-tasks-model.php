<?php
/**
 * Tasks_Model - Handles CRUD for tasks and subtasks
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Include WordPress core functionality
require_once(ABSPATH . 'wp-includes/post.php');
require_once(ABSPATH . 'wp-includes/pluggable.php');
class Tasks_Model {
    /**
     * Add a new task
     *
     * @param array $data (expects keys: title, description, status, project, author)
     * @return int|WP_Error Post ID on success, WP_Error on failure
     */
    public function add_task($data) {
        // If date is set, ensure it's at midnight of that day
        if (isset($data['date'])) {
            // Convert to site's timezone
            $timezone = new DateTimeZone(get_option('timezone_string') ?: 'UTC');
            $date = new DateTime($data['date'], $timezone);
            $date->setTime(0, 0, 0); // Set to midnight
            $task_date = $date->format('Y-m-d H:i:s');
        } else {
            $task_date = current_time('mysql');
        }
        
        $current_time = current_time('mysql');
        
        $post_data = array(
            'post_title'   => sanitize_text_field($data['title']),
            'post_content' => sanitize_textarea_field($data['description']),
            'post_type'    => 'task',
            'post_status'  => strtotime($task_date) > strtotime($current_time) ? 'future' : 'publish',
            'post_author'  => intval($data['author']),
            'post_date'    => $task_date,
            'post_date_gmt' => get_gmt_from_date($task_date) // Ensure GMT time is set correctly
        );
        $post_id = wp_insert_post($post_data);
        if (is_wp_error($post_id) || !$post_id) {
            return new WP_Error('task_create_failed', 'Failed to create task');
        }
        update_post_meta($post_id, '_task_status', sanitize_text_field($data['status']));
        if (!empty($data['project'])) {
            wp_set_post_terms($post_id, intval($data['project']), 'project');
        }
        return $post_id;
    }

    /**
     * Add a subtask to a task
     *
     * @param int $task_id
     * @param array $subtask (expects keys: title, description, status)
     * @return bool|WP_Error
     */
    public function add_subtask($task_id, $subtask) {
        $subtasks = get_post_meta($task_id, '_task_subtasks', true);
        if (!is_array($subtasks)) {
            $subtasks = array();
        }
        $subtasks[] = array(
            'title'       => sanitize_text_field($subtask['title']),
            'description' => sanitize_textarea_field($subtask['description']),
            'status'      => isset($subtask['status']) ? sanitize_text_field($subtask['status']) : 'todo',
            'created'     => current_time('mysql')
        );
        return update_post_meta($task_id, '_task_subtasks', $subtasks);
    }
}
