<?php
/**
 * Register custom taxonomy for tasks
 */
class Tasks_Taxonomy {
    public function init() {
        add_action('init', array($this, 'register_taxonomy'));
    }

    public function register_taxonomy() {
        $args = array(
            'labels' => array(
                'name' => 'Projects',
                'singular_name' => 'Project',
                'search_items' => 'Search Projects',
                'all_items' => 'All Projects',
                'edit_item' => 'Edit Project',
                'update_item' => 'Update Project',
                'add_new_item' => 'Add New Project',
                'new_item_name' => 'New Project Name',
                'menu_name' => 'Projects',
            ),
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'project'),
        );
        register_taxonomy('project', 'task', $args);
    }
}
