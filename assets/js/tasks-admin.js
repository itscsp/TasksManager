jQuery(document).ready(function($) {
    $('#add_subtask').click(function() {
        var template = `
            <div class="subtask-item">
                <p>
                    <input type="text" name="subtask_title[]" placeholder="Subtask Title" style="width: 100%;">
                </p>
                <p>
                    <textarea name="subtask_description[]" placeholder="Subtask Description" style="width: 100%;"></textarea>
                </p>
                <p>
                    <select name="subtask_status[]">
                        <option value="todo">Todo</option>
                        <option value="completed">Completed</option>
                    </select>
                    <button type="button" class="button remove-subtask">Remove Subtask</button>
                </p>
            </div>
        `;
        $('#subtasks_container').append(template);
    });

    $(document).on('click', '.remove-subtask', function() {
        $(this).closest('.subtask-item').remove();
    });
});
