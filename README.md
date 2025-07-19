# Tasks Manager - WordPress Plugin

A modern, user-friendly WordPress plugin to manage daily tasks, projects, and subtasks. Built for productivity, security, and extensibility.

## Features

- **Task Management**: Create, edit, and view tasks with title, description, project, and status.
- **Subtasks**: Add unlimited subtasks to any task, each with its own status and description.
- **Project Support**: Organize tasks under projects (custom taxonomy).
- **Status Tracking**: Mark tasks and subtasks as Todo, In Progress, or Completed.
- **User Access Control**: Only logged-in users can access the task manager. Each user sees only their own tasks.
- **AJAX-Powered UI**: Add tasks and subtasks, and update statuses without page reloads.
- **Dark Theme**: Beautiful, modern black UI with blue highlights and responsive design.
- **Security**: Nonce validation, input sanitization, and user authentication for all actions.
- **Shortcode**: Easily embed the task manager anywhere with `[tasks_manager]`.
- **Admin & Public Separation**: Clean codebase with clear separation of admin and public logic.
- **Hooks & Extensibility**: Built with WordPress best practices for easy extension.

## Installation

1. Upload the plugin folder to your `/wp-content/plugins/` directory.
2. Activate the plugin through the WordPress admin.
3. Add the `[tasks_manager]` shortcode to any page or post.

## Usage

- **Access**: Only logged-in users can view and manage tasks.
- **Add Task**: Use the form at the top to add a new task. Assign it to a project and set its status.
- **Add Subtask**: Click "Add Subtask" on any task to add a subtask. Subtasks can be marked as Todo, In Progress, or Completed.
- **Update Status**: Change the status of tasks and subtasks using the dropdown menus.
- **Projects**: Projects are managed as a custom taxonomy. Assign tasks to projects for better organization.

## Shortcode

Add the following shortcode to any page or post to display the task manager:

```
[tasks_manager]
```

## Security

- All AJAX actions are protected by WordPress nonces.
- Only authenticated users can add or update tasks and subtasks.
- All user input is sanitized before saving.
- Users can only see and manage their own tasks.

## Customization

- **CSS**: Edit `assets/css/tasks-public.css` for custom styles.
- **JS**: Edit `assets/js/tasks-public.js` for custom frontend logic.
- **Templates**: Edit `public/partials/tasks-manager.php` for markup changes.
- **Hooks**: Use WordPress hooks to extend or modify plugin behavior.

## Developer Notes

- Follows WordPress coding standards.
- Uses OOP for all main functionality.
- Activation and deactivation hooks included.
- All main classes are autoloaded and namespaced where appropriate.
- Includes unit and integration tests (see `tests/` folder).

## Testing

- Run `./vendor/bin/phpunit` to execute PHP tests.
- JavaScript tests are in `tests/js/` and can be run with Jest or Jasmine.
- See `TESTING.md` for more details.

## File Structure

- `tasks.php` - Main plugin file
- `includes/` - Core classes (loader, post type, taxonomy)
- `admin/` - Admin-specific classes and meta boxes
- `public/` - Public-facing classes, templates, and assets
- `assets/` - CSS and JS for frontend and admin
- `tests/` - Unit, integration, and JS tests

## Credits

- Author: Chethan S Poojary ([chethanspoojary.com](https://chethanspoojary.com))
- GitHub: [https://github.com/tasks-manager](https://github.com/tasks-manager)

## License

GPL-2.0+

---

For support or feature requests, please open an issue on GitHub.
