<?php
namespace TasksManager\Tests\Unit;

use Brain\Monkey;
use PHPUnit\Framework\TestCase;

class TasksPublicTest extends TestCase {
    private $tasks_public;

    protected function setUp(): void {
        parent::setUp();
        Monkey\setUp();
        
        // Mock WordPress constants
        if (!defined('TASKS_PLUGIN_DIR')) {
            define('TASKS_PLUGIN_DIR', __DIR__ . '/../../');
        }
        if (!defined('TASKS_PLUGIN_URL')) {
            define('TASKS_PLUGIN_URL', 'http://blogchethanspoojarycom.local/wp-content/plugins/tasks/');
        }
        if (!defined('TASKS_VERSION')) {
            define('TASKS_VERSION', '1.0.0');
        }
        
        $this->tasks_public = new \Tasks_Public();
    }

    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
    }

    public function test_init_hooks_are_registered() {
        // Mock WordPress functions
        Monkey\Functions\expect('add_action')
            ->with('wp_enqueue_scripts', [\Mockery::type(\Tasks_Public::class), 'enqueue_scripts'])
            ->once();
        
        Monkey\Functions\expect('add_shortcode')
            ->with('tasks_manager', [\Mockery::type(\Tasks_Public::class), 'render_tasks_manager'])
            ->once();
        
        Monkey\Functions\expect('add_action')
            ->with('wp_ajax_add_task', [\Mockery::type(\Tasks_Public::class), 'handle_add_task'])
            ->once();
        
        Monkey\Functions\expect('add_action')
            ->with('wp_ajax_add_subtask', [\Mockery::type(\Tasks_Public::class), 'handle_add_subtask'])
            ->once();
        
        Monkey\Functions\expect('add_action')
            ->with('wp_ajax_update_task_status', [\Mockery::type(\Tasks_Public::class), 'handle_update_task_status'])
            ->once();
        
        Monkey\Functions\expect('add_action')
            ->with('wp_ajax_update_subtask_status', [\Mockery::type(\Tasks_Public::class), 'handle_update_subtask_status'])
            ->once();

        $this->tasks_public->init();
    }

    public function test_render_tasks_manager_requires_login() {
        // Mock user not logged in
        Monkey\Functions\expect('is_user_logged_in')
            ->once()
            ->andReturn(false);
        
        Monkey\Functions\expect('wp_login_url')
            ->once()
            ->with(\Mockery::type('string'))
            ->andReturn('http://blogchethanspoojarycom.local/wp-login.php');
        
        Monkey\Functions\expect('get_permalink')
            ->once()
            ->andReturn('http://blogchethanspoojarycom.local/tasks');

        $result = $this->tasks_public->render_tasks_manager();
        
        $this->assertStringContainsString('You must be logged in', $result);
        $this->assertStringContainsString('Login here', $result);
    }

    public function test_render_tasks_manager_for_logged_in_user() {
        // Mock user logged in
        Monkey\Functions\expect('is_user_logged_in')
            ->once()
            ->andReturn(true);

        // Create a mock partial file
        $partialPath = TASKS_PLUGIN_DIR . 'public/partials/tasks-manager.php';
        if (!file_exists(dirname($partialPath))) {
            mkdir(dirname($partialPath), 0755, true);
        }
        if (!file_exists($partialPath)) {
            file_put_contents($partialPath, '<div>Mock tasks manager</div>');
        }

        $result = $this->tasks_public->render_tasks_manager();
        
        $this->assertStringContainsString('Mock tasks manager', $result);
    }

    public function test_handle_add_task_security_check() {
        // Mock security failure
        $_POST['nonce'] = 'invalid_nonce';
        
        Monkey\Functions\expect('wp_verify_nonce')
            ->once()
            ->with('invalid_nonce', 'tasks_ajax_nonce')
            ->andReturn(false);
        
        Monkey\Functions\expect('is_user_logged_in')
            ->once()
            ->andReturn(true);
        
        Monkey\Functions\expect('wp_die')
            ->once()
            ->with('Security check failed');

        $this->tasks_public->handle_add_task();
    }

    public function test_handle_add_task_success() {
        // Mock successful security check
        $_POST['nonce'] = 'valid_nonce';
        $_POST['task_data'] = 'task_title=Test+Task&task_description=Test+Description&task_status=todo';
        
        Monkey\Functions\expect('wp_verify_nonce')
            ->once()
            ->with('valid_nonce', 'tasks_ajax_nonce')
            ->andReturn(true);
        
        Monkey\Functions\expect('is_user_logged_in')
            ->once()
            ->andReturn(true);
        
        Monkey\Functions\expect('sanitize_text_field')
            ->times(2)
            ->andReturnUsing(function($input) { return $input; });
        
        Monkey\Functions\expect('sanitize_textarea_field')
            ->once()
            ->andReturnUsing(function($input) { return $input; });
        
        Monkey\Functions\expect('get_current_user_id')
            ->once()
            ->andReturn(1);
        
        Monkey\Functions\expect('wp_insert_post')
            ->once()
            ->andReturn(123);
        
        Monkey\Functions\expect('update_post_meta')
            ->once()
            ->with(123, '_task_status', 'todo');
        
        Monkey\Functions\expect('wp_send_json_success')
            ->once()
            ->with('Task added successfully');

        $this->tasks_public->handle_add_task();
    }

    public function test_handle_add_subtask_success() {
        // Mock successful subtask addition
        $_POST['nonce'] = 'valid_nonce';
        $_POST['task_id'] = '123';
        $_POST['subtask_title'] = 'Test Subtask';
        $_POST['subtask_description'] = 'Test Description';
        
        Monkey\Functions\expect('wp_verify_nonce')
            ->once()
            ->with('valid_nonce', 'tasks_ajax_nonce')
            ->andReturn(true);
        
        Monkey\Functions\expect('is_user_logged_in')
            ->once()
            ->andReturn(true);
        
        Monkey\Functions\expect('sanitize_text_field')
            ->once()
            ->with('Test Subtask')
            ->andReturn('Test Subtask');
        
        Monkey\Functions\expect('sanitize_textarea_field')
            ->once()
            ->with('Test Description')
            ->andReturn('Test Description');
        
        Monkey\Functions\expect('get_post_meta')
            ->once()
            ->with(123, '_task_subtasks', true)
            ->andReturn([]);
        
        Monkey\Functions\expect('current_time')
            ->once()
            ->with('mysql')
            ->andReturn('2023-01-01 12:00:00');
        
        Monkey\Functions\expect('update_post_meta')
            ->once()
            ->with(123, '_task_subtasks', \Mockery::type('array'));
        
        Monkey\Functions\expect('wp_send_json_success')
            ->once()
            ->with('Subtask added successfully');

        $this->tasks_public->handle_add_subtask();
    }

    public function test_handle_update_task_status() {
        // Mock task status update
        $_POST['nonce'] = 'valid_nonce';
        $_POST['task_id'] = '123';
        $_POST['status'] = 'completed';
        
        Monkey\Functions\expect('wp_verify_nonce')
            ->once()
            ->with('valid_nonce', 'tasks_ajax_nonce')
            ->andReturn(true);
        
        Monkey\Functions\expect('is_user_logged_in')
            ->once()
            ->andReturn(true);
        
        Monkey\Functions\expect('sanitize_text_field')
            ->once()
            ->with('completed')
            ->andReturn('completed');
        
        Monkey\Functions\expect('update_post_meta')
            ->once()
            ->with(123, '_task_status', 'completed');
        
        Monkey\Functions\expect('wp_send_json_success')
            ->once()
            ->with('Task status updated');

        $this->tasks_public->handle_update_task_status();
    }

    public function test_handle_update_subtask_status() {
        // Mock subtask status update
        $_POST['nonce'] = 'valid_nonce';
        $_POST['task_id'] = '123';
        $_POST['subtask_index'] = '0';
        $_POST['status'] = 'completed';
        
        $existing_subtasks = [
            [
                'title' => 'Test Subtask',
                'description' => 'Test Description',
                'status' => 'todo',
                'created' => '2023-01-01 12:00:00'
            ]
        ];
        
        Monkey\Functions\expect('wp_verify_nonce')
            ->once()
            ->with('valid_nonce', 'tasks_ajax_nonce')
            ->andReturn(true);
        
        Monkey\Functions\expect('is_user_logged_in')
            ->once()
            ->andReturn(true);
        
        Monkey\Functions\expect('sanitize_text_field')
            ->once()
            ->with('completed')
            ->andReturn('completed');
        
        Monkey\Functions\expect('get_post_meta')
            ->once()
            ->with(123, '_task_subtasks', true)
            ->andReturn($existing_subtasks);
        
        Monkey\Functions\expect('current_time')
            ->once()
            ->with('mysql')
            ->andReturn('2023-01-01 13:00:00');
        
        Monkey\Functions\expect('update_post_meta')
            ->once()
            ->with(123, '_task_subtasks', \Mockery::type('array'));
        
        Monkey\Functions\expect('wp_send_json_success')
            ->once()
            ->with('Subtask status updated');

        $this->tasks_public->handle_update_subtask_status();
    }

    public function test_enqueue_scripts() {
        Monkey\Functions\expect('wp_enqueue_style')
            ->once()
            ->with('tasks-public', TASKS_PLUGIN_URL . 'assets/css/tasks-public.css', [], TASKS_VERSION);
        
        Monkey\Functions\expect('wp_enqueue_script')
            ->once()
            ->with('tasks-public', TASKS_PLUGIN_URL . 'assets/js/tasks-public.js', ['jquery'], TASKS_VERSION, true);
        
        Monkey\Functions\expect('wp_localize_script')
            ->once()
            ->with('tasks-public', 'tasksAjax', \Mockery::type('array'));
        
        Monkey\Functions\expect('admin_url')
            ->once()
            ->with('admin-ajax.php')
            ->andReturn('http://blogchethanspoojarycom.local/wp-admin/admin-ajax.php');
        
        Monkey\Functions\expect('wp_create_nonce')
            ->once()
            ->with('tasks_ajax_nonce')
            ->andReturn('test_nonce');

        $this->tasks_public->enqueue_scripts();
    }
}
