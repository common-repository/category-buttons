<?php

namespace Category_Buttons;

if (!defined('ABSPATH')) {
    exit;
}

function cb_category_buttons_shortcode($atts)
{

    $atts = shortcode_atts(array(
        'display-style' => '',
        'exclude-categories' => '',
        'display-images' => '',
        'category' => 0,
    ), $atts, 'category_buttons');

    global $wp_query;
    if (empty($wp_query->queried_object->term_id)) {
        $parent = $atts['category'];
    } else {
        $parent = $wp_query->queried_object->term_id;
    }

    // Convert the exclude-categories attribute to an array of IDs
    $exclude_categories = !empty($atts['exclude-categories']) ? array_map('intval', explode(',', $atts['exclude-categories'])) : array();

    $categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'parent' => $parent,
        'exclude' => $exclude_categories,
    ));

    // Retrieve the basic settings
    $basic_settings = get_option('cb_basic_settings');

    $term = get_term($atts['category'], 'product_cat');

    $single_tag_title = single_tag_title("", false);

    if (!empty($single_tag_title)) {
        $cat_title = $single_tag_title;
    } else {
        $cat_title = wp_kses_post($basic_settings['cb_custom_title_field'] != '' ? $basic_settings['cb_custom_title_field'] : sanitize_text_field($term->name));
    }

    $cat_desc = wp_kses_post($basic_settings['cb_custom_description_field'] != '' ? $basic_settings['cb_custom_description_field'] : term_description($atts['category'], 'product_cat'));

    $output = '';
    switch ($atts['display-style']) {
        case 'with-title-and-description':
            $output .= wp_kses_post('<div class="cb-category-title"><h1>' . esc_html($cat_title) . '</h1></div>');
            $output .= wp_kses_post('<p class="cb-category-desc">' . $cat_desc . '</p>');
            break;
        case 'with-title':
            $output .= wp_kses_post('<div class="cb-category-title"><h1>' . esc_html($cat_title) . '</h1></div>');
            break;
        case 'with-description':
            $output .= wp_kses_post('<p class="cb-category-desc">' . $cat_desc . '</p>');
            break;
        case 'without-title-and-description':
            break;
        default:
            //code block
    }


    $output .= '<div class="cb-category-buttons">';
    if (!empty($categories)) {

        // Loop categories
        foreach ($categories as $category) {
            $image_html = '';
            $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
            $image = wp_get_attachment_url($thumbnail_id);
            if ($image && $atts['display-images'] == 1) {
                $image_html = '<img src="' . esc_url($image) . '" alt="' . esc_attr($category->name) . '">';
            }
            $output .= '<a href="' . get_term_link($category) . '" class="cb-category-button">' . $image_html . $category->name . '</a>';
        }
        // Loop categories END
    }

    // Loop custom repeater fields
    if (isset($basic_settings['cb_repeater_field'])) {
        foreach ($basic_settings['cb_repeater_field'] as $repeater_item) {
            if ($repeater_item['category'] == $parent || $repeater_item['category'] == 00) {
                if ($repeater_item['name'] != '') {
                    if ($repeater_item['image'] != '' && $atts['display-images'] == 1) {
                        $repeater_image_html = '<img src="' . esc_url($repeater_item['image']) . '" alt="' . esc_attr($repeater_item['name']) . '">';
                        $output .= '<a href="' . $repeater_item['url'] . '" class="cb-category-button">' . $repeater_image_html . $repeater_item['name'] . '</a>';
                    } else {
                        $output .= '<a href="' . $repeater_item['url'] . '" class="cb-category-button">' . $repeater_item['name'] . '</a>';
                    }
                }
            }
        }
    }
    // Loop custom repeater fields END


    $output .= '</div>';
    return $output;
}
add_shortcode('woocb', 'Category_Buttons\cb_category_buttons_shortcode');
