<?php
/**
 * The admin-specific functionality of the plugin
 */
class Tasks_Admin {
    public function init() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_task', array($this, 'save_meta_data'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function enqueue_scripts() {
        wp_enqueue_script('tasks-admin', TASKS_PLUGIN_URL . 'assets/js/tasks-admin.js', array('jquery'), TASKS_VERSION, true);
    }

    public function add_meta_boxes() {
        add_meta_box(
            'task_status',
            'Task Status',
            array($this, 'render_status_meta_box'),
            'task',
            'side',
            'high'
        );

        add_meta_box(
            'task_subtasks',
            'Subtasks',
            array($this, 'render_subtasks_meta_box'),
            'task',
            'normal',
            'high'
        );
    }

    public function render_status_meta_box($post) {
        $status = get_post_meta($post->ID, '_task_status', true);
        wp_nonce_field('tasks_save_meta', 'tasks_meta_nonce');
        ?>
        <select name="task_status" id="task_status">
            <option value="todo" <?php selected($status, 'todo'); ?>>Todo</option>
            <option value="completed" <?php selected($status, 'completed'); ?>>Completed</option>
        </select>
        <?php
    }

    public function render_subtasks_meta_box($post) {
        $subtasks = get_post_meta($post->ID, '_task_subtasks', true);
        if (!is_array($subtasks)) {
            $subtasks = array();
        }
        include TASKS_PLUGIN_DIR . 'admin/partials/subtasks-meta-box.php';
    }

    public function save_meta_data($post_id) {
        if (!isset($_POST['tasks_meta_nonce']) || !wp_verify_nonce($_POST['tasks_meta_nonce'], 'tasks_save_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save task status
        if (isset($_POST['task_status'])) {
            update_post_meta($post_id, '_task_status', sanitize_text_field($_POST['task_status']));
        }

        // Save subtasks
        if (isset($_POST['subtask_title'])) {
            $subtasks = array();
            $titles = $_POST['subtask_title'];
            $descriptions = $_POST['subtask_description'];
            $statuses = $_POST['subtask_status'];

            for ($i = 0; $i < count($titles); $i++) {
                if (!empty($titles[$i])) {
                    $subtasks[] = array(
                        'title' => sanitize_text_field($titles[$i]),
                        'description' => sanitize_textarea_field($descriptions[$i]),
                        'status' => sanitize_text_field($statuses[$i])
                    );
                }
            }
            update_post_meta($post_id, '_task_subtasks', $subtasks);
        }
    }
}
