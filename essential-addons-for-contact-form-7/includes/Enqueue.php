<?php

namespace EACF7;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Enqueue {

	private static $instance = null;

	public function __construct() {
		// Frontend scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );

		// Admin scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	public function frontend_scripts() {

		// Styles
		wp_register_style( 'eacf7-select2', EACF7_ASSETS . '/vendor/select2/select2.min.css', array(), '4.0.13' );
		wp_register_style( 'eacf7-frontend', EACF7_ASSETS . '/css/frontend.css', array(), EACF7_VERSION );
		wp_register_style( 'eacf7-swal', EACF7_URL . '/assets/vendor/sweetalert2/sweetalert2.min.css', array( 'eacf7-frontend' ), '11.12.3' );

		// Scripts
		wp_register_script( 'eacf7-select2', EACF7_ASSETS . '/vendor/select2/select2.min.js', array( 'jquery' ), '4.0.13', true );
		wp_register_script( 'eacf7-swal', EACF7_URL . '/assets/vendor/sweetalert2/sweetalert2.min.js', array( 'jquery' ), '11.12.3', true );
		wp_register_script(
			'eacf7-frontend',
			EACF7_ASSETS . '/js/frontend.js',
			array(
				'jquery',
				'wp-util',
			),
			EACF7_VERSION,
			true
		);

		wp_enqueue_script( 'eacf7-frontend' );

		wp_localize_script( 'eacf7-frontend', 'eacf7', $this->get_localize_data() );

		do_action( 'eacf7_frontend_script_registered' );
	}

	public function admin_scripts( $hook ) {

		// Styles
		$style_deps = array( 'wp-components' );

		if ( 'toplevel_page_wpcf7' == $hook || 'contact_page_wpcf7-new' == $hook || str_contains( $hook, '_page_wpcf7-new' ) ) {
			$style_deps[] = 'wp-codemirror';
		}

		// Styles
		wp_enqueue_style( 'eacf7-swal', EACF7_URL . '/assets/vendor/sweetalert2/sweetalert2.min.css', array(), '11.12.3' );
		wp_enqueue_style( 'eacf7-admin', EACF7_URL . '/assets/css/admin.css', $style_deps, EACF7_VERSION );

		// Scripts
		wp_enqueue_script( 'eacf7-swal', EACF7_URL . '/assets/vendor/sweetalert2/sweetalert2.min.js', array( 'jquery' ), '11.12.3', true );

		// Default dependencies
		$deps = array(
			'wp-element',
			'wp-components',
			'jquery',
			'wp-util',
			'wp-i18n',
		);


		wp_enqueue_script( 'eacf7-admin', EACF7_URL . '/assets/js/admin.js', $deps, EACF7_VERSION, true );
		wp_localize_script( 'eacf7-admin', 'eacf7', $this->get_localize_data( $hook ) );

		// Editor page dependencies
		if ( eacf7_is_editor_page() ) {
			wp_enqueue_script( 'wp-theme-plugin-editor' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_media();

			wp_enqueue_script( 'eacf7-builder', EACF7_URL . '/assets/js/builder.js', array( 'eacf7-admin' ), EACF7_VERSION, true );
		}

		// Entries page dependencies
		if ( 'contact_page_eacf7-entries' == $hook || str_contains( $hook, '_page_eacf7-entries' ) ) {
			wp_enqueue_script( 'eacf7-entries', EACF7_URL . '/assets/js/entries.js', array( 'eacf7-admin' ), EACF7_VERSION, true );
		}

		// Dashboard page dependencies
		if ( 'contact_page_eacf7' == $hook || str_contains( $hook, '_page_eacf7' ) ) {
			wp_enqueue_script( 'eacf7-dashboard', EACF7_URL . '/assets/js/dashboard.js', array( 'eacf7-admin' ), EACF7_VERSION, true );
		}


		do_action( 'eacf7_admin_scripts_registered' );
	}

	public function get_localize_data( $hook = null ) {

		$data = array(
			'homeUrl'    => home_url(),
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'pluginUrl'  => EACF7_URL,
			'nonce'      => wp_create_nonce( 'eacf7' ),
			'isPro'      => eacf7_fs()->can_use_premium_code__premium_only(),
			'upgradeUrl' => eacf7_fs()->get_upgrade_url(),
		);

		if ( is_admin() ) {
			$data['adminUrl'] = admin_url();

			// Settings data
			$data['settings'] = eacf7_get_settings();

			// Entries page data
			if ( 'contact_page_eacf7' == $hook || 'contact_page_eacf7-entries' == $hook || str_contains( $hook, '_page_eacf7' ) || str_contains( $hook, '_page_eacf7-entries' ) || 'post.php' == $hook || 'post-new.php' == $hook ) {
				$data['forms'] = eacf7_get_forms();
			}
		}

		// Preview page data
		if ( get_query_var( 'eacf7-preview' ) ) {
			$data['settings'] = eacf7_get_settings();
		}

		// Editor page data.
		if ( eacf7_is_editor_page() ) {
			$data['codeEditor'] = wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
		}

		return apply_filters( 'eacf7_localize_data', $data, $hook );
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Enqueue::instance();
