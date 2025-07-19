<?php

use PHPUnit\Framework\TestCase;

class TasksIntegrationTest extends TestCase {
    
    public function test_plugin_initialization_flow() {
        // Test the complete plugin initialization flow
        
        // 1. Test that main plugin file defines constants
        $this->assertTrue(defined('TASKS_VERSION'), 'Plugin should define version constant');
        $this->assertTrue(defined('TASKS_PLUGIN_DIR'), 'Plugin should define directory constant');
        $this->assertTrue(defined('TASKS_PLUGIN_URL'), 'Plugin should define URL constant');
        
        // 2. Test that loader class can be instantiated
        $loader = new Tasks_Loader();
        $this->assertInstanceOf('Tasks_Loader', $loader, 'Loader should be instantiable');
        
        // 3. Test that all required classes exist
        $this->assertTrue(class_exists('Tasks_Public'), 'Tasks_Public class should exist');
        $this->assertTrue(class_exists('Tasks_Admin'), 'Tasks_Admin class should exist');
        $this->assertTrue(class_exists('TasksManager\Tasks_Post_Type'), 'Tasks_Post_Type class should exist');
        $this->assertTrue(class_exists('TasksManager\Tasks_Taxonomy'), 'Tasks_Taxonomy class should exist');
    }
    
    public function test_tasks_public_instantiation() {
        // Test that Tasks_Public can be instantiated without errors
        $tasks_public = new Tasks_Public();
        $this->assertInstanceOf('Tasks_Public', $tasks_public, 'Tasks_Public should be instantiable');
        
        // Test that it has all required methods
        $this->assertTrue(method_exists($tasks_public, 'init'), 'Should have init method');
        $this->assertTrue(method_exists($tasks_public, 'enqueue_scripts'), 'Should have enqueue_scripts method');
        $this->assertTrue(method_exists($tasks_public, 'render_tasks_manager'), 'Should have render_tasks_manager method');
        $this->assertTrue(method_exists($tasks_public, 'handle_add_task'), 'Should have handle_add_task method');
        $this->assertTrue(method_exists($tasks_public, 'handle_add_subtask'), 'Should have handle_add_subtask method');
        $this->assertTrue(method_exists($tasks_public, 'handle_update_task_status'), 'Should have handle_update_task_status method');
        $this->assertTrue(method_exists($tasks_public, 'handle_update_subtask_status'), 'Should have handle_update_subtask_status method');
    }
    
    public function test_namespaced_classes_instantiation() {
        // Test that namespaced classes can be instantiated
        $post_type = new TasksManager\Tasks_Post_Type();
        $taxonomy = new TasksManager\Tasks_Taxonomy();
        
        $this->assertInstanceOf('TasksManager\Tasks_Post_Type', $post_type, 'Tasks_Post_Type should be instantiable');
        $this->assertInstanceOf('TasksManager\Tasks_Taxonomy', $taxonomy, 'Tasks_Taxonomy should be instantiable');
        
        // Test methods exist
        $this->assertTrue(method_exists($post_type, 'init'), 'Post type should have init method');
        $this->assertTrue(method_exists($post_type, 'register_post_type'), 'Post type should have register_post_type method');
        $this->assertTrue(method_exists($taxonomy, 'init'), 'Taxonomy should have init method');
        $this->assertTrue(method_exists($taxonomy, 'register_taxonomy'), 'Taxonomy should have register_taxonomy method');
    }
    
    public function test_ajax_handler_data_structure() {
        // Test that AJAX handlers expect proper data structure
        $tasks_public = new Tasks_Public();
        
        // Use reflection to test method signatures
        $reflection = new ReflectionClass($tasks_public);
        
        $add_task_method = $reflection->getMethod('handle_add_task');
        $add_subtask_method = $reflection->getMethod('handle_add_subtask');
        $update_status_method = $reflection->getMethod('handle_update_task_status');
        $update_subtask_method = $reflection->getMethod('handle_update_subtask_status');
        
        // Test that methods are public (can be called by WordPress AJAX system)
        $this->assertTrue($add_task_method->isPublic(), 'handle_add_task should be public');
        $this->assertTrue($add_subtask_method->isPublic(), 'handle_add_subtask should be public');
        $this->assertTrue($update_status_method->isPublic(), 'handle_update_task_status should be public');
        $this->assertTrue($update_subtask_method->isPublic(), 'handle_update_subtask_status should be public');
        
        // Test that methods don't require parameters (they use $_POST)
        $this->assertEquals(0, $add_task_method->getNumberOfRequiredParameters(), 'handle_add_task should not require parameters');
        $this->assertEquals(0, $add_subtask_method->getNumberOfRequiredParameters(), 'handle_add_subtask should not require parameters');
    }
    
    public function test_form_data_processing_structure() {
        // Test the structure of form data processing without actually processing
        $public_file_content = file_get_contents(TASKS_PLUGIN_DIR . 'public/class-tasks-public.php');
        
        // Test that form data is properly parsed
        $this->assertStringContainsString('parse_str', $public_file_content, 'Should parse form data');
        $this->assertStringContainsString('$_POST[\'task_data\']', $public_file_content, 'Should access task_data from POST');
        
        // Test that proper WordPress functions are used
        $this->assertStringContainsString('wp_insert_post', $public_file_content, 'Should use wp_insert_post');
        $this->assertStringContainsString('update_post_meta', $public_file_content, 'Should use update_post_meta');
        $this->assertStringContainsString('get_post_meta', $public_file_content, 'Should use get_post_meta');
        $this->assertStringContainsString('wp_send_json_success', $public_file_content, 'Should send JSON success response');
        $this->assertStringContainsString('wp_send_json_error', $public_file_content, 'Should send JSON error response');
    }
    
    public function test_subtask_data_structure() {
        // Test that subtask data is properly structured
        $public_file_content = file_get_contents(TASKS_PLUGIN_DIR . 'public/class-tasks-public.php');
        
        // Test subtask array structure
        $this->assertStringContainsString('title', $public_file_content, 'Subtask should have title');
        $this->assertStringContainsString('description', $public_file_content, 'Subtask should have description');
        $this->assertStringContainsString('status', $public_file_content, 'Subtask should have status');
        $this->assertStringContainsString('created', $public_file_content, 'Subtask should have created timestamp');
        
        // Test that subtasks are stored as array
        $this->assertStringContainsString('is_array($subtasks)', $public_file_content, 'Should check if subtasks is array');
        $this->assertStringContainsString('$subtasks[]', $public_file_content, 'Should append to subtasks array');
    }
    
    public function test_status_values() {
        // Test that valid status values are used
        $template_content = file_get_contents(TASKS_PLUGIN_DIR . 'public/partials/tasks-manager.php');
        $js_content = file_get_contents(TASKS_PLUGIN_DIR . 'assets/js/tasks-public.js');
        $css_content = file_get_contents(TASKS_PLUGIN_DIR . 'assets/css/tasks-public.css');
        
        // Test status values in template
        $this->assertStringContainsString('value="todo"', $template_content, 'Should have todo status');
        $this->assertStringContainsString('value="in-progress"', $template_content, 'Should have in-progress status');
        $this->assertStringContainsString('value="completed"', $template_content, 'Should have completed status');
        
        // Test status classes in CSS
        $this->assertStringContainsString('.task-status.todo', $css_content, 'CSS should have todo status class');
        $this->assertStringContainsString('.task-status.completed', $css_content, 'CSS should have completed status class');
        $this->assertStringContainsString('.task-status.in-progress', $css_content, 'CSS should have in-progress status class');
    }
    
    public function test_wordpress_integration_hooks() {
        // Test that proper WordPress integration hooks are used
        $loader_content = file_get_contents(TASKS_PLUGIN_DIR . 'includes/class-tasks-loader.php');
        $public_content = file_get_contents(TASKS_PLUGIN_DIR . 'public/class-tasks-public.php');
        $admin_content = file_get_contents(TASKS_PLUGIN_DIR . 'admin/class-tasks-admin.php');
        
        // Test action hooks
        $this->assertStringContainsString('add_action', $public_content, 'Public class should use add_action');
        $this->assertStringContainsString('add_action', $admin_content, 'Admin class should use add_action');
        
        // Test shortcode registration
        $this->assertStringContainsString('add_shortcode', $public_content, 'Should register shortcode');
        $this->assertStringContainsString('tasks_manager', $public_content, 'Should register tasks_manager shortcode');
    }
    
    public function test_plugin_structure_integrity() {
        // Test that the plugin structure is complete and coherent
        
        // Test main plugin file structure
        $main_content = file_get_contents(TASKS_PLUGIN_DIR . 'tasks.php');
        $this->assertStringContainsString('Plugin Name:', $main_content, 'Should have plugin header');
        $this->assertStringContainsString('tasks_init', $main_content, 'Should have init function');
        $this->assertStringContainsString('plugins_loaded', $main_content, 'Should hook into plugins_loaded');
        
        // Test that all required files are included
        $this->assertStringContainsString('class-tasks-loader.php', $main_content, 'Should include loader class');
        $this->assertStringContainsString('class-tasks-post-type.php', $main_content, 'Should include post type class');
        $this->assertStringContainsString('class-tasks-taxonomy.php', $main_content, 'Should include taxonomy class');
        $this->assertStringContainsString('class-tasks-admin.php', $main_content, 'Should include admin class');
        $this->assertStringContainsString('class-tasks-public.php', $main_content, 'Should include public class');
    }
    
    public function test_error_handling_structure() {
        // Test that proper error handling is in place
        $public_content = file_get_contents(TASKS_PLUGIN_DIR . 'public/class-tasks-public.php');
        $js_content = file_get_contents(TASKS_PLUGIN_DIR . 'assets/js/tasks-public.js');
        
        // Test PHP error handling
        $this->assertStringContainsString('wp_die', $public_content, 'Should handle fatal errors');
        $this->assertStringContainsString('wp_send_json_error', $public_content, 'Should send error responses');
        
        // Test JavaScript error handling
        $this->assertStringContainsString('error:', $js_content, 'JS should handle AJAX errors');
        $this->assertStringContainsString('alert', $js_content, 'JS should show user-friendly errors');
        $this->assertStringContainsString('complete:', $js_content, 'JS should handle completion');
    }
}
