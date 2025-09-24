<?php

namespace EACF7;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Admin {

	private static $instance = null;

	/**
	 * Constructor.
	 *
	 * Adds actions to hooks to register the submenu and update the plugin.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Add Essential Addons submenu to Contact Form 7 main menu.
		add_action( 'admin_menu', array( $this, 'add_submenu_page' ) );
		add_action( 'admin_init', array( $this, 'init_update' ) );
	}

	/**
	 * Adds submenus to the Contact Form 7 menu.
	 *
	 * This method registers the Essential Addons submenu under the Contact Form 7 main menu.
	 * It also conditionally adds a submenu for recommended plugins if the option to hide it
	 * is not set. The method triggers the 'eacf7_sub_menu' action after adding the submenus.
	 *
	 * @return void
	 */
	public function add_submenu_page() {
		add_submenu_page(
			'wpcf7',
			__( 'Essential Addons for Contact Form 7', 'essential-addons-for-contact-form-7' ),
			__( 'Essential Addons', 'essential-addons-for-contact-form-7' ),
			'manage_options',
			'eacf7',
			array( $this, 'render_admin_page' )
		);


		do_action( 'eacf7_sub_menu' );
	}

	/**
	 * Renders the Essential Addons for Contact Form 7 admin page.
	 *
	 * This method is the callback for the 'eacf7' submenu page.
	 * It renders the Essential Addons for Contact Form 7 admin interface by
	 * echoing a div with the id 'eacf7-dashboard'. Before and after rendering the
	 * dashboard, it triggers the 'eacf7_before_render_dashboard' and
	 * 'eacf7_after_render_dashboard' actions respectively.
	 *
	 * @since 1.0.0
	 */
	public function render_admin_page() {
		do_action( 'eacf7_before_render_dashboard' );

		echo '<div id="eacf7-dashboard" class="eacf7-dashboard"></div>';

		do_action( 'eacf7_after_render_dashboard' );
	}


	/**
	 * Run update
	 *
	 * @since 1.0.1
	 */
	public function init_update() {
		require_once EACF7_INCLUDES . '/Update.php';

		$updater = new Update();

		if ( $updater->needs_update() ) {
			$updater->perform_updates();
		}
	}

	/**
	 * Gets the instance of the class.
	 *
	 * This method is part of the Singleton pattern and ensures that only one instance
	 * of the class is ever created. If the instance does not exist, it creates the
	 * instance. If the instance does exist, it returns the existing instance.
	 *
	 * @since 1.0.0
	 *
	 * @return Admin The instance of the class.
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Admin::instance();
