<?php

namespace Category_Buttons;

if (!defined('ABSPATH')) {
    exit;
}

class FrontEnd
{
    private static $instance = null;

    // Class property for basic settings
    private $basic_settings;

    private function __construct()
    {
        // Retrieve the basic settings
        $this->basic_settings = get_option('cb_basic_settings');
        
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));

        $display_in_store = isset($this->basic_settings['cb_display_in_store_field']) ? intval($this->basic_settings['cb_display_in_store_field']) : 0;
        if ($display_in_store === 1) {
            add_action('woocommerce_before_shop_loop', array($this, 'load_shortcode_before_woo_loop'));
        }
    }

    public static function get_instance()
    {
        if (self::$instance == null) {
            self::$instance = new FrontEnd();
        }
        return self::$instance;
    }

    public function load_shortcode_before_woo_loop()
    {
        $store_display_style = isset($this->basic_settings['cb_store_display_style_field']) ? sanitize_text_field($this->basic_settings['cb_store_display_style_field']) : 'with-title-and-description';
        $exclude_categories = isset($this->basic_settings['cb_exclude_categories']) ? sanitize_text_field($this->basic_settings['cb_exclude_categories']) : 0;
        $display_images = isset($this->basic_settings['cb_display_images_field']) ? intval($this->basic_settings['cb_display_images_field']) : 0;

        error_log('load_shortcode_before_woo_loop called'); // Debugging: Log to see if this function is called
        echo do_shortcode(
            '[woocb display-style="' . $store_display_style . '" 
            exclude-categories="' . $exclude_categories . '" 
            display-images="' . $display_images . '"]'
        );
    }

    public function enqueue_frontend_scripts()
    {
        $css_file = CB_PLUGIN_PATH . 'assets/css/styles.css';
        wp_enqueue_style('cb-dynamic-css', CB_PLUGIN_URL . 'assets/css/styles.css', array(), filemtime($css_file));
    }
}

FrontEnd::get_instance();
