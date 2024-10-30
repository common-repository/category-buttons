<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Delete the plugin options from the database
delete_option('cb_basic_settings');
delete_option('cb_style_settings');

// Delete old plugin options
delete_option('woocommerce_category_buttons_option_name');
delete_option('ryvenia_style_settings_option_name');
delete_site_option('woocommerce_category_buttons_option_name');
delete_site_option('ryvenia_style_settings_option_name');
?>
