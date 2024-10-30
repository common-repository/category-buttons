<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Category_Buttons_Widget extends \Elementor\Widget_Base
{
    public function get_name()
    {
        return 'category_buttons';
    }

    public function get_title()
    {
        return esc_html__('Category Buttons', 'category-buttons');
    }

    public function get_icon()
    {
        return 'eicon-button';
    }

    public function get_categories()
    {
        return ['basic'];
    }

    public function get_keywords()
    {
        return ['hello', 'world'];
    }

    protected function register_controls()
    {
        // Fetch WooCommerce categories
        $categories = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ]);

        $categories_options = [];
        if (!is_wp_error($categories)) {
            foreach ($categories as $category) {
                $categories_options[$category->term_id] = $category->name;
            }
        }

        // Basic Settings
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__('Basic Settings', 'category-buttons'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'display_category',
            [
                'label' => esc_html__('Display Category', 'category-buttons'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $categories_options,
                'multiple' => false,
                'default' => '',
            ]
        );

        $this->add_control(
            'display_style',
            [
                'label' => esc_html__('Display Style', 'category-buttons'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => [
                    'with-title-and-description' => esc_html__('With Title and Description', 'category-buttons'),
                    'with-title' => esc_html__('With Title', 'category-buttons'),
                    'with-description' => esc_html__('With Description', 'category-buttons'),
                    'without-title-and-description' => esc_html__('Without Title and Description', 'category-buttons'),
                ],
                'default' => 'with-title-and-description',
            ]
        );

        $this->add_control(
            'exclude_categories',
            [
                'label' => esc_html__('Exclude Categories', 'category-buttons'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $categories_options,
                'multiple' => true,
                'default' => [],
            ]
        );

        $this->add_control(
            'display_images',
            [
                'label' => esc_html__('Display Images', 'category-buttons'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'category-buttons'),
                'label_off' => esc_html__('No', 'category-buttons'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();
        // Content Tab End

        // Column Settings
        $this->start_controls_section(
            'section_content',
            [
                'label' => esc_html__('Column Settings', 'category-buttons'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        // Column Settings Section Title
        $this->add_control(
            'column_settings_title',
            [
                'label' => esc_html__('Column Settings', 'category-buttons'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => __( 'Columns', 'category-buttons' ),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'devices' => [ 'desktop', 'tablet', 'mobile' ],
                'desktop_default' => 4,
                'tablet_default' => 3,
                'mobile_default' => 2,
                'selectors' => [
                    '{{WRAPPER}} .cb-category-buttons' => 'grid-template-columns: repeat({{SIZE}}, 1fr);',
                ],
            ]
        );

        $this->end_controls_section();
        // Content Tab End

        // Style Tab Start
        $this->start_controls_section(
            'section_style',
            [
                'label' => esc_html__('Style', 'category-buttons'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'background_color',
            [
                'label' => esc_html__('Background Color', 'category-buttons'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-category-button' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'background_color_hover',
            [
                'label' => esc_html__('Background Hover Color', 'category-buttons'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-category-button:hover' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__('Text Color', 'category-buttons'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-category-button' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'text_color_hover',
            [
                'label' => esc_html__('Text Hover Color', 'category-buttons'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-category-button:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        // Border Settings Section Title
        $this->add_control(
            'border_settings_title',
            [
                'label' => esc_html__('Border Settings', 'category-buttons'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

		$this->add_control(
			'border_radius',
			[
				'label' => esc_html__( 'Border radius', 'category-buttons' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'default' => [
					'top' => 4,
					'right' => 4,
					'bottom' => 4,
					'left' => 4,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .cb-category-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

        $this->add_control(
            'border_color',
            [
                'label' => esc_html__('Border Color', 'category-buttons'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-category-button' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'border_color_hover',
            [
                'label' => esc_html__('Border Hover Color', 'category-buttons'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cb-category-button:hover' => 'border-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__( 'Title Typography', 'category-buttons' ),
                'selector' => '{{WRAPPER}} .cb-category-buttons a',
            ]
        );

        $this->end_controls_section();
        // Style Tab End
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $display_style = $settings['display_style'];
        $exclude_categories = !empty($settings['exclude_categories']) ? implode(',', $settings['exclude_categories']) : '';
        $display_images = $settings['display_images'] === 'yes' ? '1' : '0';
        $display_category = $settings['display_category'];
        
        echo '<div class="shortcode-preview">' . do_shortcode(
            '[woocb display-style="' . esc_attr($display_style) . '" ' . 'exclude-categories="' . esc_attr($exclude_categories) . '" ' . 'display-images="' . esc_attr($display_images) . '"' . ' category="' .  esc_attr($display_category) . '"]'
        ) . '</div>';
    }

}
