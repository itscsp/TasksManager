<?php
/**
 * The public-facing functionality of the plugin
 */
class Tasks_Public {
    public function init() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('tasks_manager', array($this, 'render_tasks_manager'));
        
        // AJAX handlers for logged-in users
        add_action('wp_ajax_add_task', array($this, 'handle_add_task'));
        add_action('wp_ajax_add_subtask', array($this, 'handle_add_subtask'));
        add_action('wp_ajax_update_task_status', array($this, 'handle_update_task_status'));
        add_action('wp_ajax_update_subtask_status', array($this, 'handle_update_subtask_status'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style('tasks-public', TASKS_PLUGIN_URL . 'assets/css/tasks-public.css', array(), TASKS_VERSION);
        wp_enqueue_script('tasks-public', TASKS_PLUGIN_URL . 'assets/js/tasks-public.js', array('jquery'), TASKS_VERSION, true);
        
        wp_localize_script('tasks-public', 'tasksAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tasks_ajax_nonce')
        ));
    }

    public function render_tasks_manager() {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            return '<p class="tasks-login-required">You must be logged in to access the task manager. <a href="' . wp_login_url(get_permalink()) . '">Login here</a>.</p>';
        }
        
        ob_start();
        include TASKS_PLUGIN_DIR . 'public/partials/tasks-manager.php';
        return ob_get_clean();
    }

    /**
     * Handle AJAX request to add a new task
     */
    public function handle_add_task() {
        // Verify nonce and user permissions
        if (!wp_verify_nonce($_POST['nonce'], 'tasks_ajax_nonce') || !is_user_logged_in()) {
            wp_die('Security check failed');
        }

        // Parse form data
        parse_str($_POST['task_data'], $task_data);

        // Create new task post
        $post_data = array(
            'post_title' => sanitize_text_field($task_data['task_title']),
            'post_content' => sanitize_textarea_field($task_data['task_description']),
            'post_type' => 'task',
            'post_status' => 'publish',
            'post_author' => get_current_user_id()
        );

        $post_id = wp_insert_post($post_data);

        if ($post_id) {
            // Save meta data
            update_post_meta($post_id, '_task_status', sanitize_text_field($task_data['task_status']));
            
            // Set project taxonomy
            if (!empty($task_data['task_project'])) {
                wp_set_post_terms($post_id, intval($task_data['task_project']), 'project');
            }

            wp_send_json_success('Task added successfully');
        } else {
            wp_send_json_error('Failed to add task');
        }
    }

    /**
     * Handle AJAX request to add a subtask
     */
    public function handle_add_subtask() {
        // Verify nonce and user permissions
        if (!wp_verify_nonce($_POST['nonce'], 'tasks_ajax_nonce') || !is_user_logged_in()) {
            wp_die('Security check failed');
        }

        $task_id = intval($_POST['task_id']);
        $subtask_title = sanitize_text_field($_POST['subtask_title']);
        $subtask_description = sanitize_textarea_field($_POST['subtask_description']);

        // Get existing subtasks
        $subtasks = get_post_meta($task_id, '_task_subtasks', true);
        if (!is_array($subtasks)) {
            $subtasks = array();
        }

        // Add new subtask
        $subtasks[] = array(
            'title' => $subtask_title,
            'description' => $subtask_description,
            'status' => 'todo',
            'created' => current_time('mysql')
        );

        update_post_meta($task_id, '_task_subtasks', $subtasks);

        wp_send_json_success('Subtask added successfully');
    }

    /**
     * Handle AJAX request to update task status
     */
    public function handle_update_task_status() {
        // Verify nonce and user permissions
        if (!wp_verify_nonce($_POST['nonce'], 'tasks_ajax_nonce') || !is_user_logged_in()) {
            wp_die('Security check failed');
        }

        $task_id = intval($_POST['task_id']);
        $status = sanitize_text_field($_POST['status']);

        update_post_meta($task_id, '_task_status', $status);

        wp_send_json_success('Task status updated');
    }

    /**
     * Handle AJAX request to update subtask status
     */
    public function handle_update_subtask_status() {
        // Verify nonce and user permissions
        if (!wp_verify_nonce($_POST['nonce'], 'tasks_ajax_nonce') || !is_user_logged_in()) {
            wp_die('Security check failed');
        }

        $task_id = intval($_POST['task_id']);
        $subtask_index = intval($_POST['subtask_index']);
        $status = sanitize_text_field($_POST['status']);

        // Get existing subtasks
        $subtasks = get_post_meta($task_id, '_task_subtasks', true);
        if (!is_array($subtasks) || !isset($subtasks[$subtask_index])) {
            wp_send_json_error('Subtask not found');
        }

        // Update subtask status
        $subtasks[$subtask_index]['status'] = $status;
        $subtasks[$subtask_index]['updated'] = current_time('mysql');

        update_post_meta($task_id, '_task_subtasks', $subtasks);

        wp_send_json_success('Subtask status updated');
    }
}
