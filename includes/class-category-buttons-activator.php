<?php

namespace Category_Buttons;

if (!defined('ABSPATH')) {
    exit;
}

class Activator
{
    public static function activate()
    {

        // Ensure the options are set and display the admin notice
        Main::get_instance()->init_after_update();

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
    }
}
