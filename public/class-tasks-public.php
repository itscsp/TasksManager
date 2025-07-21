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
        wp_enqueue_style('tasks-flatpickr-css', TASKS_PLUGIN_URL . 'assets/library/flatpickr/flatpickr.min.css', array(), TASKS_VERSION);
        // wp_enqueue_style('tasks-flatpickr-custom', TASKS_PLUGIN_URL . 'assets/css/tasks-flatpickr.css', array('tasks-flatpickr-css'), TASKS_VERSION);
        wp_enqueue_script('tasks-flatpickr-js', TASKS_PLUGIN_URL . 'assets/library/flatpickr/flatpickr.js', array('jquery'), TASKS_VERSION, true);

        wp_enqueue_style('tasks-public', TASKS_PLUGIN_URL . 'assets/css/tasks-public.css', array(), TASKS_VERSION);
        wp_enqueue_style('tasks-public-model', TASKS_PLUGIN_URL . 'assets/css/tasks-model.css', array(), TASKS_VERSION);

        wp_enqueue_script('tasks-public', TASKS_PLUGIN_URL . 'assets/js/tasks-public.js', array('jquery'), TASKS_VERSION, true);
        wp_enqueue_script('tasks-archive-accordion', TASKS_PLUGIN_URL . 'assets/js/tasks-archive-accordion.js', array('jquery'), TASKS_VERSION, true);
        wp_enqueue_script('tasks-archive-accordion', TASKS_PLUGIN_URL . 'assets/js/tasks-archive-accordion.js', array('jquery'), TASKS_VERSION, true);


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

        $model = new Tasks_Model();
        $task_id = $model->add_task([
            'title' => $task_data['task_title'],
            'description' => $task_data['task_description'],
            'status' => isset($task_data['task_status']) ? $task_data['task_status'] : 'todo',
            'project' => $task_data['task_project'],
            'author' => get_current_user_id(),
            'date' => $task_data['task_date']
        ]);

        if (is_wp_error($task_id)) {
            wp_send_json_error($task_id->get_error_message());
        } else {
            wp_send_json_success('Task added successfully');
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

        $model = new Tasks_Model();
        $result = $model->add_subtask($task_id, [
            'title' => $_POST['subtask_title'],
            'description' => $_POST['subtask_description'],
            'status' => 'todo'
        ]);

        if ($result) {
            wp_send_json_success('Subtask added successfully');
        } else {
            wp_send_json_error('Failed to add subtask');
        }
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
