      <!-- Subtasks List -->
      <?php if (!empty($subtasks)) : ?>
          <div class="subtasks">
              <h4>Subtasks (<?php echo count($subtasks); ?>):</h4>
              <ul>
                  <?php foreach ($subtasks as $index => $subtask) : ?>
                      <li class="subtask-item">
                          <div class="subtask-content">
                              <strong><?php echo esc_html($subtask['title']); ?></strong>
                              <?php if (!empty($subtask['description'])) : ?>
                                  <p><?php echo esc_html($subtask['description']); ?></p>
                              <?php endif; ?>
                              <small>Status: <span class="task-status <?php echo $subtask['status']; ?>"><?php echo ucfirst(str_replace('-', ' ', $subtask['status'])); ?></span></small>
                          </div>
                          <div class="subtask-actions">
                              <select class="subtask-status-selector" data-task-id="<?php echo $task_id; ?>" data-subtask-index="<?php echo $index; ?>">
                                  <option value="todo" <?php selected($subtask['status'], 'todo'); ?>>Todo</option>
                                  <option value="in-progress" <?php selected($subtask['status'], 'in-progress'); ?>>In Progress</option>
                                  <option value="completed" <?php selected($subtask['status'], 'completed'); ?>>Completed</option>
                              </select>
                          </div>
                      </li>
                  <?php endforeach; ?>
              </ul>
          </div>
      <?php endif; ?>