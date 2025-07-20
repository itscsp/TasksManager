<div class="tasks-manager-container">
    <div class="add-task-form">
        <h1 class="task-heading">Tasks</h1>
        <button id="open-add-task-modal" class="tasks-btn">Add Task</button>
    </div>

    <div class="today-date">
        <div class="time-box" id="today-date"></div>
        <div class="time-box" id="countdown"></div>
    </div>

    <?php include TASKS_PLUGIN_DIR . 'public/partials/UI/add-task-model.php'; ?>

    <div class="tasks-list">
        <?php
        // Today's tasks
        include TASKS_PLUGIN_DIR . 'public/partials/UI/todays-tasks.php';

        // Archive tasks
        include TASKS_PLUGIN_DIR . 'public/partials/UI/archive-tasks.php';

        // Future tasks
        include TASKS_PLUGIN_DIR . 'public/partials/UI/future-tasks.php';
        ?>
    </div>
</div>