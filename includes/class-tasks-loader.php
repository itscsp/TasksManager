<?php
/**
 * The core plugin loader class.
 */
class Tasks_Loader {
    /**
     * Initialize the plugin
     */
    public function run() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Load the required dependencies
     */
    private function load_dependencies() {
        // Initialize post type
        $post_type = new Tasks_Post_Type();
        $post_type->init();

        // Initialize taxonomy
        $taxonomy = new Tasks_Taxonomy();
        $taxonomy->init();

        // Initialize admin
        if (is_admin()) {
            $admin = new Tasks_Admin();
            $admin->init();
        }

        // Initialize public
        $public = new Tasks_Public();
        $public->init();
    }

    /**
     * Register hooks that are fired when the plugin is activated or deactivated
     */
    private function init_hooks() {
        register_activation_hook(TASKS_PLUGIN_DIR . 'tasks.php', array($this, 'activate'));
        register_deactivation_hook(TASKS_PLUGIN_DIR . 'tasks.php', array($this, 'deactivate'));
    }

    /**
     * The code that runs during plugin activation
     */
    public function activate() {
        // Activation code here
        flush_rewrite_rules();
    }

    /**
     * The code that runs during plugin deactivation
     */
    public function deactivate() {
        // Deactivation code here
        flush_rewrite_rules();
    }
}
