<?php

namespace EACF7;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Main {

	/**
	 * The single instance of the class.
	 *
	 * @var Main
	 * @since 1.0.0
	 */
	protected static $instance = null;

	/**
	 * Main constructor.
	 */
	public function __construct() {

		if ( ! $this->check_environment() ) {
			return;
		}

		$this->includes();
		$this->init_hooks();

		do_action( 'eacf7_loaded' );
	}

	private function check_environment() {
		$environment = true;

		if ( ! class_exists( 'WPCF7' ) ) {
			$environment = false;

			if ( is_admin() ) {
				//add_action( 'admin_notices', [ $this, 'missing_plugin_notice' ] );
			}
		}

		return $environment;
	}

	public function is_cf7_installed( $basename ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			include_once ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$installed_plugins = get_plugins();

		return isset( $installed_plugins[ $basename ] );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function includes() {
		include_once EACF7_INCLUDES . '/functions.php';
		include_once EACF7_INCLUDES . '/Enqueue.php';
		include_once EACF7_INCLUDES . '/Hooks.php';
		include_once EACF7_INCLUDES . '/Ajax.php';

		include_once EACF7_INCLUDES . '/Tinymce.php';
		include_once EACF7_INCLUDES . '/Elementor.php';

		include_once EACF7_INCLUDES . '/Integrations.php';
		include_once EACF7_INCLUDES . '/Security.php';

		// Fields Manager
		include_once EACF7_INCLUDES . '/FieldsManager.php';


		if ( is_admin() ) {
			include_once EACF7_INCLUDES . '/Admin.php';
		}
	}

	/**
	 * Hook into actions and filters.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {

		add_action( 'admin_notices', [ $this, 'print_notices' ], 15 );

		// Localize our plugin
		add_action( 'init', [ $this, 'localization_setup' ] );

		// Plugin action links
		add_filter( 'plugin_row_meta', [ $this, 'plugin_row_meta' ], 10, 2 );

		add_filter( 'wpcf7_messages', [ $this, 'cf7_extended_messages' ] );
	}


	public function cf7_extended_messages( $messages ) {
		return array_merge( $messages, array(
			'invalid_captcha' => array(
				'description' => __( 'This message shows whenever the reCaptcha is invalid. Added by Essential Addons for Contact Form 7', 'essential-addons-for-contact-form-7' ),
				'default'     => __( 'Could not verify the reCaptcha response.', 'essential-addons-for-contact-form-7' ),
			),
		) );
	}

	public function plugin_row_meta( $plugin_meta, $plugin_file ) {
		if ( $plugin_file == plugin_basename( EACF7_FILE ) ) {
			$plugin_meta[] = sprintf( '<a target="_blank" href="%s" style="color:#1E62B9; font-weight: 600;">%s</a>',admin_url( 'admin.php?page=eacf7' ), esc_html__( 'Settings', 'essential-addons-for-contact-form-7' ) );
			$plugin_meta[] = sprintf( '<a target="_blank" href="https://softlabbd.com/docs-category/essential-addons-for-contact-form-7/" style="color:#1E62B9; font-weight: 600;">%s</a>', esc_html__( 'Docs', 'essential-addons-for-contact-form-7' ) );
			$plugin_meta[] = sprintf( '<a target="_blank" href="https://softlabbd.com/support/" style="color:#1E62B9; font-weight: 600;">%s</a>', esc_html__( 'Support', 'essential-addons-for-contact-form-7' ) );
		}

		return $plugin_meta;
	}

	/**
	 * Initialize plugin for localization
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function localization_setup() {
		load_plugin_textdomain( 'essential-addons-for-contact-form-7', false, dirname( plugin_basename( EACF7_FILE ) ) . '/languages/' );
	}


	public function add_notice( $class, $message ) {

		$notices = get_option( sanitize_key( 'essential_addons_notices' ), [] );
		if ( is_string( $message ) && is_string( $class ) && ! wp_list_filter( $notices, array( 'message' => $message ) ) ) {

			$notices[] = array(
				'message' => $message,
				'class'   => $class,
			);

			update_option( sanitize_key( 'essential_addons_notices' ), $notices );
		}

	}

	/**
	 * Prince admin notice
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function print_notices() {
		$notices = get_option( sanitize_key( 'essential_addons_notices' ), [] );

		foreach ( $notices as $notice ) { ?>
            <div class="notice notice-large is-dismissible eacf7-admin-notice notice-<?php echo esc_attr( $notice['class'] ); ?>">
				<?php esc_html( $notice['message'] ); ?>
            </div>
			<?php
			update_option( sanitize_key( 'essential_addons_notices' ), [] );
		}
	}

	public function missing_plugin_notice() {

		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		$cf7 = 'contact-form-7/wp-contact-form-7.php';

		if ( $this->is_cf7_installed( $cf7 ) ) {
			$activation_url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $cf7 . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_' . $cf7 );

			$message = '<strong>Essential Addons for Contact Form 7 </strong> requires <strong>Contact Form 7</strong> plugin to be active. Please activate Contact Form 7 to continue.';

			$button_text = 'Activate Contact Form 7';
		} else {

			$activation_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=contact-form-7' ), 'install-plugin_contact-form-7' );

			$message     = '<strong>Essential Addons for Contact Form 7</strong> requires <strong>Contact Form 7</strong> plugin to be installed and activated. Please install Contact Form 7 to continue.';
			$button_text = 'Install Contact Form 7';
		}

		$button = '<p><a href="' . esc_url( $activation_url ) . '" class="button-primary">' . esc_html( $button_text ) . '</a></p>';

		printf( '<div class="error"><p>%1$s</p>%2$s</div>', $message, $button );
	}


	/**
	 * Main Instance.
	 *
	 * Ensures only one instance of Main is loaded or can be loaded.
	 *
	 * @return Main - Main instance.
	 * @since 1.0.0
	 * @static
	 */
	public static function instance() {

		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

//kickoff cf7e
if ( ! function_exists( 'cf7e' ) ) {
	function cf7e() {
		return Main::instance();
	}
}

cf7e();