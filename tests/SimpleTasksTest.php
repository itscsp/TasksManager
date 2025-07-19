<?php

use PHPUnit\Framework\TestCase;

class SimpleTasksTest extends TestCase {
    
    public function test_plugin_classes_can_be_instantiated() {
        // Test that all our main classes can be loaded without fatal errors
        
        // Test Tasks_Loader
        $this->assertTrue(class_exists('Tasks_Loader'), 'Tasks_Loader class should exist');
        
        // Test Tasks_Public
        $this->assertTrue(class_exists('Tasks_Public'), 'Tasks_Public class should exist');
        
        // Test Tasks_Admin
        $this->assertTrue(class_exists('Tasks_Admin'), 'Tasks_Admin class should exist');
        
        // Test namespaced classes
        $this->assertTrue(class_exists('TasksManager\Tasks_Post_Type'), 'Tasks_Post_Type class should exist in TasksManager namespace');
        $this->assertTrue(class_exists('TasksManager\Tasks_Taxonomy'), 'Tasks_Taxonomy class should exist in TasksManager namespace');
    }
    
    public function test_plugin_constants_are_defined() {
        // Test that our plugin constants are defined
        $this->assertTrue(defined('TASKS_PLUGIN_DIR'), 'TASKS_PLUGIN_DIR constant should be defined');
        $this->assertTrue(defined('TASKS_PLUGIN_URL'), 'TASKS_PLUGIN_URL constant should be defined');
        $this->assertTrue(defined('TASKS_VERSION'), 'TASKS_VERSION constant should be defined');
        
        // Test constant values
        $this->assertEquals('1.0.0', TASKS_VERSION, 'TASKS_VERSION should be 1.0.0');
        $this->assertStringEndsWith('/', TASKS_PLUGIN_DIR, 'TASKS_PLUGIN_DIR should end with /');
        $this->assertStringEndsWith('/', TASKS_PLUGIN_URL, 'TASKS_PLUGIN_URL should end with /');
    }
    
    public function test_tasks_public_class_structure() {
        // Test that Tasks_Public class has the expected methods
        $reflection = new ReflectionClass('Tasks_Public');
        
        $this->assertTrue($reflection->hasMethod('init'), 'Tasks_Public should have init method');
        $this->assertTrue($reflection->hasMethod('enqueue_scripts'), 'Tasks_Public should have enqueue_scripts method');
        $this->assertTrue($reflection->hasMethod('render_tasks_manager'), 'Tasks_Public should have render_tasks_manager method');
        $this->assertTrue($reflection->hasMethod('handle_add_task'), 'Tasks_Public should have handle_add_task method');
        $this->assertTrue($reflection->hasMethod('handle_add_subtask'), 'Tasks_Public should have handle_add_subtask method');
        $this->assertTrue($reflection->hasMethod('handle_update_task_status'), 'Tasks_Public should have handle_update_task_status method');
        $this->assertTrue($reflection->hasMethod('handle_update_subtask_status'), 'Tasks_Public should have handle_update_subtask_status method');
    }
    
    public function test_tasks_loader_class_structure() {
        // Test that Tasks_Loader class has the expected methods
        $reflection = new ReflectionClass('Tasks_Loader');
        
        $this->assertTrue($reflection->hasMethod('run'), 'Tasks_Loader should have run method');
        $this->assertTrue($reflection->hasMethod('load_dependencies'), 'Tasks_Loader should have load_dependencies method');
    }
    
    public function test_post_type_class_structure() {
        // Test that the post type class has the expected methods
        $reflection = new ReflectionClass('TasksManager\Tasks_Post_Type');
        
        $this->assertTrue($reflection->hasMethod('init'), 'Tasks_Post_Type should have init method');
        $this->assertTrue($reflection->hasMethod('register_post_type'), 'Tasks_Post_Type should have register_post_type method');
    }
    
    public function test_taxonomy_class_structure() {
        // Test that the taxonomy class has the expected methods
        $reflection = new ReflectionClass('TasksManager\Tasks_Taxonomy');
        
        $this->assertTrue($reflection->hasMethod('init'), 'Tasks_Taxonomy should have init method');
        $this->assertTrue($reflection->hasMethod('register_taxonomy'), 'Tasks_Taxonomy should have register_taxonomy method');
    }
    
    public function test_plugin_file_structure() {
        // Test that important plugin files exist
        $plugin_dir = TASKS_PLUGIN_DIR;
        
        $this->assertFileExists($plugin_dir . 'tasks.php', 'Main plugin file should exist');
        $this->assertFileExists($plugin_dir . 'public/class-tasks-public.php', 'Public class file should exist');
        $this->assertFileExists($plugin_dir . 'admin/class-tasks-admin.php', 'Admin class file should exist');
        $this->assertFileExists($plugin_dir . 'includes/class-tasks-loader.php', 'Loader class file should exist');
        $this->assertFileExists($plugin_dir . 'assets/css/tasks-public.css', 'Public CSS file should exist');
        $this->assertFileExists($plugin_dir . 'assets/js/tasks-public.js', 'Public JS file should exist');
        $this->assertFileExists($plugin_dir . 'public/partials/tasks-manager.php', 'Tasks manager template should exist');
    }
    
    public function test_css_file_has_black_theme() {
        // Test that the CSS file contains our black theme
        $css_file = TASKS_PLUGIN_DIR . 'assets/css/tasks-public.css';
        $css_content = file_get_contents($css_file);
        
        $this->assertStringContainsString('background: #000', $css_content, 'CSS should contain black background');
        $this->assertStringContainsString('color: #fff', $css_content, 'CSS should contain white text');
        $this->assertStringContainsString('#00aaff', $css_content, 'CSS should contain blue accent color');
        $this->assertStringContainsString('tasks-manager-container', $css_content, 'CSS should contain main container class');
    }
    
    public function test_javascript_file_has_ajax_functionality() {
        // Test that the JS file contains our AJAX functionality
        $js_file = TASKS_PLUGIN_DIR . 'assets/js/tasks-public.js';
        $js_content = file_get_contents($js_file);
        
        $this->assertStringContainsString('add_task', $js_content, 'JS should contain add_task action');
        $this->assertStringContainsString('add_subtask', $js_content, 'JS should contain add_subtask action');
        $this->assertStringContainsString('update_task_status', $js_content, 'JS should contain update_task_status action');
        $this->assertStringContainsString('update_subtask_status', $js_content, 'JS should contain update_subtask_status action');
        $this->assertStringContainsString('tasksAjax.ajaxurl', $js_content, 'JS should use localized AJAX URL');
        $this->assertStringContainsString('tasksAjax.nonce', $js_content, 'JS should use localized nonce');
    }
    
    public function test_template_file_has_required_elements() {
        // Test that the template file contains required form elements and classes
        $template_file = TASKS_PLUGIN_DIR . 'public/partials/tasks-manager.php';
        $template_content = file_get_contents($template_file);
        
        $this->assertStringContainsString('tasks-manager-container', $template_content, 'Template should contain main container');
        $this->assertStringContainsString('new-task-form', $template_content, 'Template should contain task form');
        $this->assertStringContainsString('add-subtask-form', $template_content, 'Template should contain subtask form');
        $this->assertStringContainsString('status-selector', $template_content, 'Template should contain status selector');
        $this->assertStringContainsString('get_current_user_id()', $template_content, 'Template should filter by current user');
    }
}
