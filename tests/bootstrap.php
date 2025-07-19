<?php
/**
 * PHPUnit bootstrap file
 *
 * @package TasksManager
 */

// First, we need to load the composer autoloader so we can use WP Mock and other dependencies
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Now we need to bootstrap WP Mock
Brain\Monkey\setUp();

// Define WordPress constants
if (!defined('TASKS_PLUGIN_DIR')) {
    define('TASKS_PLUGIN_DIR', dirname(__DIR__) . '/');
}
if (!defined('TASKS_PLUGIN_URL')) {
    define('TASKS_PLUGIN_URL', 'http://blogchethanspoojarycom.local/wp-content/plugins/tasks/');
}
if (!defined('TASKS_VERSION')) {
    define('TASKS_VERSION', '1.0.0');
}

// Include the class files
require_once dirname(__DIR__) . '/includes/class-tasks-post-type.php';
require_once dirname(__DIR__) . '/includes/class-tasks-taxonomy.php';
require_once dirname(__DIR__) . '/includes/class-tasks-loader.php';
require_once dirname(__DIR__) . '/admin/class-tasks-admin.php';
require_once dirname(__DIR__) . '/public/class-tasks-public.php';

// Define WordPress functions that we'll use in tests
if (!function_exists('plugin_dir_path')) {
    function plugin_dir_path($file) {
        return dirname($file) . '/';
    }
}

if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url($file) {
        return 'http://blogchethanspoojarycom.local/wp-content/plugins/' . basename(dirname($file)) . '/';
    }
}

if (!function_exists('plugin_basename')) {
    function plugin_basename($file) {
        return basename(dirname($file)) . '/' . basename($file);
    }
}

// Define WordPress textdomain functions
if (!function_exists('__')) {
    function __($text, $domain = 'default') {
        return $text;
    }
}

if (!function_exists('_e')) {
    function _e($text, $domain = 'default') {
        echo $text;
    }
}

// Set up global WordPress variables that might be used
global $wpdb;
$wpdb = new class {
    public $prefix = 'wp_';
    public function prepare($query, ...$args) {
        return $query;
    }
};
