<?php
/**
 * Tasks Manager - WordPress Plugin
 *
 * @package     TasksManager
 * @author      Chethan S Poojary
 * @copyright   2025 Chethan S Poojary
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Tasks Manager
 * Plugin URI:  https://github.com/tasks-manager
 * Description: A plugin to manage daily tasks with projects and subtasks
 * Version: 1.0.2
 * Author:      Chethan S Poojary
 * Author URI:  https://chethanspoojary.com
 * Text Domain: tasks-manager
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    die('Direct access is not allowed.');
}

/**
 * Define plugin constants
 */
define('TASKS_VERSION', '1.0.2');
define('TASKS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TASKS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TASKS_BASENAME', plugin_basename(__FILE__));

/**
 * Include required files
 */
$required_files = array(
    'includes/class-tasks-loader.php',
    'includes/class-tasks-post-type.php',
    'includes/class-tasks-taxonomy.php',
    'includes/class-tasks-model.php',
    'admin/class-tasks-admin.php',
    'public/class-tasks-public.php'
);

foreach ($required_files as $file) {
    require_once TASKS_PLUGIN_DIR . $file;
}

require_once plugin_dir_path(__FILE__) . 'plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

// No meta box code here anymore - it's been moved to admin/class-tasks-meta-boxes.php

/**
 * Initialize the plugin
 *
 * @since 1.0.0
 * @return void
 */
function tasks_init() {
            public function run_plugin() {
            if (is_admin()) {
                PucFactory::buildUpdateChecker(
                    'https://raw.githubusercontent.com/itscsp/TasksManager/main/manifest.json',
                    __FILE__,
                    'budget-buddy'
                );
            }
        }
    // Initialize plugin loader
    $loader = new Tasks_Loader();
    $loader->run();
}

/**
 * Plugin activation callback
 */
function tasks_activate() {
    // Flush rewrite rules on activation
    flush_rewrite_rules();
}

/**
 * Plugin deactivation callback
 */
function tasks_deactivate() {
    // Flush rewrite rules on deactivation
    flush_rewrite_rules();
}

// Initialize the plugin
add_action('plugins_loaded', 'tasks_init');

// Register activation and deactivation hooks
register_activation_hook(__FILE__, 'tasks_activate');
register_deactivation_hook(__FILE__, 'tasks_deactivate');
