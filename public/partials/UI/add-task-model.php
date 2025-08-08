    <!-- Add Task Modal -->
    <div id="add-task-modal" class="tasks-modal" style="display:none;">
        <div class="tasks-modal-content">
            <span class="tasks-modal-close" id="close-add-task-modal">
                <svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="22px" width="22px" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M368 368 144 144m224 0L144 368"></path></svg>
            </span>
            <h2 class="heading">Add New Task</h2>
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
                    <label for="task_start_date">Start Date:</label>
                    <input type="text" id="task_start_date" name="task_start_date" placeholder="Select start date" required>
                </p>
                <p>
                    <label for="task_end_date">End Date:</label>
                    <input type="text" id="task_end_date" name="task_end_date" placeholder="Select end date" required>
                </p>
                <div class="new-task-btns">
                    <p class="tsk-projects">
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
                    <div class="add-form-btn">
                        <button type="submit" class="tasks-btn ">Add Task</button>
                    </div>
                </div>
            </form>
        </div>
    </div>