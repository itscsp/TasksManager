<?php
/**
 * Subtasks meta box template
 *
 * @package TasksManager
 */

if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}
?>
<div id="subtasks_container">
    <?php foreach ($subtasks as $index => $subtask): ?>
        <div class="subtask-item">
            <p>
                <input type="text" name="subtask_title[]" 
                    placeholder="<?php esc_attr_e('Subtask Title', 'tasks-manager'); ?>" 
                    value="<?php echo esc_attr($subtask['title']); ?>" 
                    style="width: 100%;">
            </p>
            <p>
                <textarea name="subtask_description[]" 
                    placeholder="<?php esc_attr_e('Subtask Description', 'tasks-manager'); ?>" 
                    style="width: 100%;"><?php echo esc_textarea($subtask['description']); ?></textarea>
            </p>
            <p>
                <select name="subtask_status[]">
                    <option value="todo" <?php selected($subtask['status'], 'todo'); ?>><?php _e('Todo', 'tasks-manager'); ?></option>
                    <option value="completed" <?php selected($subtask['status'], 'completed'); ?>><?php _e('Completed', 'tasks-manager'); ?></option>
                </select>
                <button type="button" class="button remove-subtask"><?php _e('Remove Subtask', 'tasks-manager'); ?></button>
            </p>
        </div>
    <?php endforeach; ?>
</div>
<button type="button" class="button" id="add_subtask"><?php _e('Add Subtask', 'tasks-manager'); ?></button>
