<?php
/**
 * Status meta box template
 *
 * @package TasksManager
 */

if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}
?>
<select name="task_status" id="task_status">
    <option value="todo" <?php selected($status, 'todo'); ?>><?php _e('Todo', 'tasks-manager'); ?></option>
    <option value="completed" <?php selected($status, 'completed'); ?>><?php _e('Completed', 'tasks-manager'); ?></option>
</select>
