  <div class="task-item" data-status="<?php echo $status; ?>" data-task-id="<?php echo $task_id; ?>">
      <div class="task-meta">
          <p><?php echo get_the_term_list($task_id, 'project', '', ', '); ?></p>
      </div>
      <h3 class="task-item-heading"><?php the_title(); ?></h3>
      <div class="inner-container">

          <div class="task-content">
              <?php the_content(); ?>
          </div>

          <!-- Add Subtask Modal -->
          <div class="tasks-modal add-subtask-modal" id="add-subtask-modal-<?php echo $task_id; ?>" style="display:none;">
              <div class="tasks-modal-content">
                  <span class="tasks-modal-close close-add-subtask-modal" data-task-id="<?php echo $task_id; ?>">&times;</span>
                  <h4>Add Subtask</h4>
                  <form class="subtask-form" data-task-id="<?php echo $task_id; ?>">
                      <input type="text" name="subtask_title" placeholder="Subtask Title" required>
                      <textarea name="subtask_description" placeholder="Subtask Description" rows="2"></textarea>
                      <button type="submit" class="tasks-btn">Add Subtask</button>
                  </form>
              </div>
          </div>
          <?php include TASKS_PLUGIN_DIR . 'public/partials/UI/subtask-item.php'; ?>

      </div>


      <!-- Task Actions -->
      <div class="task-actions">
          <button class="tasks-btn open-add-subtask-modal" data-task-id="<?php echo $task_id; ?>">Add Subtask</button>
          <div class="status-checkbox-group" data-task-id="<?php echo $task_id; ?>">
              <label class="status-checkbox-label">
                  <input type="checkbox" class="status-checkbox" data-task-id="<?php echo $task_id; ?>" <?php if ($status === 'completed') echo 'checked'; ?>>
                  <span>Completed</span>
              </label>
          </div>
      </div>


  </div>