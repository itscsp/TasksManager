<?php
namespace TasksManager;

/**
 * Register custom post type for tasks
 */
class Tasks_Post_Type {
    public function init() {
        \add_action('init', array($this, 'register_post_type'));
    }

    protected function get_post_type_args() {
        return [
            'public' => true,
            'rewrite' => ['slug' => 'tasks'],
            'supports' => ['title', 'editor', 'comments']
        ];
    }

    public function register_post_type() {
        \register_post_type('task', $this->get_post_type_args());
    }
}
