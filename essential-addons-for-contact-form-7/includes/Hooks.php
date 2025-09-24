<?php

namespace CF7_Extended;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Hooks {

	private static $instance = null;

	public function __construct() {

		// add wrapper to contact form
		add_filter( 'wpcf7_contact_form_properties', array( $this, 'eacf7_add_wrapper_to_cf7_form' ), 10, 2 );

		if ( defined( 'WPCF7_VERSION' ) && WPCF7_VERSION >= 5.7 ) {
			add_filter( 'wpcf7_autop_or_not', '__return_false' );
		}

		add_action( 'eacf7_before_render_dashboard', array( $this, 'handle_connect_page_render' ) );
	}

	/**
	 * Handle Connect Page Render
	 */
	public function handle_connect_page_render() {
		if ( ! eacf7_fs()->is_premium() && ! eacf7_fs()->is_registered() && eacf7_fs()->is_activation_page() && ! eacf7_fs()->is_anonymous_site() ) {
			eacf7_fs()->_connect_page_render();
		}
	}

	/***
	 * Add Wrapper to Contact Form
	 */
	public function eacf7_add_wrapper_to_cf7_form( $properties, $cf_form ) {
		if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {

			$form         = $properties['form'];
			$form_id      = $cf_form->id();
			$conversation = str_contains( $properties['form'], 'conversational_start' ) ? 1 : 0;

			if ( ! $conversation ) {
				ob_start();

				do_action( 'eacf7_before_render_contact_form_7' );

				echo '<div class="eacf7-form-' . $form_id . '">' . $form . '</div>';

				do_action( 'eacf7_after_render_contact_form_7' );

				$properties['form'] = ob_get_clean();
			}
		}

		return $properties;
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}

Hooks::instance();
