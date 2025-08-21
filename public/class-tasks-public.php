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
        add_action('wp_ajax_add_task_comment', array($this, 'handle_add_comment'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style('tasks-flatpickr-css', TASKS_PLUGIN_URL . 'assets/library/flatpickr/flatpickr.min.css', array(), TASKS_VERSION);
        wp_enqueue_script('tasks-flatpickr-js', TASKS_PLUGIN_URL . 'assets/library/flatpickr/flatpickr.js', array('jquery'), TASKS_VERSION, true);

        // Consolidated CSS file with all styles
        wp_enqueue_style('tasks-main', TASKS_PLUGIN_URL . 'assets/css/main.css', array('tasks-flatpickr-css'), TASKS_VERSION);

        wp_enqueue_script('tasks-public', TASKS_PLUGIN_URL . 'assets/js/tasks-public.js', array('jquery'), TASKS_VERSION, true);
        wp_enqueue_script('tasks-archive-accordion', TASKS_PLUGIN_URL . 'assets/js/tasks-archive-accordion.js', array('jquery'), TASKS_VERSION, true);
        wp_enqueue_script('tasks-comments', TASKS_PLUGIN_URL . 'assets/js/tasks-comments.js', array('jquery'), TASKS_VERSION, true);


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
            'status' => 'todo',
            'project' => $task_data['task_project'],
            'author' => get_current_user_id(),
            'start_date' => $task_data['task_start_date'],
            'end_date' => $task_data['task_end_date']
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
        
        // Check if user owns this task
        $task_author = get_post_field('post_author', $task_id);
        if ($task_author != get_current_user_id()) {
            wp_die('Permission denied: You can only modify your own tasks');
        }

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
        
        // Check if user owns this task
        $task_author = get_post_field('post_author', $task_id);
        if ($task_author != get_current_user_id()) {
            wp_die('Permission denied: You can only modify your own tasks');
        }

        update_post_meta($task_id, '_task_status', $status);

        wp_send_json_success('Task status updated');
    }

    /**
     * Handle AJAX request to update subtask status
     */
    /**
     * Handle AJAX comment submission
     */
    public function handle_add_comment() {
        check_ajax_referer('tasks_ajax_nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error('You must be logged in to comment');
        }

        $task_id = intval($_POST['task_id']);
        
        // Check if user owns this task or if task allows commenting by current user
        $task_author = get_post_field('post_author', $task_id);
        if ($task_author != get_current_user_id()) {
            wp_send_json_error('Permission denied: You can only comment on your own tasks');
        }
        
        $comment_content = wp_kses_post($_POST['comment']);
        $user = wp_get_current_user();

        $comment_data = array(
            'comment_post_ID' => $task_id,
            'comment_content' => $comment_content,
            'user_id' => $user->ID,
            'comment_author' => $user->display_name,
            'comment_author_email' => $user->user_email,
            'comment_type' => 'comment',
            'comment_parent' => isset($_POST['comment_parent']) ? intval($_POST['comment_parent']) : 0,
        );

        $comment_id = wp_insert_comment($comment_data);

        if ($comment_id) {
            $comment = get_comment($comment_id);
            $response = array(
                'id' => $comment_id,
                'content' => apply_filters('comment_text', $comment->comment_content),
                'author' => $comment->comment_author,
                'date' => sprintf(
                    _x('%s ago', '%s = human-readable time difference', 'tasks'),
                    human_time_diff(strtotime($comment->comment_date_gmt))
                ),
                'avatar' => get_avatar_url($comment->user_id, array('size' => 32)),
                'parent' => $comment->comment_parent
            );
            wp_send_json_success($response);
        } else {
            wp_send_json_error('Failed to add comment');
        }
    }

    public function handle_update_subtask_status() {
        // Verify nonce and user permissions
        if (!wp_verify_nonce($_POST['nonce'], 'tasks_ajax_nonce') || !is_user_logged_in()) {
            wp_die('Security check failed');
        }

        $task_id = intval($_POST['task_id']);
        $subtask_index = intval($_POST['subtask_index']);
        $status = sanitize_text_field($_POST['status']);
        
        // Check if user owns this task
        $task_author = get_post_field('post_author', $task_id);
        if ($task_author != get_current_user_id()) {
            wp_die('Permission denied: You can only modify your own tasks');
        }

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
