<?php
/**
 * Handle meta boxes for tasks
 */
namespace TasksManager;

class Tasks_Meta_Boxes {
    public function init_hooks() {
        \add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        \add_action('save_post_task', array($this, 'save_meta_data'));
    }

    public function add_meta_boxes() {
        \add_meta_box(
            'task_details',
            'Task Details',
            array($this, 'render_meta_box'),
            'task',
            'normal',
            'high'
        );
    }

    public function render_meta_box($post) {
        // Add nonce for security
        \wp_nonce_field('task_details_nonce', 'task_details_nonce');
        
        // Get the current values
        $due_date = \get_post_meta($post->ID, '_task_due_date', true);
        $status = \get_post_meta($post->ID, '_task_status', true);
        
        // Output the form fields
        ?>
        <p>
            <label for="task_due_date">Due Date:</label>
            <input type="date" id="task_due_date" name="task_due_date" value="<?php echo \esc_attr($due_date); ?>">
        </p>
        <p>
            <label for="task_status">Status:</label>
            <select id="task_status" name="task_status">
                <option value="pending" <?php \selected($status, 'pending'); ?>>Pending</option>
                <option value="in-progress" <?php \selected($status, 'in-progress'); ?>>In Progress</option>
                <option value="completed" <?php \selected($status, 'completed'); ?>>Completed</option>
            </select>
        </p>
        <?php
    }

    public function save_meta_data($post_id) {
        // Check if our nonce is set
        if (!isset($_POST['task_details_nonce'])) {
            return;
        }

        // Verify that the nonce is valid
        if (!\wp_verify_nonce($_POST['task_details_nonce'], 'task_details_nonce')) {
            return;
        }

        // If this is an autosave, we don't want to do anything
        if (\defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check the user's permissions
        if (!\current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save the meta data
        if (isset($_POST['task_due_date'])) {
            \update_post_meta($post_id, '_task_due_date', \sanitize_text_field($_POST['task_due_date']));
        }

        if (isset($_POST['task_status'])) {
            \update_post_meta($post_id, '_task_status', \sanitize_text_field($_POST['task_status']));
        }
    }
}
