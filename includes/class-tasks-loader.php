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
    }

    /**
     * Load the required dependencies
     */
    private function load_dependencies() {
        // Initialize post type
        $post_type = new \TasksManager\Tasks_Post_Type();
        $post_type->init();

        // Initialize taxonomy
        $taxonomy = new \TasksManager\Tasks_Taxonomy();
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
}
