<?php
/**
 * Tasks_Model - Handles CRUD for tasks and subtasks
 */
class Tasks_Model {
    /**
     * Add a new task
     *
     * @param array $data (expects keys: title, description, status, project, author)
     * @return int|WP_Error Post ID on success, WP_Error on failure
     */
    public function add_task($data) {
        $post_data = array(
            'post_title'   => sanitize_text_field($data['title']),
            'post_content' => sanitize_textarea_field($data['description']),
            'post_type'    => 'task',
            'post_status'  => 'publish',
            'post_author'  => intval($data['author'])
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
