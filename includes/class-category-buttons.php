<?php

namespace Category_Buttons;

// Prevent direct access to the file
if (!defined('ABSPATH')) {
    exit;
}

class Main
{
    private static $instance = null;

    // Constructor
    public function __construct()
    {
        // Initialize the plugin
        $this->init();
    }

    /**
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @return Main The single instance of the class.
     */
    public static function get_instance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Initialize the plugin
    private function init()
    {

        $this->init_after_update();

        // Initialize Frontend functionality
        if (!is_admin()) {
            $this->init_frontend();
        }

        // Initialize Elementor Widgets functionality
        $this->init_elementor_widgets();

        // Initialize Admin functionality
        if (is_admin()) {
            $this->init_admin();
        }

        // Include shortcode
        require_once CB_PLUGIN_PATH . 'includes/shortcode.php';
    }

    public function init_after_update()
    {
        $default_basic_options = array(
            'cb_display_in_store_field' => 1,
            'cb_store_display_style_field' => 'with-title-and-description',
            'cb_display_images_field' => 1,
            'cb_hide_notice' => 1,
            'cb_repeater_field' => array(
                array(
                    'image' => '', // Default image URL
                    'name' => '',
                    'url' => '',
                ),
            ),
        );

        $default_style_options = array(
            'cb_columns_desktop' => 4,
            'cb_columns_tablet' => 3,
            'cb_columns_mobile' => 1,
            'cb_background_color_field' => '#ffffff',
            'cb_background_hover_color_field' => '#f3f3f3',
            'cb_text_color_field' => '#000000',
            'cb_text_hover_color_field' => '#000000',
            'cb_border_radius_field' => '4px',
            'cb_border_color_field' => '#ebebeb',
            'cb_border_hover_color_field' => '#fed700',
            'cb_title_font_size_field' => '1.75rem',
            'cb_description_font_size_field' => '.875rem',
            'cb_buttons_font_size_field' => '.875rem',
            'cb_style_hide_notice' => 1,
        );

        // Only set the default options if they do not already exist
        if (get_option('cb_basic_settings') === false) {
            add_option('cb_basic_settings', $default_basic_options);
        }

        if (get_option('cb_style_settings') === false) {
            add_option('cb_style_settings', $default_style_options);
        }

        // Delete old plugin options
        delete_option('woocommerce_category_buttons_option_name');
        delete_option('ryvenia_style_settings_option_name');
        delete_site_option('woocommerce_category_buttons_option_name');
        delete_site_option('ryvenia_style_settings_option_name');

        // Add Admin Notices after Update
        $cb_basic_settings = get_option('cb_basic_settings');
        $cb_style_settings = get_option('cb_style_settings');
        if ($cb_basic_settings['cb_hide_notice'] == 0 && $cb_style_settings['cb_style_hide_notice'] == 0) {
            add_action('admin_notices', array($this, 'after_update_notice'));
        }
    }

    public function after_update_notice()
    {
        $settings_url = admin_url('admin.php?page=woocommerce-category-buttons');
        $logo_url = CB_PLUGIN_URL . 'assets/images/icon-256x256.png';

        echo '<div style="padding: 10px;" class="notice notice-warning is-dismissible">
            <div style="display: flex; align-items: center;">
                <img src="' . esc_url($logo_url) . '" alt="Category Buttons" style="width: 50px; height: 50px; margin-right: 20px;">
                <div>
                    <p>' . __('<strong>The Category Buttons plugin has been reworked!</strong> We have added new functions, fixed errors and issues, and introduced a new <strong>Elementor widget</strong> along with a shortcode copy-paste generator. Please visit the plugin settings to set up the plugin. This notice will disappear after you save any settings in the plugin.', 'category-buttons') . '</p>
                    <a href="' . esc_url($settings_url) . '" class="button button-primary">' . __('Go to Settings', 'category-buttons') . '</a>
                </div>
            </div>
        </div>';
    }

    public function init_admin()
    {
        require_once CB_PLUGIN_PATH . 'includes/class-category-buttons-admin.php';
        Admin::get_instance();
        require_once CB_PLUGIN_PATH . 'includes/class-category-buttons-dynamic-css.php';
        DynamicCss::get_instance();  // Initialize the DynamicCss class
    }

    public function init_frontend()
    {
        require_once CB_PLUGIN_PATH . 'includes/class-category-buttons-frontend.php';
        FrontEnd::get_instance();
    }

    // ELEMENTOR FEATURE
    public function init_elementor_widgets()
    {
        add_action('elementor/widgets/register', array($this, 'register_elementor_widgets'));
    }

    public function register_elementor_widgets($widgets_manager)
    {
        // Check if Elementor is active
        if (did_action('elementor/loaded')) {
            require_once CB_PLUGIN_PATH . 'includes/elementor-widgets/widget.php';
            $widgets_manager->register(new \Category_Buttons_Widget());
        }
    }
}
