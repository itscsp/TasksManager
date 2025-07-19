<?php
/**
 * Register custom post type for tasks
 */
namespace TasksManager;

class Tasks_Post_Type {
    public function init() {
        \add_action('init', array($this, 'register_post_type'));
    }

    public function register_post_type() {
        $args = array(
            'labels' => array(
                'name' => 'Tasks',
                'singular_name' => 'Task',
                'add_new' => 'Add New Task',
                'add_new_item' => 'Add New Task',
                'edit_item' => 'Edit Task',
                'view_item' => 'View Task',
                'search_items' => 'Search Tasks',
            ),
            'public' => true,
            'supports' => array('title', 'editor', 'comments'),
            'menu_icon' => 'dashicons-clipboard',
            'has_archive' => true,
            'rewrite' => array('slug' => 'tasks'),
        );
        \register_post_type('task', $args);
    }
}
