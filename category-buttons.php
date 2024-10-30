<?php
/*
Plugin Name: Category Buttons
Description: A simple plugin that enables you to add custom category buttons to all shop pages and category pages.
Version: 2.0.1
Author: Ryvenia
Author URI: https://ryvenia.sk/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: category-buttons
Domain Path: /languages
Requires Plugins: woocommerce
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

if ( ! defined( 'CB_PLUGIN_VERSION' ) ) {
    define( 'CB_PLUGIN_VERSION', '2.0.1' );
}

function ryvenia_load_textdomain($mofile, $domain)
{
    if ('category-buttons' === $domain && false !== strpos($mofile, WP_LANG_DIR . '/plugins/')) {
        $locale = apply_filters('plugin_locale', determine_locale(), $domain);
        $mofile = WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__)) . '/languages/' . $domain . '-' . $locale . '.mo';
    }
    return $mofile;
}
add_filter('load_textdomain_mofile', 'ryvenia_load_textdomain', 10, 2);

add_action('init', 'ryvenia_load_text_domain');

function ryvenia_load_text_domain()
{
    load_plugin_textdomain('category-buttons', false, dirname(plugin_basename(__FILE__)) . '/languages');
}

// Define Plugin Constants
define('CB_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('CB_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include the main plugin class file
require_once CB_PLUGIN_PATH . 'includes/class-category-buttons.php';
// Include the activation class file
require_once CB_PLUGIN_PATH . 'includes/class-category-buttons-activator.php';

// Set default options upon activation
register_activation_hook(__FILE__, array('Category_Buttons\Activator', 'activate'));

// Initialize the plugin
function category_buttons_init()
{
    $category_buttons = new \Category_Buttons\Main();
}
add_action('plugins_loaded', 'category_buttons_init');
