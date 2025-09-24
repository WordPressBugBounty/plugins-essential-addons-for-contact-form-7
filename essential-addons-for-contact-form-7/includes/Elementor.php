<?php

namespace EACF7;

defined('ABSPATH') || exit;

class Elementor {
    /**
     * @var null
     */
    protected static $instance = null;

    public function __construct() {

        // Check if Elementor installed and activated
        if (! did_action('elementor/loaded')) {
            return;
        }

        // Register default widgets
        add_action('elementor/elements/categories_registered', [$this, 'add_categories']);

        if (defined('ELEMENTOR_VERSION')) {
            if (version_compare(ELEMENTOR_VERSION, '3.5.0', '>=')) {
                add_action('elementor/widgets/register', [$this, 'register_widgets']);
            } else {
                add_action('elementor/widgets/widgets_registered', array($this, 'register_widgets'));
            }
        }
    }

    /**
     * Register Widget
     */
    public function register_widgets($widgets_manager) {

        $features = eacf7_get_settings('features');
        $active_popup_form = in_array('popup-form', $features);

        include_once EACF7_INCLUDES . '/elementor/ContactForm.php';

        if( $active_popup_form ){
            include_once EACF7_INCLUDES . '/elementor/PopupForm.php';
        }

        if (method_exists($widgets_manager, 'register')) {
            $widgets_manager->register(new Contact_Form_Widget());

            // popup form
            if ($active_popup_form) {
                $widgets_manager->register(new Popup_Form_Widget());
            }
        } else {
            $widgets_manager->register_widget_type(new Contact_Form_Widget());

            // popup form
            if( $active_popup_form){
                $widgets_manager->register_widget_type(new Popup_Form_Widget());
            }
        }
    }

    public function add_categories($elements_manager) {
        $elements_manager->add_category(
            'eacf7',
            [
                'title' => __('Essential Addons for Contact form 7', 'essential-addons-for-contact-form-7'),
                'icon'  => 'fa fa-plug',
            ]
        );
    }

    /**
     * @return Elementor|null
     */
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

Elementor::instance();
