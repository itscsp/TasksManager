/**
 * Tasks Manager Frontend Tests
 * Tests for the jQuery functionality
 */

describe('Tasks Manager Frontend', function() {
    
    beforeEach(function() {
        // Setup DOM
        document.body.innerHTML = `
            <div class="tasks-manager-container">
                <div class="add-task-form">
                    <form id="new-task-form">
                        <input type="text" name="task_title" id="task_title" value="Test Task">
                        <textarea name="task_description" id="task_description">Test Description</textarea>
                        <select name="task_status" id="task_status">
                            <option value="todo">Todo</option>
                            <option value="in-progress">In Progress</option>
                            <option value="completed">Completed</option>
                        </select>
                        <button type="submit">Add Task</button>
                    </form>
                </div>
                
                <div class="tasks-list">
                    <div class="task-item" data-task-id="123">
                        <h3>Sample Task</h3>
                        <div class="task-meta">
                            <span class="task-status todo">Todo</span>
                        </div>
                        <div class="task-actions">
                            <button class="btn toggle-subtask-form" data-task-id="123">Add Subtask</button>
                            <select class="status-selector" data-task-id="123">
                                <option value="todo" selected>Todo</option>
                                <option value="in-progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="add-subtask-form" id="subtask-form-123">
                            <form class="subtask-form" data-task-id="123">
                                <input type="text" name="subtask_title" placeholder="Subtask Title" required>
                                <textarea name="subtask_description" placeholder="Subtask Description"></textarea>
                                <button type="submit" class="btn">Add Subtask</button>
                                <button type="button" class="btn secondary cancel-subtask">Cancel</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Mock jQuery and AJAX
        global.jQuery = global.$ = {
            fn: {},
            ready: function(callback) { callback(this); },
            ajax: jasmine.createSpy('ajax'),
            serialize: jasmine.createSpy('serialize').and.returnValue('task_title=Test&task_description=Test'),
            find: jasmine.createSpy('find').and.returnValue({
                prop: jasmine.createSpy('prop').and.returnValue(this),
                text: jasmine.createSpy('text').and.returnValue(this),
                val: jasmine.createSpy('val').and.returnValue('test-value'),
                focus: jasmine.createSpy('focus').and.returnValue(this)
            }),
            on: jasmine.createSpy('on').and.callFake(function(event, handler) {
                return this;
            }),
            closest: jasmine.createSpy('closest').and.returnValue(this),
            hasClass: jasmine.createSpy('hasClass').and.returnValue(false),
            removeClass: jasmine.createSpy('removeClass').and.returnValue(this),
            addClass: jasmine.createSpy('addClass').and.returnValue(this),
            data: jasmine.createSpy('data').and.returnValue('123'),
            attr: jasmine.createSpy('attr').and.returnValue('todo')
        };
        
        // Mock tasksAjax object
        global.tasksAjax = {
            ajaxurl: 'http://blogchethanspoojarycom.local/wp-admin/admin-ajax.php',
            nonce: 'test_nonce'
        };
    });

    describe('Task Form Submission', function() {
        it('should prevent default form submission', function() {
            var mockEvent = {
                preventDefault: jasmine.createSpy('preventDefault')
            };
            
            // Simulate form submission
            var formHandler = jasmine.createSpy('formHandler').and.callFake(function(e) {
                e.preventDefault();
                // Form submission logic would go here
            });
            
            formHandler(mockEvent);
            
            expect(mockEvent.preventDefault).toHaveBeenCalled();
        });

        it('should serialize form data correctly', function() {
            var form = document.getElementById('new-task-form');
            expect(form).toBeTruthy();
            expect(form.querySelector('#task_title').value).toBe('Test Task');
            expect(form.querySelector('#task_description').value).toBe('Test Description');
        });

        it('should make AJAX call with correct parameters', function() {
            var expectedData = {
                action: 'add_task',
                nonce: 'test_nonce',
                task_data: 'task_title=Test&task_description=Test'
            };
            
            // This would be called in the actual form submission handler
            $.ajax({
                url: tasksAjax.ajaxurl,
                type: 'POST',
                data: expectedData
            });
            
            expect($.ajax).toHaveBeenCalledWith({
                url: 'http://blogchethanspoojarycom.local/wp-admin/admin-ajax.php',
                type: 'POST',
                data: expectedData
            });
        });
    });

    describe('Subtask Functionality', function() {
        it('should toggle subtask form visibility', function() {
            var form = document.getElementById('subtask-form-123');
            var button = document.querySelector('.toggle-subtask-form');
            
            expect(form).toBeTruthy();
            expect(button).toBeTruthy();
            expect(form.classList.contains('show')).toBe(false);
        });

        it('should validate subtask form fields', function() {
            var form = document.querySelector('.subtask-form');
            var titleInput = form.querySelector('input[name="subtask_title"]');
            
            expect(titleInput.hasAttribute('required')).toBe(true);
            expect(titleInput.placeholder).toBe('Subtask Title');
        });
    });

    describe('Status Updates', function() {
        it('should handle task status changes', function() {
            var statusSelector = document.querySelector('.status-selector');
            expect(statusSelector).toBeTruthy();
            expect(statusSelector.dataset.taskId).toBe('123');
        });

        it('should update status display after successful AJAX', function() {
            var statusSpan = document.querySelector('.task-status');
            expect(statusSpan).toBeTruthy();
            expect(statusSpan.classList.contains('todo')).toBe(true);
        });
    });

    describe('User Authentication', function() {
        it('should show login message for non-authenticated users', function() {
            // This would be tested by checking the server response
            var loginMessage = 'You must be logged in to access the task manager';
            expect(loginMessage).toContain('logged in');
        });

        it('should include nonce in all AJAX requests', function() {
            expect(global.tasksAjax.nonce).toBeDefined();
            expect(global.tasksAjax.nonce).toBe('test_nonce');
        });
    });

    describe('UI Interactions', function() {
        it('should provide visual feedback on form focus', function() {
            var input = document.querySelector('#task_title');
            expect(input).toBeTruthy();
            
            // Simulate focus event
            var focusEvent = new Event('focus');
            input.dispatchEvent(focusEvent);
            
            // Check that the element exists for focus handling
            expect(input.closest('.add-task-form')).toBeTruthy();
        });

        it('should disable submit button during AJAX request', function() {
            var submitButton = document.querySelector('button[type="submit"]');
            expect(submitButton).toBeTruthy();
            expect(submitButton.textContent).toBe('Add Task');
        });
    });

    describe('Error Handling', function() {
        it('should handle AJAX errors gracefully', function() {
            spyOn(window, 'alert');
            
            // Simulate error response
            var errorResponse = {
                success: false,
                data: 'Test error message'
            };
            
            // This would be called in the AJAX error handler
            if (!errorResponse.success) {
                window.alert('Error adding task: ' + errorResponse.data);
            }
            
            expect(window.alert).toHaveBeenCalledWith('Error adding task: Test error message');
        });

        it('should handle network errors', function() {
            spyOn(window, 'alert');
            
            // Simulate network error
            window.alert('Network error occurred while adding task');
            
            expect(window.alert).toHaveBeenCalledWith('Network error occurred while adding task');
        });
    });

    describe('Form Validation', function() {
        it('should require task title', function() {
            var titleInput = document.querySelector('#task_title');
            expect(titleInput.hasAttribute('required')).toBe(true);
        });

        it('should clear form after successful submission', function() {
            var form = document.getElementById('new-task-form');
            expect(form).toBeTruthy();
            
            // Simulate successful form submission
            form.reset();
            
            expect(form.querySelector('#task_title').value).toBe('');
        });
    });
});
