<?php
/**
 * The public-facing functionality of the plugin
 */
class Tasks_Public {
    public function init() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_shortcode('tasks_manager', array($this, 'render_tasks_manager'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style('tasks-public', TASKS_PLUGIN_URL . 'assets/css/tasks-public.css', array(), TASKS_VERSION);
        wp_enqueue_script('tasks-public', TASKS_PLUGIN_URL . 'assets/js/tasks-public.js', array('jquery'), TASKS_VERSION, true);
        
        wp_localize_script('tasks-public', 'tasksAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tasks_ajax_nonce')
        ));
    }

    public function render_tasks_manager() {
        ob_start();
        include TASKS_PLUGIN_DIR . 'public/partials/tasks-manager.php';
        return ob_get_clean();
    }
}
