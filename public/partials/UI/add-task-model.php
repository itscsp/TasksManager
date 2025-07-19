    <!-- Add Task Modal -->
    <div id="add-task-modal" class="tasks-modal" style="display:none;">
        <div class="tasks-modal-content">
            <span class="tasks-modal-close" id="close-add-task-modal">&times;</span>
            <h2>Add New Task</h2>
            <form id="new-task-form">
                <p>
                    <label for="task_title">Title:</label>
                    <input type="text" id="task_title" name="task_title" required>
                </p>
                <p>
                    <label for="task_description">Description:</label>
                    <textarea id="task_description" name="task_description" rows="4"></textarea>
                </p>
                <p>
                    <label for="task_project">Project:</label>
                    <?php
                    wp_dropdown_categories(array(
                        'taxonomy' => 'project',
                        'name' => 'task_project',
                        'show_option_none' => 'Select Project',
                        'option_none_value' => '',
                        'hide_empty' => false,
                    ));
                    ?>
                </p>
                <p>
                    <label for="task_status">Status:</label>
                    <select id="task_status" name="task_status">
                        <option value="todo">Todo</option>
                        <option value="in-progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </p>
                <button type="submit" class="button">Add Task</button>
            </form>
        </div>
    </div>