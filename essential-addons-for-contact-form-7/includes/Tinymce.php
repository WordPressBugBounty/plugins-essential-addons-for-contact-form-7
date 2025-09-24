<?php

namespace CF7_Extended;

if (! defined('ABSPATH')) exit;

class Tinymce {

    private static $instance = null;

    public function __construct() {
        //add contact form button
        add_action('media_buttons', [$this, 'add_button'], 20);
    }

    public function add_button() {
        if (! function_exists('get_current_screen')) {
            return;
        }

        $screen = get_current_screen();

        if (! empty($screen) && ($screen->base !== 'post')) {
            return;
        }

        printf(
            '<a href="#" class="button" id="eacf7-contact-form" title="%1$s"><img width="20" src="%2$s/images/eacf7.svg" /> %1$s</a>',
            __('Contact Form 7', 'essential-addons-for-contact-form-7'),
            EACF7_ASSETS
        );
    }


    public static function instance() {
        if (null === self::$instance) {
            self::$instance = new self;
        }

        return self::$instance;
    }
}

Tinymce::instance();
