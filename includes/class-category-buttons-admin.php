<?php

namespace Category_Buttons;

if (!defined('ABSPATH')) {
    exit;
}

class Admin
{
    private static $instance = null;

    private function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts_styles'));
        add_action('admin_menu', array($this, 'add_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_post_send_contact_form', array($this, 'handle_contact_form_submission'));
    }

    public static function get_instance()
    {
        if (self::$instance == null) {
            self::$instance = new Admin();
        }
        return self::$instance;
    }

    public function enqueue_admin_scripts_styles()
    {
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_media();
        wp_enqueue_script('cb-a-scripts', CB_PLUGIN_URL . 'assets/js/admin-scripts.js', array('jquery', 'media-upload', 'thickbox'), CB_PLUGIN_VERSION, true);
        wp_enqueue_style('thickbox');
        wp_enqueue_style('cb-a-styles', CB_PLUGIN_URL . 'assets/css/admin-styles.css');
    }

    public function add_menu()
    {
        add_menu_page(
            esc_html__('Category Buttons', 'category-buttons'), // page_title
            esc_html__('Category Buttons', 'category-buttons'), // menu_title
            'manage_options', // capability
            'woocommerce-category-buttons', // menu_slug
            array($this, 'admin_index'),
            CB_PLUGIN_URL . '/assets/images/icon.png', // icon_url
            2 // position
        );
    }

    public function register_settings()
    {
        // Register Basic Setting
        register_setting('cb_basic_settings_group', 'cb_basic_settings', array($this, 'sanitize_basic_settings'));

        // Register Style Setting
        register_setting('cb_style_settings_group', 'cb_style_settings', array($this, 'sanitize_style_settings'));

        // Add Basic settings section
        add_settings_section(
            'cb_basic_settings_section',
            '',
            null,
            'woocommerce-category-buttons'
        );

        // Add Style Settings section
        add_settings_section(
            'cb_styling_settings_section',
            '',
            null,
            'woocommerce-category-buttons-styling'
        );

        // Add a Shortcode settings section
        add_settings_section(
            'cb_shortcode_settings_section', // section ID
            esc_html__('', 'category-buttons'), // title
            null, // callback function
            'woocommerce-category-buttons-shortcode' // page slug
        );

        // Add fields to Basic Settings
        $this->add_basic_fields();

        // Add fields to Shortcode Settings
        $this->add_shortcode_fields();

        // Add fields to Style Settings
        $this->add_style_fields();
    }

    private function add_shortcode_fields()
    {
        // Add fields to Shortcode Settings
        add_settings_field(
            'cb_shortcode_heading',
            '', // No label
            array($this, 'shortcode_heading_callback'), // callback function
            'woocommerce-category-buttons-shortcode',
            'cb_shortcode_settings_section'
        );

        add_settings_field(
            'cb_shortcode_settings',
            '', // No label
            array($this, 'shortcode_settings_callback'), // callback function
            'woocommerce-category-buttons-shortcode',
            'cb_shortcode_settings_section'
        );
    }

    private function add_basic_fields()
    {
        add_settings_field(
            'cb_top_heading',
            '',
            array($this, 'top_heading_callback'),
            'woocommerce-category-buttons',
            'cb_basic_settings_section'
        );

        add_settings_field(
            'cb_display_in_store_field',
            esc_html__('Display in store', 'category-buttons'),
            array($this, 'display_in_store_field_callback'),
            'woocommerce-category-buttons',
            'cb_basic_settings_section'
        );

        add_settings_field(
            'cb_store_display_style_field',
            esc_html__('Store display style', 'category-buttons'),
            array($this, 'store_display_style_field_callback'),
            'woocommerce-category-buttons',
            'cb_basic_settings_section'
        );

        add_settings_field(
            'cb_exclude_categories',
            esc_html__('Exclude categories', 'category-buttons'),
            array($this, 'exclude_categories_callback'),
            'woocommerce-category-buttons',
            'cb_basic_settings_section'
        );

        add_settings_field(
            'cb_display_images_field',
            esc_html__('Display images', 'category-buttons'),
            array($this, 'display_images_field_callback'),
            'woocommerce-category-buttons',
            'cb_basic_settings_section'
        );

        add_settings_field(
            'cb_custom_settings_heading',
            '',
            array($this, 'custom_settings_heading_callback'),
            'woocommerce-category-buttons',
            'cb_basic_settings_section'
        );

        add_settings_field(
            'cb_custom_title_field',
            esc_html__('Custom title', 'category-buttons'),
            array($this, 'custom_title_field_callback'),
            'woocommerce-category-buttons',
            'cb_basic_settings_section'
        );

        add_settings_field(
            'cb_custom_description_field',
            esc_html__('Custom description', 'category-buttons'),
            array($this, 'custom_description_field_callback'),
            'woocommerce-category-buttons',
            'cb_basic_settings_section'
        );

        // Repeater field for images, custom names, and custom URLs
        add_settings_field(
            'cb_custom_repeater_heading',
            '',
            array($this, 'custom_repeater_heading_callback'),
            'woocommerce-category-buttons',
            'cb_basic_settings_section'
        );

        add_settings_field(
            'cb_repeater_field',
            esc_html__('', 'category-buttons'),
            array($this, 'repeater_field_callback'),
            'woocommerce-category-buttons',
            'cb_basic_settings_section'
        );

        add_settings_field(
            'cb_hide_notice',
            esc_html__('', 'category-buttons'),
            array($this, 'hide_notice_field_callback'),
            'woocommerce-category-buttons',
            'cb_basic_settings_section'
        );
    }

    private function add_style_fields()
    {
        add_settings_field(
            'cb_style_settings_top_heading',
            '',
            array($this, 'style_settings_top_heading_callback'),
            'woocommerce-category-buttons-styling',
            'cb_styling_settings_section'
        );

        add_settings_field(
            'cb_columns_desktop',
            esc_html__('Columns Desktop', 'category-buttons'),
            array($this, 'columns_desktop_callback'),
            'woocommerce-category-buttons-styling',
            'cb_styling_settings_section'
        );

        add_settings_field(
            'cb_columns_tablet',
            esc_html__('Columns Tablet', 'category-buttons'),
            array($this, 'columns_tablet_callback'),
            'woocommerce-category-buttons-styling',
            'cb_styling_settings_section'
        );

        add_settings_field(
            'cb_columns_mobile',
            esc_html__('Columns Mobile', 'category-buttons'),
            array($this, 'columns_mobile_callback'),
            'woocommerce-category-buttons-styling',
            'cb_styling_settings_section'
        );

        add_settings_field(
            'cb_style_color_settings_heading',
            '',
            array($this, 'style_color_settings_heading_callback'),
            'woocommerce-category-buttons-styling',
            'cb_styling_settings_section'
        );

        add_settings_field(
            'cb_background_color_field',
            esc_html__('Background Color', 'category-buttons'),
            array($this, 'background_color_field_callback'),
            'woocommerce-category-buttons-styling',
            'cb_styling_settings_section'
        );

        add_settings_field(
            'cb_background_hover_color_field',
            esc_html__('Background Hover Color', 'category-buttons'),
            array($this, 'background_hover_color_field_callback'),
            'woocommerce-category-buttons-styling',
            'cb_styling_settings_section'
        );

        add_settings_field(
            'cb_text_color_field',
            esc_html__('Text Color', 'category-buttons'),
            array($this, 'text_color_field_callback'),
            'woocommerce-category-buttons-styling',
            'cb_styling_settings_section'
        );

        add_settings_field(
            'cb_text_hover_color_field',
            esc_html__('Text Hover Color', 'category-buttons'),
            array($this, 'text_hover_color_field_callback'),
            'woocommerce-category-buttons-styling',
            'cb_styling_settings_section'
        );

        add_settings_field(
            'cb_style_border_settings_heading',
            '',
            array($this, 'style_border_settings_heading_callback'),
            'woocommerce-category-buttons-styling',
            'cb_styling_settings_section'
        );

        add_settings_field(
            'cb_border_radius_field',
            esc_html__('Border radius', 'category-buttons'),
            array($this, 'border_radius_field_callback'),
            'woocommerce-category-buttons-styling',
            'cb_styling_settings_section'
        );

        add_settings_field(
            'cb_border_color_field',
            esc_html__('Border Color', 'category-buttons'),
            array($this, 'border_color_field_callback'),
            'woocommerce-category-buttons-styling',
            'cb_styling_settings_section'
        );

        add_settings_field(
            'cb_border_hover_color_field',
            esc_html__('Border Hover Color', 'category-buttons'),
            array($this, 'border_hover_color_field_callback'),
            'woocommerce-category-buttons-styling',
            'cb_styling_settings_section'
        );

        add_settings_field(
            'cb_font_settings_heading',
            '',
            array($this, 'style_font_settings_heading_callback'),
            'woocommerce-category-buttons-styling',
            'cb_styling_settings_section'
        );

        add_settings_field(
            'cb_title_font_size_field',
            esc_html__('Title font size', 'category-buttons'),
            array($this, 'title_font_size_field_callback'),
            'woocommerce-category-buttons-styling',
            'cb_styling_settings_section'
        );

        add_settings_field(
            'cb_description_font_size_field',
            esc_html__('Description font size', 'category-buttons'),
            array($this, 'description_font_size_field_callback'),
            'woocommerce-category-buttons-styling',
            'cb_styling_settings_section'
        );

        add_settings_field(
            'cb_buttons_font_size_field',
            esc_html__('Buttons font size', 'category-buttons'),
            array($this, 'buttons_font_size_field_callback'),
            'woocommerce-category-buttons-styling',
            'cb_styling_settings_section'
        );

        add_settings_field(
            'cb_style_hide_notice',
            esc_html__('', 'category-buttons'),
            array($this, 'style_hide_notice_field_callback'),
            'woocommerce-category-buttons-styling',
            'cb_styling_settings_section'
        );
    }

    public function sanitize_basic_settings($input)
    {
        $sanitized_input = array();
        // Sanitize Basic Settings fields
        if (isset($input['cb_display_in_store_field'])) {
            $sanitized_input['cb_display_in_store_field'] = $input['cb_display_in_store_field'] ? 1 : 0;
        }
        if (isset($input['cb_store_display_style_field'])) {
            $sanitized_input['cb_store_display_style_field'] = sanitize_text_field($input['cb_store_display_style_field']);
        }
        if (isset($input['cb_exclude_categories'])) {
            $sanitized_input['cb_exclude_categories'] = implode(',', array_map('intval', explode(',', $input['cb_exclude_categories'])));
        }
        if (isset($input['cb_display_images_field'])) {
            $sanitized_input['cb_display_images_field'] = $input['cb_display_images_field'] ? 1 : 0;
        }
        if (isset($input['cb_custom_title_field'])) {
            $sanitized_input['cb_custom_title_field'] = sanitize_text_field($input['cb_custom_title_field']);
        }
        if (isset($input['cb_custom_description_field'])) {
            $sanitized_input['cb_custom_description_field'] = wp_kses_post($input['cb_custom_description_field']);
        }

        if (isset($input['cb_repeater_field']) && is_array($input['cb_repeater_field'])) {
            foreach ($input['cb_repeater_field'] as $index => $item) {
                $sanitized_input['cb_repeater_field'][$index]['image'] = esc_url_raw($item['image']);
                $sanitized_input['cb_repeater_field'][$index]['name'] = sanitize_text_field($item['name']);
                $sanitized_input['cb_repeater_field'][$index]['url'] = esc_url_raw($item['url']);
                $sanitized_input['cb_repeater_field'][$index]['category'] = sanitize_text_field($item['category']);
            }
        }
        if (isset($input['cb_hide_notice'])) {
            $sanitized_input['cb_hide_notice'] = $input['cb_hide_notice'] ? 1 : 0;
        }
        // Sanitize Basic Settings fields END

        return $sanitized_input;
    }

    public function sanitize_style_settings($input)
    {
        $sanitized_input = array();
        // Sanitize Style Settings fields
        if (isset($input['cb_columns_desktop'])) {
            $sanitized_input['cb_columns_desktop'] = sanitize_text_field($input['cb_columns_desktop']);
        }
        if (isset($input['cb_columns_tablet'])) {
            $sanitized_input['cb_columns_tablet'] = sanitize_text_field($input['cb_columns_tablet']);
        }
        if (isset($input['cb_columns_mobile'])) {
            $sanitized_input['cb_columns_mobile'] = sanitize_text_field($input['cb_columns_mobile']);
        }
        if (isset($input['cb_background_color_field'])) {
            $sanitized_input['cb_background_color_field'] = sanitize_hex_color($input['cb_background_color_field']);
        }
        if (isset($input['cb_background_hover_color_field'])) {
            $sanitized_input['cb_background_hover_color_field'] = sanitize_hex_color($input['cb_background_hover_color_field']);
        }
        if (isset($input['cb_text_color_field'])) {
            $sanitized_input['cb_text_color_field'] = sanitize_hex_color($input['cb_text_color_field']);
        }
        if (isset($input['cb_text_hover_color_field'])) {
            $sanitized_input['cb_text_hover_color_field'] = sanitize_hex_color($input['cb_text_hover_color_field']);
        }
        if (isset($input['cb_border_radius_field'])) {
            $sanitized_input['cb_border_radius_field'] = sanitize_text_field($input['cb_border_radius_field']);
        }
        if (isset($input['cb_border_color_field'])) {
            $sanitized_input['cb_border_color_field'] = sanitize_hex_color($input['cb_border_color_field']);
        }
        if (isset($input['cb_border_hover_color_field'])) {
            $sanitized_input['cb_border_hover_color_field'] = sanitize_hex_color($input['cb_border_hover_color_field']);
        }
        if (isset($input['cb_title_font_size_field'])) {
            $sanitized_input['cb_title_font_size_field'] = sanitize_text_field($input['cb_title_font_size_field']);
        }
        if (isset($input['cb_description_font_size_field'])) {
            $sanitized_input['cb_description_font_size_field'] = sanitize_text_field($input['cb_description_font_size_field']);
        }
        if (isset($input['cb_buttons_font_size_field'])) {
            $sanitized_input['cb_buttons_font_size_field'] = sanitize_text_field($input['cb_buttons_font_size_field']);
        }
        if (isset($input['cb_style_hide_notice'])) {
            $sanitized_input['cb_style_hide_notice'] = $input['cb_style_hide_notice'] ? 1 : 0;
        }
        // Sanitize Style Settings fields END

        return $sanitized_input;
    }

    // Template Basic Field callback
    public function top_heading_callback()
    {
        echo '<h2>' . esc_html__('Store Setting', 'category-buttons') . '</h2>';
    }

    public function display_in_store_field_callback()
    {
        $options = get_option('cb_basic_settings');
        $checked = isset($options['cb_display_in_store_field']) ? (bool) $options['cb_display_in_store_field'] : false;
        echo '<input type="checkbox" name="cb_basic_settings[cb_display_in_store_field]" value="1"' . checked($checked, true, false) . ' />';
    }

    public function store_display_style_field_callback()
    {
        $options = get_option('cb_basic_settings');
        $selected_image = $options['cb_store_display_style_field'] ?? '';

        // List of images to choose from
        $images = array(
            'with-title-and-description' => array(
                'url' => CB_PLUGIN_URL . 'assets/images/store-display-style-images/With title and description.svg',
                'description' => __('With title and description', 'category-buttons')
            ),
            'with-title' => array(
                'url' => CB_PLUGIN_URL . 'assets/images/store-display-style-images/With title.svg',
                'description' => __('With title', 'category-buttons')
            ),
            'with-description' => array(
                'url' => CB_PLUGIN_URL . 'assets/images/store-display-style-images/With description.svg',
                'description' => __('With description', 'category-buttons')
            ),
            'without-title-and-description' => array(
                'url' => CB_PLUGIN_URL . 'assets/images/store-display-style-images/Without title and description.svg',
                'description' => __('Without title and description', 'category-buttons')
            ),
        );

        echo '<div class="image-select-wrapper">';
        foreach ($images as $value => $image) {
            $selected_class = ($selected_image === $value) ? 'selected' : '';
            echo '<div class="image-select-item ' . esc_attr($selected_class) . '" data-value="' . esc_attr($value) . '">';
            echo '<img src="' . esc_url($image['url']) . '" >';
            echo '<div class="image-select-description">' . esc_html($image['description']) . '</div>';
            echo '</div>';
        }
        echo '</div>';
        echo '<input type="hidden" name="cb_basic_settings[cb_store_display_style_field]" value="' . esc_attr($selected_image) . '">';
    }

    public function exclude_categories_callback()
    {
        $options = get_option('cb_basic_settings');
        $selected_categories = isset($options['cb_exclude_categories']) ? explode(',', $options['cb_exclude_categories']) : array();

        // Get all WooCommerce categories
        $categories = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ));

        echo '<div class="exclude-categories-wrapper">';
        if (!empty($categories) && !is_wp_error($categories)) {
            foreach ($categories as $category) {
                $checked = in_array($category->term_id, $selected_categories) ? 'checked' : '';
                echo '<label><input type="checkbox" name="cb_exclude_categories[]" value="' . esc_attr($category->term_id) . '" ' . $checked . '> ' . esc_html($category->name) . '</label>';
            }
        }
        echo '</div>';

        // Hidden field to store selected category IDs
        echo '<input type="hidden" name="cb_basic_settings[cb_exclude_categories]" value="' . esc_attr(implode(',', $selected_categories)) . '" id="cb_exclude_categories">';
    }

    public function display_images_field_callback()
    {
        $options = get_option('cb_basic_settings');
        $checked = isset($options['cb_display_images_field']) ? (bool) $options['cb_display_images_field'] : false;
        echo '<input type="checkbox" name="cb_basic_settings[cb_display_images_field]" value="1"' . checked($checked, true, false) . ' />';
    }

    public function custom_settings_heading_callback()
    {
        echo '<h2 style="margin:0;">' . esc_html__('Custom Settings', 'category-buttons') . '</h2>';
        echo '<p>' . esc_html__("By adding a custom title or description, you'll overwrite the category content with your text written here. It is global and will overwrite all titles and descriptions if used in this plugin.", 'category-buttons') . '</p>';
    }

    public function custom_title_field_callback()
    {
        $options = get_option('cb_basic_settings');
        echo '<input type="text" name="cb_basic_settings[cb_custom_title_field]" value="' . esc_attr($options['cb_custom_title_field'] ?? '') . '" />';
    }

    public function hide_notice_field_callback()
    {
        echo '<input type="hidden" name="cb_basic_settings[cb_hide_notice]" value="1" />';
    }

    public function custom_description_field_callback()
    {
        $options = get_option('cb_basic_settings');
        $content = $options['cb_custom_description_field'] ?? '';
        $editor_id = 'cb_custom_description_field';

        $settings = array(
            'textarea_name' => 'cb_basic_settings[cb_custom_description_field]',
            'editor_height' => 200,
            'tinymce' => array(
                'toolbar1' => 'bold,italic,underline,bullist,numlist,link,unlink,undo,redo',
                'toolbar2' => '',
            ),
            'media_buttons' => false,
            'quicktags' => array(
                'buttons' => 'strong,em,ul,ol,li,link,close',
            ),
        );

        wp_editor($content, $editor_id, $settings);
    }

    public function repeater_field_callback()
    {
        $options = get_option('cb_basic_settings');
        $repeater_values = $options['cb_repeater_field'] ?? array();

        echo '<div id="repeater-field-wrapper">';

        if (!empty($repeater_values)) {
            foreach ($repeater_values as $index => $field) {
                $this->repeater_field_template($index, $field);
            }
        } else {
            $this->repeater_field_template(0);
        }

        echo '</div>';
        echo '<button type="button" id="add-repeater-field" class="button">' . esc_html__('Add Field', 'category-buttons') . '</button>';
    }

    public function custom_repeater_heading_callback()
    {
        echo '<h2 style="margin:0;">' . esc_html__('Custom Buttons', 'category-buttons') . '</h2>';
        echo '<p>' . esc_html__("Add custom buttons that will appear at the end of the loop", 'category-buttons') . '</p>';
    }
    // Template Basic Field callback END

    // Template Shortcode Field callback
    public function shortcode_heading_callback()
    {
        echo '<h2 style="margin:0;">' . esc_html__('Shortcode Settings', 'category-buttons') . '</h2>';
    }

    public function shortcode_settings_callback()
    {
        $categories_shortcode = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ));

        // Allowed HTML tags and attributes
        $allowed_html = array(
            'div' => array(
                'class' => array(),
                'data-style' => array(),
                'data-category' => array(),
            ),
            'p' => array(
                'style' => array(),
            ),
            'b' => array(),
            'h3' => array(
                'style' => array(),
            ),
            'select' => array(
                'class' => array(),
            ),
            'h4' => array(
                'style' => array(),
            ),
            'option' => array(
                'value' => array(),
            ),
            'button' => array(
                'style' => array(),
                'id' => array(),
                'class' => array(),
            ),
            'img' => array(
                'width' => array(),
                'height' => array(),
                'src' => array(),
            ),
            'input' => array(
                'type' => array(),
                'name' => array(),
                'value' => array(),
            ),
        );

        $output  = '';
        $output .= '<h3 style="margin: -1rem 0 1rem 0">' . __('You can use our Elementor widget with many style options', 'category-buttons') . '</h3>';
        $output .= '<p style="margin: 1rem 0 0.5rem 0">' . esc_html__('Use shortcode', 'category-buttons') . ' <b>[woocb]</b> / <b>[woocb category="CATEGORY-ID"]</b> ' . esc_html__('You can use it in any short code supported environment from Gutenberg, Elementor or other page builders.', 'category-buttons') . '</p>';
        $output .= '<h3 style="margin: 1rem 0 1rem 0">' . __('Select a category and click to copy the shortcode of the desired style', 'category-buttons') . '</h3>';

        $output .= '<h3 style="margin: 1rem 0 .5rem  0">' . __('Select Category', 'category-buttons') . '</h3>';
        $output .= '<select class="shortcode-category-select">';
        $output .= '<option value="0">' . __('Select Category', 'category-buttons') . '</option>';

        foreach ($categories_shortcode as $category) {
            $output .= '<option value="' . esc_attr($category->term_id) . '">' . esc_html($category->name) . '</option>';
        }
        $output .= '</select>';

        $output .= '<h3 style="margin: 1rem 0 .5rem  0">' . __('Display images', 'category-buttons') . '</h3>';
        $output .= '<select class="shortcode-image-select">';
        $output .= '<option value="1">' . __('Yes', 'category-buttons') . '</option>';
        $output .= '<option value="0">' . __('No', 'category-buttons') . '</option>';
        $output .= '</select>';

        $output .= '<div class="short-copy-wrapper">';
        $output .= '<div data-style=\'display-style="with-title-and-description"\' class="short-copy-item">';
        $output .= '<img width="180" height="180" src="' . esc_url(CB_PLUGIN_URL . 'assets/images/store-display-style-images/With title and description.svg') . '">';
        $output .= '<div class="short-copy-description">' . esc_html__('With title and description', 'category-buttons') . '</div>';
        $output .= '</div>';
        $output .= '<div data-style=\'display-style="with-title"\' class="short-copy-item">';
        $output .= '<img width="180" height="180" src="' . esc_url(CB_PLUGIN_URL . 'assets/images/store-display-style-images/With title.svg') . '">';
        $output .= '<div class="short-copy-description">' . esc_html__('With title', 'category-buttons') . '</div>';
        $output .= '</div>';
        $output .= '<div data-style=\'display-style="with-description"\' class="short-copy-item ">';
        $output .= '<img width="180" height="180" src="' . esc_url(CB_PLUGIN_URL . 'assets/images/store-display-style-images/With description.svg') . '">';
        $output .= '<div class="short-copy-description">' . esc_html__('With description', 'category-buttons') . '</div>';
        $output .= '</div>';
        $output .= '<div data-style=\'display-style="without-title-and-description"\' class="short-copy-item ">';
        $output .= '<img width="180" height="180" src="' . esc_url(CB_PLUGIN_URL . 'assets/images/store-display-style-images/Without title and description.svg') . '">';
        $output .= '<div class="short-copy-description">' . esc_html__('Without title and description', 'category-buttons') . '</div>';
        $output .= '</div>';
        $output .= '</div>';

        echo wp_kses($output, $allowed_html);
    }
    // Template Shortcode Fields callback END

    // Template Style Fields callback
    public function style_settings_top_heading_callback()
    {
        echo '<h2>' . esc_html__('Column Settings', 'category-buttons') . '</h2>';
    }

    public function columns_desktop_callback()
    {
        $options = get_option('cb_style_settings');
        echo '<input type="number" name="cb_style_settings[cb_columns_desktop]" value="' . esc_attr($options['cb_columns_desktop'] ?? 4) . '" min="1" />';
    }

    public function columns_tablet_callback()
    {
        $options = get_option('cb_style_settings');
        echo '<input type="number" name="cb_style_settings[cb_columns_tablet]" value="' . esc_attr($options['cb_columns_tablet'] ?? 3) . '" min="1" />';
    }

    public function columns_mobile_callback()
    {
        $options = get_option('cb_style_settings');
        echo '<input type="number" name="cb_style_settings[cb_columns_mobile]" value="' . esc_attr($options['cb_columns_mobile'] ?? 1) . '" min="1" />';
    }

    public function style_color_settings_heading_callback()
    {
        echo '<h2>' . esc_html__('Color Settings', 'category-buttons') . '</h2>';
    }

    public function background_color_field_callback()
    {
        $options = get_option('cb_style_settings');
        echo '<input type="text" name="cb_style_settings[cb_background_color_field]" value="' . esc_attr($options['cb_background_color_field'] ?? '') . '" class="cb-color-field" />';
    }

    public function background_hover_color_field_callback()
    {
        $options = get_option('cb_style_settings');
        echo '<input type="text" name="cb_style_settings[cb_background_hover_color_field]" value="' . esc_attr($options['cb_background_hover_color_field'] ?? '') . '" class="cb-color-field" />';
    }

    public function text_hover_color_field_callback()
    {
        $options = get_option('cb_style_settings');
        echo '<input type="text" name="cb_style_settings[cb_text_hover_color_field]" value="' . esc_attr($options['cb_text_hover_color_field'] ?? '') . '" class="cb-color-field" />';
    }

    public function text_color_field_callback()
    {
        $options = get_option('cb_style_settings');
        echo '<input type="text" name="cb_style_settings[cb_text_color_field]" value="' . esc_attr($options['cb_text_color_field'] ?? '') . '" class="cb-color-field" />';
    }

    public function style_border_settings_heading_callback()
    {
        echo '<h2>' . esc_html__('Border Settings', 'category-buttons') . '</h2>';
    }

    public function border_radius_field_callback()
    {
        $options = get_option('cb_style_settings');
        echo '<input type="text" name="cb_style_settings[cb_border_radius_field]" value="' . esc_attr($options['cb_border_radius_field'] ?? '') . '" />';
    }

    public function border_color_field_callback()
    {
        $options = get_option('cb_style_settings');
        echo '<input type="text" name="cb_style_settings[cb_border_color_field]" value="' . esc_attr($options['cb_border_color_field'] ?? '') . '" class="cb-color-field" />';
    }

    public function border_hover_color_field_callback()
    {
        $options = get_option('cb_style_settings');
        echo '<input type="text" name="cb_style_settings[cb_border_hover_color_field]" value="' . esc_attr($options['cb_border_hover_color_field'] ?? '') . '" class="cb-color-field" />';
    }

    public function style_font_settings_heading_callback()
    {
        echo '<h2>' . esc_html__('Font Settings', 'category-buttons') . '</h2>';
    }
    public function title_font_size_field_callback()
    {
        $options = get_option('cb_style_settings');
        echo '<input type="text" name="cb_style_settings[cb_title_font_size_field]" value="' . esc_attr($options['cb_title_font_size_field'] ?? '') . '" />';
    }
    public function description_font_size_field_callback()
    {
        $options = get_option('cb_style_settings');
        echo '<input type="text" name="cb_style_settings[cb_description_font_size_field]" value="' . esc_attr($options['cb_description_font_size_field'] ?? '') . '" />';
    }
    public function buttons_font_size_field_callback()
    {
        $options = get_option('cb_style_settings');
        echo '<input type="text" name="cb_style_settings[cb_buttons_font_size_field]" value="' . esc_attr($options['cb_buttons_font_size_field'] ?? '') . '" />';
    }

    public function style_hide_notice_field_callback()
    {
        echo '<input type="hidden" name="cb_style_settings[cb_style_hide_notice]" value="1" />';
    }
    // Template Style Fields callback END

    private function repeater_field_template($index = 0, $field = array('image' => '', 'name' => '', 'url' => '', 'category' => ''))
    {
?>
        <div class="repeater-field-item">
            <div class="repeater-field-item-block">
                <label><?php esc_html_e('Image:', 'category-buttons'); ?></label>
                <input type="hidden" class="cb-image-url" name="cb_basic_settings[cb_repeater_field][<?php echo esc_attr($index); ?>][image]" value="<?php echo esc_attr($field['image']); ?>" />
                <button type="button" class="button cb-upload-image"><?php esc_html_e('Upload Image', 'category-buttons'); ?></button>
                <div class="image-wrapper">
                    <img src="<?php echo esc_url($field['image']); ?>" class="cb-image-preview" style="max-width: 100px; display: <?php echo $field['image'] ? 'block' : 'none'; ?>;" />
                    <button type="button" class="button cb-remove-image" style="display: <?php echo $field['image'] ? 'inline-block' : 'none'; ?>;">X</button>
                </div>
            </div>

            <div class="repeater-field-item-block">
                <label><?php esc_html_e('Custom Name:', 'category-buttons'); ?></label>
                <input type="text" name="cb_basic_settings[cb_repeater_field][<?php echo esc_attr($index); ?>][name]" value="<?php echo esc_attr($field['name']); ?>" />
            </div class="repeater-field-item-block">

            <div class="repeater-field-item-block">
                <label><?php esc_html_e('Custom URL:', 'category-buttons'); ?></label>
                <input type="text" name="cb_basic_settings[cb_repeater_field][<?php echo esc_attr($index); ?>][url]" value="<?php echo esc_attr($field['url']); ?>" />
            </div>

            <div class="repeater-field-item-block">
                <label><?php esc_html_e('Display under', 'category-buttons'); ?></label>
                <select class="repeater-category-select">
                    <option value="00"><?php esc_html_e('Select Category', 'category-buttons'); ?></option>
                    <?php
                    $categories_repeater = get_terms(array(
                        'taxonomy' => 'product_cat',
                        'hide_empty' => false,
                    ));

                    foreach ($categories_repeater as $category) {
                        $selected = ($field['category'] == $category->term_id) ? 'selected' : '';
                        echo '<option value="' . esc_attr($category->term_id) . '" ' . esc_attr($selected) . '>' . esc_html($category->name) . '</option>';
                    }
                    ?>
                </select>
                <input class="repeater-category-input" type="hidden" name="cb_basic_settings[cb_repeater_field][<?php echo esc_attr($index); ?>][category]" value="<?php echo esc_attr($field['category']); ?>" />
            </div>

            <div class="repeater-field-item-block">
                <label>&nbsp;</label>
                <button type="button" style="<?php echo ($index == 0) ? 'display: none;' : 'display: block;'; ?>" class="button remove-repeater-field"><?php esc_html_e('Remove', 'category-buttons'); ?></button>
            </div>
        </div>
    <?php
    }


    public function admin_index()
    {
    ?>
        <div class="wrap cb-wrap">
            <h1 style="display: flex;align-items: center;gap: 15px;font-size: 2.5em;margin-bottom:1rem;"><img width="50" height="50" src="<? echo esc_url(CB_PLUGIN_URL . '/assets/images/icon-256x256.png') ?>" alt="<? echo esc_html__('Woocommerce Category Buttons', 'category-buttons'); ?>"><?php echo esc_html__('Woocommerce Category Buttons', 'category-buttons'); ?></h1>

            <!-- Tabs for admin page -->
            <h2 class="nav-tab-wrapper">
                <a href="?page=woocommerce-category-buttons" class="nav-tab <?php echo esc_attr($this->get_active_tab('basic')); ?>"><?php esc_html_e('Basic Settings', 'category-buttons'); ?></a>
                <a href="?page=woocommerce-category-buttons&tab=shortcode" class="nav-tab <?php echo esc_attr($this->get_active_tab('shortcode')); ?>"><?php esc_html_e('Shortcode Settings', 'category-buttons'); ?></a>
                <a href="?page=woocommerce-category-buttons&tab=styling" class="nav-tab <?php echo esc_attr($this->get_active_tab('styling')); ?>"><?php esc_html_e('Style Settings', 'category-buttons'); ?></a>
                <a href="?page=woocommerce-category-buttons&tab=support" class="nav-tab <?php echo esc_attr($this->get_active_tab('support')); ?>"><?php esc_html_e('Support', 'category-buttons'); ?></a>
            </h2>

            <!-- Admin page content -->
            <?php
            settings_fields('cb_basic_settings_group');
            wp_nonce_field('cb_basic_settings_nonce', 'cb_basic_settings_nonce_field');
            settings_fields('cb_style_settings_group');
            wp_nonce_field('cb_style_settings_nonce', 'cb_style_settings_nonce_field');

            // Default Tab
            $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'basic';

            // Support Tab
            if ($tab === 'support') {
                if (isset($_GET['form_status'])) {
                    if ($_GET['form_status'] == 'success') {
                        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Your message was sent successfully.', 'category-buttons') . '</p></div>';
                    } elseif ($_GET['form_status'] == 'error') {
                        echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('There was an error sending your message. Please ensure all fields are filled in.', 'category-buttons') . '</p></div>';
                    }
                }
            ?>
                <div class="cb-container">
                    <?php echo '<h2 style="font-size: 22px; margin: 1rem 0;">' . esc_html__('Contact us using the form or contact information below', 'category-buttons') . '</h2>'; ?>
                    <?php echo '<p> Email: ' . wp_kses('<a href="mailto:ryvenia@ryvenia.sk">ryvenia@ryvenia.sk</a>', array('a' => array('href' => array()),)) . '</p>'; ?>
                    <form class="cb-support-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="send_contact_form">
                        <?php wp_nonce_field('send_contact_form_nonce', 'send_contact_form_nonce_field'); ?>
                        <label for="name"><?php echo esc_html__('Name:', 'category-buttons'); ?></label>
                        <input type="text" id="name" name="name" required>
                        <label for="email"><?php echo esc_html__('Email:', 'category-buttons'); ?></label>
                        <input type="email" id="email" name="email" required>
                        <label for="message"><?php echo esc_html__('Message:', 'category-buttons'); ?></label>
                        <textarea id="message" name="message" required></textarea>
                        <label for="send_system_status">
                            <input type="checkbox" id="send_system_status" name="send_system_status" value="1">
                            <?php esc_html_e('Send WordPress system status', 'category-buttons'); ?>
                        </label>
                        <label for="consent">
                            <input type="checkbox" id="consent" name="consent" value="1" required>
                            <?php echo wp_kses(
                                __('I agree with <a target="_blank" href="https://ryvenia.sk/ochrana-osobnych-udajov">privacy policy</a>', 'category-buttons'),
                                array(
                                    'a' => array(
                                        'href' => array(),
                                        'target' => array(),
                                    ),
                                )
                            ); ?>

                        </label>
                        <input type="submit" name="submit_support_form" value="<? echo esc_attr(__('Send', 'category-buttons')); ?>">
                    </form>
                </div>
            <?php
            }
            // Settings Tabs
            else {
            ?>
                <div class="cb-container">
                    <form action="options.php" method="post">
                        <?php

                        switch ($tab) {
                            case 'basic':
                                do_settings_sections('woocommerce-category-buttons');
                                settings_fields('cb_basic_settings_group');

                                submit_button(__('Save Changes'), 'primary', 'cb-button', true, array('id' => 'cb-button'));
                                break;

                            case 'shortcode':
                                do_settings_sections('woocommerce-category-buttons-shortcode');
                                settings_fields('cb_basic_settings_group');
                                break;

                            case 'styling':
                                do_settings_sections('woocommerce-category-buttons-styling');
                                settings_fields('cb_style_settings_group');

                                submit_button(__('Save Changes'), 'primary', 'cb-button', true, array('id' => 'cb-button'));
                                break;
                        }
                        ?>
                    </form>
                </div>
            <?php
            }
            ?>
        </div>
<?php
    }

    // Handle Active Tab
    private function get_active_tab($tab)
    {
        $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'basic';
        return $current_tab == $tab ? 'nav-tab-active' : '';
    }

    // Handle Support Mail
    public function handle_contact_form_submission()
    {
        // Check nonce for security
        if (!isset($_POST['send_contact_form_nonce_field']) || !wp_verify_nonce($_POST['send_contact_form_nonce_field'], 'send_contact_form_nonce')) {
            wp_redirect(add_query_arg('form_status', 'error', wp_get_referer()));
            exit;
        }

        // Function to get WordPress system status
        function get_wp_system_status()
        {
            global $wpdb;

            // Get WordPress version
            $wp_version = get_bloginfo('version');

            // Get PHP version
            $php_version = phpversion();

            // Get MySQL version
            $mysql_version = $wpdb->db_version();

            // Get server information
            $server_info = $_SERVER['SERVER_SOFTWARE'];

            // Get active theme information
            $theme = wp_get_theme();
            $theme_name = $theme->get('Name');
            $theme_version = $theme->get('Version');

            // Get active plugins information
            $active_plugins = get_option('active_plugins');
            $plugins = [];
            foreach ($active_plugins as $plugin) {
                $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
                $plugins[] = [
                    'name' => $plugin_data['Name'],
                    'version' => $plugin_data['Version']
                ];
            }

            // Get WordPress site information
            $site_url = get_site_url();
            $home_url = get_home_url();

            // Compile system status information
            $system_status = [
                'WordPress Version' => $wp_version,
                'PHP Version' => $php_version,
                'MySQL Version' => $mysql_version,
                'Server Info' => $server_info,
                'Active Theme' => [
                    'name' => $theme_name,
                    'version' => $theme_version
                ],
                'Active Plugins' => $plugins,
                'Site URL' => $site_url,
                'Home URL' => $home_url
            ];

            return $system_status;
        }

        // Format system status information as a string
        function format_system_status($status)
        {
            $formatted = "System Status:\n";
            $formatted .= "WordPress Version: " . esc_html($status['WordPress Version']) . "\n";
            $formatted .= "PHP Version: " . esc_html($status['PHP Version']) . "\n";
            $formatted .= "MySQL Version: " . esc_html($status['MySQL Version']) . "\n";
            $formatted .= "Server Info: " . esc_html($status['Server Info']) . "\n";
            $formatted .= "Active Theme: " . esc_html($status['Active Theme']['name']) . " (Version: " . esc_html($status['Active Theme']['version']) . ")\n";
            $formatted .= "Active Plugins:\n";
            foreach ($status['Active Plugins'] as $plugin) {
                $formatted .= "- " . esc_html($plugin['name']) . " (Version: " . esc_html($plugin['version']) . ")\n";
            }
            $formatted .= "Site URL: " . esc_url($status['Site URL']) . "\n";
            $formatted .= "Home URL: " . esc_url($status['Home URL']) . "\n";
            return $formatted;
        }

        // Check if the form is submitted
        if (isset($_POST['submit_support_form'])) {
            // Sanitize and validate form input
            $name    = sanitize_text_field($_POST['name']);
            $email   = sanitize_email($_POST['email']);
            $message = sanitize_textarea_field($_POST['message']);
            $send_system_status = isset($_POST['send_system_status']) ? 1 : 0;

            // Validate email
            if (!is_email($email)) {
                wp_redirect(add_query_arg('form_status', 'error', wp_get_referer()));
                exit;
            }

            // Prepare email
            $to      = 'ryvenia@ryvenia.sk'; // Send to the admin email
            $subject = 'New message from ' . $name;
            $body    = 'Name: ' . $name . "\n";
            $body   .= 'Email: ' . $email . "\n\n";
            $body   .= 'Message: ' . $message . "\n\n";
            if ($send_system_status == 1) {
                $status = get_wp_system_status();
                $formatted_status = format_system_status($status);
                $body   .= $formatted_status;
            }
            $headers = array('Content-Type: text/plain; charset=UTF-8');

            // Send email
            if (wp_mail($to, $subject, $body, $headers)) {
                wp_redirect(add_query_arg('form_status', 'success', wp_get_referer()));
            } else {
                wp_redirect(add_query_arg('form_status', 'error', wp_get_referer()));
            }
            exit;
        }
    }
}

Admin::get_instance();
