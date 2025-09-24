<?php

namespace EACF7;

class Security {

	private static $instance = null;

	public function __construct() {

		// load security files
		if ( $this->is_active( 'recaptcha' ) ) {
			include_once EACF7_INCLUDES . '/security/Recaptcha.php';
		}

		if ( $this->is_active( 'hcaptcha' ) ) {
			include_once EACF7_INCLUDES . '/security/Hcaptcha.php';
		}

		if ( $this->is_active( 'cloudflare_turnstile' ) ) {
			include_once EACF7_INCLUDES . '/security/Cloudflare_Turnstile.php';
		}
	}

	public function is_active( $key ) {
		$security = eacf7_get_settings( 'security', [] );

		return in_array( $key, $security );
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
}

Security::instance();