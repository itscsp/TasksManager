<?php
namespace TasksManager;

/**
 * Register custom taxonomy for tasks
 */
class Tasks_Taxonomy {
    public function init() {
        \add_action('init', array($this, 'register_taxonomy'));
    }

    protected function get_taxonomy_args() {
        return [
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'show_admin_column' => true,
            'rewrite' => ['slug' => 'project'],
            'labels' => [
                'name' => 'Projects',
                'singular_name' => 'Project'
            ]
        ];
    }

    public function register_taxonomy() {
        \register_taxonomy('project', 'task', $this->get_taxonomy_args());
    }
}
