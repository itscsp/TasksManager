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

// Include the class files directly (temporary fix)
require_once dirname(__DIR__) . '/includes/Tasks_Post_Type.php';
require_once dirname(__DIR__) . '/includes/Tasks_Taxonomy.php';
require_once dirname(__DIR__) . '/includes/Tasks_Meta_Boxes.php';

// Define WordPress functions that we'll use in tests
if (!function_exists('plugin_dir_path')) {
    function plugin_dir_path($file) {
        return dirname($file) . '/';
    }
}

if (!function_exists('plugin_dir_url')) {
    function plugin_dir_url($file) {
        return 'http://example.com/wp-content/plugins/' . basename(dirname($file)) . '/';
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
