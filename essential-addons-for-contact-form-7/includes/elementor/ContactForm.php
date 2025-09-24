<?php

namespace EACF7;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

defined('ABSPATH') || exit();

class Contact_Form_Widget extends Widget_Base {

    public function get_name() {
        return 'cf7';
    }

    public function get_title() {
        return __('Contact Form 7', 'essential-addons-for-contact-form-7');
    }

    public function get_icon() {
        return 'eicon-form-horizontal';
    }

    public function get_categories() {
        return ['eacf7'];
    }

    public function get_keywords() {
        return [
            "contact",
            "form",
            "contact form",
            "contact form 7",
            "cf7"
        ];
    }

    public function get_script_depends() {
        return [
            '',
        ];
    }

    public function get_style_depends() {
        return [
            '',
        ];
    }

    public function register_controls() {

        // form control section start here
        $this->start_controls_section(
            '_section_module_contact_form',
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
    }

    public function render() {
        $settings     = $this->get_settings_for_display();
        $form_id = ! empty($settings['form_id']) ? intval($settings['form_id']) : '';

        if (! empty($form_id)) {
            echo do_shortcode('[contact-form-7 id="'.$form_id.'"]');
        } else {
            if (\Elementor\Plugin::$instance->editor->is_edit_mode()) { ?>
                <div class="eacf7-contact-form" style="border: 1px dashed #bfbfbf;padding: 50px;text-align: center;">
                    <h3><?php echo esc_html('Contact Form 7', 'essential-addons-for-contact-form-7'); ?></h3>
                    <p style="margin-bottom: 0;"><?php echo esc_html('Please select a from to display form.', 'essential-addons-for-contact-form-7'); ?></p>
                </div>
<?php }
        }
    }
}
