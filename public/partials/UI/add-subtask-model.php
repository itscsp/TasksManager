<?php
/**
 * Template for Add Subtask Modal
 */
?>
<!-- Add Subtask Modal -->
<div id="add-subtask-modal-<?php echo $task_id; ?>" class="tasks-modal" style="display:none;">
    <div class="tasks-modal-content">
        <span class="tasks-modal-close close-add-subtask-modal" data-task-id="<?php echo $task_id; ?>">
            <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="22px" width="22px" xmlns="http://www.w3.org/2000/svg">
                <path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M368 368 144 144m224 0L144 368"></path>
            </svg>
        </span>
        <h2 class="heading">Add New Subtask</h2>
        <form class="subtask-form" data-task-id="<?php echo $task_id; ?>">
            <p>
                <label for="subtask_title_<?php echo $task_id; ?>">Title:</label>
                <input type="text" 
                       id="subtask_title_<?php echo $task_id; ?>" 
                       name="subtask_title" 
                       required>
            </p>
            <p>
                <label for="subtask_description_<?php echo $task_id; ?>">Description:</label>
                <textarea id="subtask_description_<?php echo $task_id; ?>" 
                          name="subtask_description" 
                          rows="4"></textarea>
            </p>
            <div class="new-task-btns">
                <div class="add-form-btn">
                    <button type="submit" class="tasks-btn">Add Subtask</button>
                </div>
            </div>
        </form>
    </div>
</div>
