<?php

namespace EACF7;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

defined('ABSPATH') || exit();

class Popup_Form_Widget extends Widget_Base {

    public function get_name() {
        return 'popup_form';
    }

    public function get_title() {
        return __('Popup Form', 'essential-addons-for-contact-form-7');
    }

    public function get_icon() {
        return 'eicon-lightbox';
    }

    public function get_categories() {
        return ['eacf7'];
    }

    public function get_keywords() {
        return [
            "popup form",
            "popup",
            "form",
            "contact",
            "contact form 7",
            "cf7 popup form"
        ];
    }

    public function get_script_depends() {
        return [
            'igd-frontend',
        ];
    }

    public function get_style_depends() {
        return [
            'igd-frontend',
        ];
    }

    public function register_controls() {

        // form control section start here
        $this->start_controls_section(
            '_section_module_popup_form',
            [
                'label' => __('Form', 'essential-addons-for-contact-form-7'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'form_id',
            [
                'label'       => __('Contact Form 7', 'essential-addons-for-contact-form-7'),
                'type'        => Controls_Manager::SELECT,
                'label_block' => true,
                'options'     => ['' => __('Select Contact Form', 'essential-addons-for-contact-form-7')] + eacf7_get_forms(),
            ]
        );

        $this->end_controls_section();
        // form control section stop here

        // button control section start here
        $this->start_controls_section(
            '_section_module_popup_form_btn',
            [
                'label' => __('Button', 'essential-addons-for-contact-form-7'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'btn_text',
            [
                'label' => esc_html__('Button Text', 'essential-addons-for-contact-form-7'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => esc_html__('Leave a Message', 'essential-addons-for-contact-form-7'),
                'placeholder' => esc_html__('Type your button text here.', 'essential-addons-for-contact-form-7'),
            ]
        );

        $this->add_control(
            'btn_icon',
            [
                'label' => esc_html__('Button Icon', 'essential-addons-for-contact-form-7'),
                'type' => \Elementor\Controls_Manager::ICONS,
                'default' => [
                    'value' => 'fas fa-circle',
                    'library' => 'fa-solid',
                ],
                'recommended' => [
                    'fa-solid' => [
                        'circle',
                        'dot-circle',
                        'square-full',
                    ],
                    'fa-regular' => [
                        'circle',
                        'dot-circle',
                        'square-full',
                    ],
                ],
            ]
        );

        $this->add_control(
            'btn_style',
            [
                'label'       => __('Button Style', 'essential-addons-for-contact-form-7'),
                'type'        => Controls_Manager::SELECT,
                'default'     => 'global',
                'label_block' => true,
                'options'     => [
                    'global' => __('Default', 'essential-addons-for-contact-form-7'),
                    '2' => __('Stok', 'essential-addons-for-contact-form-7'),
                    '3' => __('Low', 'essential-addons-for-contact-form-7'),
                    '4' => __('Text', 'essential-addons-for-contact-form-7'),
                    '5' => __('Shadow', 'essential-addons-for-contact-form-7'),
                    'custom' => __('Custom', 'essential-addons-for-contact-form-7'),
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'selector' => '{{WRAPPER}} .eacf7-popup-form-button',
            ]
        );

        $this->add_responsive_control(
            'btn_icon_size',
            [
                'type' => \Elementor\Controls_Manager::SLIDER,
                'label' => esc_html__('Icon size', 'essential-addons-for-contact-form-7'),
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'devices' => ['desktop', 'tablet', 'mobile'],
                'desktop_default' => [
                    'size' => 24,
                    'unit' => 'px',
                ],
                'tablet_default' => [
                    'size' => 20,
                    'unit' => 'px',
                ],
                'mobile_default' => [
                    'size' => 10,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .eacf7-popup-form-button svg' => 'width: {{SIZE}}{{UNIT}} !important;',
                    '{{WRAPPER}} .eacf7-popup-form-button svg' => 'height: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_control(
            'color_options',
            [
                'label' => esc_html__('Color Options', 'essential-addons-for-contact-form-7'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'after',
                'condition' => [
                    'btn_style' => ['custom'],
                ],
            ]
        );

        $this->add_control(
            'text_color',
            [
                'label' => esc_html__('Text Color', 'essential-addons-for-contact-form-7'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'condition' => [
                    'btn_style' => ['custom'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eacf7-popup-form-button' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'text_hover_color',
            [
                'label' => esc_html__('Text Hover Color', 'essential-addons-for-contact-form-7'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'condition' => [
                    'btn_style' => ['custom'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eacf7-popup-form-button:hover' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => esc_html__('Icon Color', 'essential-addons-for-contact-form-7'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eacf7-popup-form-button svg' => 'fill: {{VALUE}} !important',
                    '{{WRAPPER}} .eacf7-popup-form-button svg path' => 'fill: {{VALUE}} !important',
                ],
            ]
        );

        $this->add_control(
            'icon_hover_color',
            [
                'label' => esc_html__('Icon Hover Color', 'essential-addons-for-contact-form-7'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .eacf7-popup-form-button:hover svg' => 'fill: {{VALUE}} !important',
                    '{{WRAPPER}} .eacf7-popup-form-button:hover svg path' => 'fill: {{VALUE}} !important',
                ],
            ]
        );

        $this->add_control(
            'bg_color',
            [
                'label' => esc_html__('Background Color', 'essential-addons-for-contact-form-7'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'condition' => [
                    'btn_style' => ['custom'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eacf7-popup-form-button' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'bg_hover_color',
            [
                'label' => esc_html__('Background Hover Color', 'essential-addons-for-contact-form-7'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'condition' => [
                    'btn_style' => ['custom'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eacf7-popup-form-button:hover' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'border_options',
            [
                'label' => esc_html__('Border Options', 'essential-addons-for-contact-form-7'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'after',
                'condition' => [
                    'btn_style' => ['custom'],
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'border',
                'selector' => '{{WRAPPER}} .eacf7-popup-form-button',
            ]
        );

        $this->add_control(
            'btn_border_hover_color',
            [
                'label' => esc_html__('Border Hover Color', 'essential-addons-for-contact-form-7'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'condition' => [
                    'btn_style' => ['custom'],
                ],
                'selectors' => [
                    '{{WRAPPER}} .eacf7-popup-form-button:hover' => 'border-color: {{VALUE}} !important',
                ],
            ]
        );

        $this->add_control(
            'btn_size',
            [
                'label'       => __('Button Style', 'essential-addons-for-contact-form-7'),
                'type'        => Controls_Manager::SELECT,
                'default'     => 'medium',
                'label_block' => true,
                'options'     => [
                    'small' => __('Small', 'essential-addons-for-contact-form-7'),
                    'medium' => __('Medium', 'essential-addons-for-contact-form-7'),
                    'large' => __('Large', 'essential-addons-for-contact-form-7'),
                ],
            ]
        );

        $this->add_control(
            'btn_alignment',
            [
                'label' => esc_html__('Button Alignment', 'essential-addons-for-contact-form-7'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'essential-addons-for-contact-form-7'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', 'essential-addons-for-contact-form-7'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'essential-addons-for-contact-form-7'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => 'flex-start',
                'toggle' => true,
            ]
        );

        $this->add_control(
            'btn_class',
            [
                'label' => esc_html__('Button class', 'essential-addons-for-contact-form-7'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__('Type your class here', 'essential-addons-for-contact-form-7'),
            ]
        );

        $this->end_controls_section();
        // button control section stop here

        // popup control section start here
        $this->start_controls_section(
            '_section_module_popup_form_popup',
            [
                'label' => __('Popup', 'essential-addons-for-contact-form-7'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'popup_width',
            [
                'label' => esc_html__('Popup Width', 'essential-addons-for-contact-form-7'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'min' => 300,
                'max' => 1000,
                'step' => 5,
                'default' => 500,
            ]
        );

        $this->add_control(
            'popup_outside_click',
            [
                'label' => esc_html__('Popup Outside Click', 'essential-addons-for-contact-form-7'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Enable', 'essential-addons-for-contact-form-7'),
                'label_off' => esc_html__('Disable', 'essential-addons-for-contact-form-7'),
                'return_value' => 1,
                'default' => 1,
            ]
        );

        $this->add_control(
            'popup_class',
            [
                'label' => esc_html__('Popup class', 'essential-addons-for-contact-form-7'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => esc_html__('Type your class here', 'essential-addons-for-contact-form-7'),
            ]
        );

        $this->end_controls_section();
        // popup control section stop here
    }

    public function render() {
        $settings     = $this->get_settings_for_display();
        $form_id = ! empty($settings['form_id']) ? intval($settings['form_id']) : '';
        $btn_text = ! empty($settings['btn_text']) ? sanitize_text_field($settings['btn_text']) : sanitize_text_field(eacf7_get_popup_settings('buttonText'));
        $btn_icon = ! empty($settings['btn_icon']) ? sanitize_text_field($settings['btn_icon']) : '';
        $btn_class = ! empty($settings['btn_class']) ? sanitize_text_field($settings['btn_class']) : '';
        $btn_style = ! empty($settings['btn_style']) ? sanitize_text_field($settings['btn_style']) : '';
        $btn_size = ! empty($settings['btn_size']) ? sanitize_text_field($settings['btn_size']) : sanitize_text_field(eacf7_get_popup_settings('buttonSize', 'medium'));
        $btn_alignment = ! empty($settings['btn_alignment']) ? sanitize_text_field($settings['btn_alignment']) : 'left';
        $popup_width = ! empty($settings['popup_width']) ? intval($settings['popup_width']) : intval(eacf7_get_popup_settings('popupWidth', 500)) . sanitize_text_field(eacf7_get_popup_settings('popupWidthUnit', 'px'));
        $popup_outside_click = ! empty($settings['popup_outside_click']) ? intval($settings['popup_outside_click']) : intval(eacf7_get_popup_settings('popupOutsideClick', 1));
        $popup_class = ! empty($settings['popup_class']) ? sanitize_text_field($settings['popup_class']) : '';

        if (! empty($form_id)) {
?>
            <div class="eacf7-popup-form-button-wrap <?php echo esc_attr($btn_alignment); ?>">
                <button
                    type="button"
                    class="button eacf7-button eacf7-popup-form-button style-<?php echo esc_attr($btn_style) . ' ' . esc_attr($btn_class) . ' ' . esc_attr($btn_size);  ?>"
                    id="eacf7-button-<?php echo esc_attr($form_id); ?>"
                    data-id="<?php echo esc_attr($form_id); ?>"
                    data-click="<?php echo esc_attr($popup_outside_click); ?>"
                    data-width="<?php echo esc_attr($popup_width); ?>"
                    data-class="<?php echo esc_attr($popup_class); ?>">
                    <?php \Elementor\Icons_Manager::render_icon($btn_icon, ['aria-hidden' => 'true']); ?>
                    <?php echo esc_html($btn_text) ?>
                </button>
            </div>
            <?php
        } else {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) { ?>
                <div class="eacf7-popup-form">
                    <h3><?php echo esc_html('Contact Form 7 Popup Form', 'essential-addons-for-contact-form-7'); ?></h3>
                    <p><?php echo esc_html('Please select a from the widget settings.', 'essential-addons-for-contact-form-7'); ?></p>
                </div>
<?php }
        }
    }
}
