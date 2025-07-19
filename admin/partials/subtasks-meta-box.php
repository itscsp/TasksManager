<div id="subtasks_container">
    <?php foreach ($subtasks as $index => $subtask): ?>
        <div class="subtask-item">
            <p>
                <input type="text" name="subtask_title[]" placeholder="Subtask Title" value="<?php echo esc_attr($subtask['title']); ?>" style="width: 100%;">
            </p>
            <p>
                <textarea name="subtask_description[]" placeholder="Subtask Description" style="width: 100%;"><?php echo esc_textarea($subtask['description']); ?></textarea>
            </p>
            <p>
                <select name="subtask_status[]">
                    <option value="todo" <?php selected($subtask['status'], 'todo'); ?>>Todo</option>
                    <option value="completed" <?php selected($subtask['status'], 'completed'); ?>>Completed</option>
                </select>
                <button type="button" class="button remove-subtask">Remove Subtask</button>
            </p>
        </div>
    <?php endforeach; ?>
</div>
<button type="button" class="button" id="add_subtask">Add Subtask</button>
