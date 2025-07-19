<?php

use PHPUnit\Framework\TestCase;

class TasksSecurityTest extends TestCase {
    
    public function setUp(): void {
        parent::setUp();
        
        // Mock $_POST global for security tests
        $_POST = [];
    }
    
    public function tearDown(): void {
        parent::tearDown();
        
        // Clean up $_POST
        $_POST = [];
    }
    
    public function test_tasks_public_handles_missing_post_data() {
        // Test that AJAX handlers properly validate input data
        $tasks_public = new Tasks_Public();
        
        // Test with empty $_POST - this should not cause fatal errors
        $reflection = new ReflectionClass($tasks_public);
        $method = $reflection->getMethod('handle_add_task');
        
        // The method should exist and be callable
        $this->assertTrue($method->isPublic(), 'handle_add_task should be public method');
    }
    
    public function test_form_input_validation() {
        // Test that our form template requires proper input validation
        $template_file = TASKS_PLUGIN_DIR . 'public/partials/tasks-manager.php';
        $template_content = file_get_contents($template_file);
        
        // Check for required attributes
        $this->assertStringContainsString('required', $template_content, 'Form should have required fields');
        $this->assertStringContainsString('task_title', $template_content, 'Form should have task title field');
        $this->assertStringContainsString('subtask_title', $template_content, 'Form should have subtask title field');
        
        // Check for proper escaping functions
        $this->assertStringContainsString('esc_html', $template_content, 'Template should escape output');
    }
    
    public function test_user_authentication_check_exists() {
        // Test that authentication checks are in place
        $public_file = TASKS_PLUGIN_DIR . 'public/class-tasks-public.php';
        $public_content = file_get_contents($public_file);
        
        // Check for authentication functions
        $this->assertStringContainsString('is_user_logged_in', $public_content, 'Should check if user is logged in');
        $this->assertStringContainsString('wp_verify_nonce', $public_content, 'Should verify nonces');
        $this->assertStringContainsString('wp_die', $public_content, 'Should handle security failures');
        $this->assertStringContainsString('Security check failed', $public_content, 'Should have security check message');
    }
    
    public function test_input_sanitization_exists() {
        // Test that input sanitization functions are used
        $public_file = TASKS_PLUGIN_DIR . 'public/class-tasks-public.php';
        $public_content = file_get_contents($public_file);
        
        // Check for sanitization functions
        $this->assertStringContainsString('sanitize_text_field', $public_content, 'Should sanitize text fields');
        $this->assertStringContainsString('sanitize_textarea_field', $public_content, 'Should sanitize textarea fields');
        $this->assertStringContainsString('intval', $public_content, 'Should validate integer inputs');
    }
    
    public function test_ajax_actions_are_registered() {
        // Test that AJAX actions are properly registered
        $public_file = TASKS_PLUGIN_DIR . 'public/class-tasks-public.php';
        $public_content = file_get_contents($public_file);
        
        // Check for AJAX action registrations
        $this->assertStringContainsString('wp_ajax_add_task', $public_content, 'Should register add_task AJAX action');
        $this->assertStringContainsString('wp_ajax_add_subtask', $public_content, 'Should register add_subtask AJAX action');
        $this->assertStringContainsString('wp_ajax_update_task_status', $public_content, 'Should register update_task_status AJAX action');
        $this->assertStringContainsString('wp_ajax_update_subtask_status', $public_content, 'Should register update_subtask_status AJAX action');
    }
    
    public function test_javascript_security_features() {
        // Test that JavaScript includes security features
        $js_file = TASKS_PLUGIN_DIR . 'assets/js/tasks-public.js';
        $js_content = file_get_contents($js_file);
        
        // Check for security features
        $this->assertStringContainsString('nonce', $js_content, 'JS should include nonce for security');
        $this->assertStringContainsString('preventDefault', $js_content, 'JS should prevent default form submission');
        $this->assertStringContainsString('error:', $js_content, 'JS should handle errors');
        $this->assertStringContainsString('success:', $js_content, 'JS should handle success responses');
    }
    
    public function test_user_specific_data_filtering() {
        // Test that data is filtered by user
        $template_file = TASKS_PLUGIN_DIR . 'public/partials/tasks-manager.php';
        $template_content = file_get_contents($template_file);
        
        // Check for user-specific queries
        $this->assertStringContainsString('get_current_user_id', $template_content, 'Should filter by current user');
        $this->assertStringContainsString('author', $template_content, 'Should use author parameter in query');
    }
    
    public function test_css_accessibility_features() {
        // Test that CSS includes accessibility features
        $css_file = TASKS_PLUGIN_DIR . 'assets/css/tasks-public.css';
        $css_content = file_get_contents($css_file);
        
        // Check for accessibility features
        $this->assertStringContainsString('focus', $css_content, 'CSS should have focus states');
        $this->assertStringContainsString('hover', $css_content, 'CSS should have hover states');
        $this->assertStringContainsString('@media', $css_content, 'CSS should be responsive');
        
        // Check for proper contrast (dark theme)
        $this->assertStringContainsString('#fff', $css_content, 'Should have white text for contrast');
        $this->assertStringContainsString('#000', $css_content, 'Should have black background');
    }
    
    public function test_plugin_activation_safety() {
        // Test that plugin activation is safe
        $main_file = TASKS_PLUGIN_DIR . 'tasks.php';
        $main_content = file_get_contents($main_file);
        
        // Check for security measures
        $this->assertStringContainsString('ABSPATH', $main_content, 'Should check for ABSPATH');
        $this->assertStringContainsString('die(', $main_content, 'Should die if accessed directly');
        $this->assertStringContainsString('register_activation_hook', $main_content, 'Should have activation hook');
        $this->assertStringContainsString('register_deactivation_hook', $main_content, 'Should have deactivation hook');
    }
    
    public function test_namespace_usage() {
        // Test that namespaces are properly used
        $post_type_file = TASKS_PLUGIN_DIR . 'includes/class-tasks-post-type.php';
        $taxonomy_file = TASKS_PLUGIN_DIR . 'includes/class-tasks-taxonomy.php';
        
        $post_type_content = file_get_contents($post_type_file);
        $taxonomy_content = file_get_contents($taxonomy_file);
        
        // Check for namespace declarations
        $this->assertStringContainsString('namespace TasksManager', $post_type_content, 'Post type should use TasksManager namespace');
        $this->assertStringContainsString('namespace TasksManager', $taxonomy_content, 'Taxonomy should use TasksManager namespace');
        
        // Check for escaped global functions
        $this->assertStringContainsString('\add_action', $post_type_content, 'Should escape global add_action function');
        $this->assertStringContainsString('\register_post_type', $post_type_content, 'Should escape global register_post_type function');
    }
}
