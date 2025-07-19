<?php
namespace TasksManager\Tests\Integration;

use Brain\Monkey;
use PHPUnit\Framework\TestCase;

class TasksAjaxIntegrationTest extends TestCase {
    private $tasks_public;

    protected function setUp(): void {
        parent::setUp();
        Monkey\setUp();
        
        // Mock WordPress environment
        $this->mockWordPressEnvironment();
        
        $this->tasks_public = new \Tasks_Public();
    }

    protected function tearDown(): void {
        Monkey\tearDown();
        parent::tearDown();
        
        // Clean up $_POST
        $_POST = [];
    }

    private function mockWordPressEnvironment() {
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

        // Mock common WordPress functions
        Monkey\Functions\when('add_action')->justReturn(true);
        Monkey\Functions\when('add_shortcode')->justReturn(true);
        Monkey\Functions\when('wp_enqueue_style')->justReturn(true);
        Monkey\Functions\when('wp_enqueue_script')->justReturn(true);
        Monkey\Functions\when('wp_localize_script')->justReturn(true);
        Monkey\Functions\when('admin_url')->justReturn('http://blogchethanspoojarycom.local/wp-admin/admin-ajax.php');
        Monkey\Functions\when('wp_create_nonce')->justReturn('test_nonce');
    }

    public function test_complete_task_workflow() {
        // Test 1: Add a task
        $_POST = [
            'nonce' => 'valid_nonce',
            'task_data' => 'task_title=Integration+Test+Task&task_description=Test+Description&task_status=todo&task_project=1'
        ];

        Monkey\Functions\expect('wp_verify_nonce')->andReturn(true);
        Monkey\Functions\expect('is_user_logged_in')->andReturn(true);
        Monkey\Functions\expect('sanitize_text_field')->andReturnUsing(function($input) { return $input; });
        Monkey\Functions\expect('sanitize_textarea_field')->andReturnUsing(function($input) { return $input; });
        Monkey\Functions\expect('get_current_user_id')->andReturn(1);
        Monkey\Functions\expect('wp_insert_post')->andReturn(100);
        Monkey\Functions\expect('update_post_meta')->andReturn(true);
        Monkey\Functions\expect('wp_set_post_terms')->andReturn(true);
        Monkey\Functions\expect('wp_send_json_success')->with('Task added successfully');

        $this->tasks_public->handle_add_task();

        // Test 2: Add a subtask to the created task
        $_POST = [
            'nonce' => 'valid_nonce',
            'task_id' => '100',
            'subtask_title' => 'Test Subtask',
            'subtask_description' => 'Subtask Description'
        ];

        Monkey\Functions\expect('wp_verify_nonce')->andReturn(true);
        Monkey\Functions\expect('is_user_logged_in')->andReturn(true);
        Monkey\Functions\expect('sanitize_text_field')->with('Test Subtask')->andReturn('Test Subtask');
        Monkey\Functions\expect('sanitize_textarea_field')->with('Subtask Description')->andReturn('Subtask Description');
        Monkey\Functions\expect('get_post_meta')->with(100, '_task_subtasks', true)->andReturn([]);
        Monkey\Functions\expect('current_time')->with('mysql')->andReturn('2023-01-01 12:00:00');
        Monkey\Functions\expect('update_post_meta')->with(100, '_task_subtasks', \Mockery::type('array'))->andReturn(true);
        Monkey\Functions\expect('wp_send_json_success')->with('Subtask added successfully');

        $this->tasks_public->handle_add_subtask();

        // Test 3: Update task status
        $_POST = [
            'nonce' => 'valid_nonce',
            'task_id' => '100',
            'status' => 'in-progress'
        ];

        Monkey\Functions\expect('wp_verify_nonce')->andReturn(true);
        Monkey\Functions\expect('is_user_logged_in')->andReturn(true);
        Monkey\Functions\expect('sanitize_text_field')->with('in-progress')->andReturn('in-progress');
        Monkey\Functions\expect('update_post_meta')->with(100, '_task_status', 'in-progress')->andReturn(true);
        Monkey\Functions\expect('wp_send_json_success')->with('Task status updated');

        $this->tasks_public->handle_update_task_status();

        $this->assertTrue(true); // If we reach here, all tests passed
    }

    public function test_security_validation() {
        // Test invalid nonce
        $_POST = ['nonce' => 'invalid_nonce'];
        
        Monkey\Functions\expect('wp_verify_nonce')->with('invalid_nonce', 'tasks_ajax_nonce')->andReturn(false);
        Monkey\Functions\expect('is_user_logged_in')->andReturn(true);
        Monkey\Functions\expect('wp_die')->with('Security check failed');

        $this->tasks_public->handle_add_task();

        // Test user not logged in
        $_POST = ['nonce' => 'valid_nonce'];
        
        Monkey\Functions\expect('wp_verify_nonce')->with('valid_nonce', 'tasks_ajax_nonce')->andReturn(true);
        Monkey\Functions\expect('is_user_logged_in')->andReturn(false);
        Monkey\Functions\expect('wp_die')->with('Security check failed');

        $this->tasks_public->handle_add_task();
    }

    public function test_subtask_status_update_with_invalid_index() {
        $_POST = [
            'nonce' => 'valid_nonce',
            'task_id' => '100',
            'subtask_index' => '5', // Invalid index
            'status' => 'completed'
        ];

        Monkey\Functions\expect('wp_verify_nonce')->andReturn(true);
        Monkey\Functions\expect('is_user_logged_in')->andReturn(true);
        Monkey\Functions\expect('sanitize_text_field')->andReturn('completed');
        Monkey\Functions\expect('get_post_meta')->with(100, '_task_subtasks', true)->andReturn([
            ['title' => 'Subtask 1', 'status' => 'todo']
        ]); // Only 1 subtask, so index 5 is invalid
        Monkey\Functions\expect('wp_send_json_error')->with('Subtask not found');

        $this->tasks_public->handle_update_subtask_status();
    }

    public function test_user_access_control() {
        // Test that non-logged-in users get login prompt
        Monkey\Functions\expect('is_user_logged_in')->andReturn(false);
        Monkey\Functions\expect('wp_login_url')->andReturn('http://blogchethanspoojarycom.local/wp-login.php');
        Monkey\Functions\expect('get_permalink')->andReturn('http://blogchethanspoojarycom.local/tasks');

        $result = $this->tasks_public->render_tasks_manager();
        
        $this->assertStringContainsString('You must be logged in', $result);
        $this->assertStringContainsString('tasks-login-required', $result);
    }

    public function test_form_data_validation() {
        // Test with missing required fields
        $_POST = [
            'nonce' => 'valid_nonce',
            'task_data' => 'task_description=Only+description+no+title&task_status=todo'
        ];

        Monkey\Functions\expect('wp_verify_nonce')->andReturn(true);
        Monkey\Functions\expect('is_user_logged_in')->andReturn(true);
        Monkey\Functions\expect('sanitize_text_field')->andReturnUsing(function($input) { return $input; });
        Monkey\Functions\expect('sanitize_textarea_field')->andReturnUsing(function($input) { return $input; });
        Monkey\Functions\expect('get_current_user_id')->andReturn(1);

        // WordPress should handle missing title gracefully
        Monkey\Functions\expect('wp_insert_post')->andReturn(false); // Simulate failure
        Monkey\Functions\expect('wp_send_json_error')->with('Failed to add task');

        $this->tasks_public->handle_add_task();
    }
}
