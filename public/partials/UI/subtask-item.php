      <!-- Subtasks List -->
      <?php if (!empty($subtasks)) : ?>
          <div class="subtasks">
              <ul>
                  <?php foreach ($subtasks as $index => $subtask) : ?>
                      <li class="subtask-item">
                          <div class="subtask-content">
                              <strong><?php echo esc_html($subtask['title']); ?></strong>
                              <?php if (!empty($subtask['description'])) : ?>
                                  <p><?php echo nl2br(esc_html($subtask['description'])); ?></p>
                              <?php endif; ?>
                              <div class="subtask-actions-container">
                                  <small>Status: <span class="task-status <?php echo $subtask['status']; ?>"><?php echo ucfirst(str_replace('-', ' ', $subtask['status'])); ?></span></small>
  <?php if (!isset($show_add_subtask) || $show_add_subtask !== false): ?>
                                   <div class="subtask-actions">
                              <label class="custom-checkbox">
                                  <input type="checkbox" 
                                         class="subtask-status-checkbox" 
                                         data-task-id="<?php echo $task_id; ?>" 
                                         data-subtask-index="<?php echo $index; ?>"
                                         <?php echo ($subtask['status'] === 'completed') ? 'checked' : ''; ?>>
                                         
                                  <svg class="custom-checkbox-check" stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="18" width="18" xmlns="http://www.w3.org/2000/svg">
                                      <path d="M17.47 250.9C88.82 328.1 158 397.6 224.5 485.5c72.3-143.8 146.3-288.1 268.4-444.37L460 26.06C356.9 135.4 276.8 238.9 207.2 361.9c-48.4-43.6-126.62-105.3-174.38-137z"></path>
                                  </svg>
                              </label>
                          </div>
                          <?php endif; ?>
                                </div>
                          </div>
                      </li>
                  <?php endforeach; ?>
              </ul>
          </div>
      <?php endif; ?>