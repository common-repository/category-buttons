<?php

namespace Category_Buttons;

if (!defined('ABSPATH')) {
    exit;
}

class DynamicCss
{
    private static $instance = null;

    public static function get_instance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        error_log('DynamicCss::__construct called'); // Debugging: Constructor called
        add_action('wp_enqueue_scripts', array($this, 'enqueue_dynamic_css'));
        add_action('update_option_cb_style_settings', array($this, 'generate_dynamic_css'), 10, 2);
    }

    public function enqueue_dynamic_css()
    {
        error_log('DynamicCss::enqueue_dynamic_css called'); // Debugging: enqueue_dynamic_css called
        $css_file = CB_PLUGIN_PATH . 'assets/css/styles.css';
        if (file_exists($css_file)) {
            wp_enqueue_style('cb-dynamic-css', CB_PLUGIN_URL . 'assets/css/styles.css', array(), filemtime($css_file));
        }
    }

    public function generate_dynamic_css($old_value, $value)
    {
        // Debugging: Check if the function is called
        error_log('DynamicCss::generate_dynamic_css called');

        // Initialize the filesystem
        if (!function_exists('WP_Filesystem')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        WP_Filesystem();
        global $wp_filesystem;

        $css_dir = CB_PLUGIN_PATH . 'assets/css/';
        $css_file = $css_dir . 'styles.css';
        $template_file = $css_dir . 'styles-template.css';

        if (!$wp_filesystem->is_dir($css_dir)) {
            $wp_filesystem->mkdir($css_dir);
        }

        if (!$wp_filesystem->exists($template_file)) {
            error_log('Template CSS file does not exist.');
            return;
        }

        $columns_desktop = isset($value['cb_columns_desktop']) ? intval($value['cb_columns_desktop']) : 4;
        $columns_tablet = isset($value['cb_columns_tablet']) ? intval($value['cb_columns_tablet']) : 3;
        $columns_mobile = isset($value['cb_columns_mobile']) ? intval($value['cb_columns_mobile']) : 1;
        $background_color= isset($value['cb_background_color_field']) ? sanitize_text_field($value['cb_background_color_field']) : '#ffffff';
        $background_hover_color= isset($value['cb_background_hover_color_field']) ? sanitize_text_field($value['cb_background_hover_color_field']) : '#f3f3f3';
        $text_color = isset($value['cb_text_color_field']) ? sanitize_text_field($value['cb_text_color_field']) : '#000000';
        $text_hover_color = isset($value['cb_text_hover_color_field']) ? sanitize_text_field($value['cb_text_hover_color_field']) : '#000000';
        $border_radius = isset($value['cb_border_radius_field']) ? sanitize_text_field($value['cb_border_radius_field']) : '4px';
        $border_color = isset($value['cb_border_color_field']) ? sanitize_text_field($value['cb_border_color_field']) : '#ebebeb';
        $border_hover_color = isset($value['cb_border_hover_color_field']) ? sanitize_text_field($value['cb_border_hover_color_field']) : '#fed700';
        $title_font_size = isset($value['cb_title_font_size_field']) ? sanitize_text_field($value['cb_title_font_size_field']) : '40px';
        $description_font_size = isset($value['cb_description_font_size_field']) ? sanitize_text_field($value['cb_description_font_size_field']) : '20px';
        $buttons_font_size = isset($value['cb_buttons_font_size_field']) ? sanitize_text_field($value['cb_buttons_font_size_field']) : '15px';

        $template_content = $wp_filesystem->get_contents($template_file);

        $css_content = str_replace(
            array('{{columns_desktop}}', '{{columns_tablet}}', '{{columns_mobile}}', '{{background_color}}', '{{background_hover_color}}', '{{text_color}}', '{{text_hover_color}}', '{{border_radius}}', '{{border_color}}', '{{border_hover_color}}', '{{title_font_size}}', '{{description_font_size}}', '{{buttons_font_size}}'),
            array($columns_desktop, $columns_tablet, $columns_mobile, $background_color, $background_hover_color, $text_color, $text_hover_color, $border_radius, $border_color, $border_hover_color, $title_font_size, $description_font_size, $buttons_font_size),
            $template_content
        );

        // Debugging: Check CSS content
        error_log('Generated CSS content: ' . $css_content);

        if ($wp_filesystem->put_contents($css_file, $css_content, FS_CHMOD_FILE)) {
            // Debugging: Log success
            error_log('CSS file written successfully.');
        } else {
            // Debugging: Log failure
            error_log('Failed to write CSS file.');
        }
    }
}
