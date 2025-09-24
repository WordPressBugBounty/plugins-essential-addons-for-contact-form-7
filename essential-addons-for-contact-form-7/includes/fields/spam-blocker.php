<?php

namespace EACF7;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Spam_Blocker {
	/**
	 * @var null
	 */
	protected static $instance = null;

	protected $ip_block = false;

	protected $country_block = false;

	public function __construct() {
		add_filter( 'eacf7_localize_data', array( $this, 'add_localize_data' ) );
		add_action( 'wpcf7_after_save', array( $this, 'save_data' ) );
		add_action( 'wpcf7_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'wpcf7_validate_email', array( $this, 'check_email' ), 10, 2 );
		add_filter( 'wpcf7_validate_email*', array( $this, 'check_email' ), 10, 2 );

		add_filter( 'wpcf7_validate_text', array( $this, 'check_words' ), 10, 2 );
		add_filter( 'wpcf7_validate_text*', array( $this, 'check_words' ), 10, 2 );

		add_filter( 'wpcf7_validate_textarea', array( $this, 'check_words' ), 10, 2 );
		add_filter( 'wpcf7_validate_textarea*', array( $this, 'check_words' ), 10, 2 );

		add_filter( 'wpcf7_validate', array( $this, 'check_ip_address' ), 10, 2 );
		add_filter( 'wpcf7_display_message', array( $this, 'display_message' ), 10, 1 );
	}

	/**
	 * Add the spam blocker data to the localize data.
	 *
	 * @param array $data The localize data.
	 *
	 * @return array The updated localize data.
	 */
	public function add_localize_data( $data ) {

		if (eacf7_is_editor_page()) {
			$data['spamBlockerData'] = $this->get_spam_blocker_data();
		}

		return $data;
	}

	function get_spam_blocker_data( $form_id = null ) {

		if ( ! $form_id ) {
			$contact_form = \WPCF7_ContactForm::get_current();
			$form_id      = $contact_form->id();
		}

		$data = get_post_meta( $form_id, 'eacf7_spam_blocker_data', true );

		return ! empty( $data ) ? $data : [];

	}

	/**
	 * Save meta
	 */
	public function save_data( $contact_form ) {
		$post_id = $contact_form->id();

		if ( empty( $_POST['eacf7_spam_blocker_data'] ) ) {
			return;
		}

		$spam_blocker_data = stripslashes( $_POST['eacf7_spam_blocker_data'] );
		$spam_blocker_data = json_decode( $spam_blocker_data, true );

		update_post_meta( $post_id, 'eacf7_spam_blocker_data', $spam_blocker_data );
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'eacf7-frontend' );

		wp_enqueue_script( 'eacf7-frontend' );
	}

	public function check_email( $result, $tag ) {
		$post_id           = sanitize_text_field( $_POST['_wpcf7'] );
		$spam_blocker_data = get_post_meta( $post_id, 'eacf7_spam_blocker_data', true );
		$spam_blocker_msg  = $spam_blocker_data['blockCustomMessage'] ?? __( 'We do not accept spam emails, ADs and other type of unwanted info. If this is a false block, please contact us.', 'essential-addons-for-contact-form-7' );

		$name     = $tag['name'];
		$basetype = $tag['basetype'];
		$post_id  = sanitize_text_field( $_POST['_wpcf7'] );

		if ( $basetype == 'email' ) {
			$value = $_POST[ $name ];
			if ( ! $this->check_email_and_domain( $value, $post_id ) ) {
				$result->invalidate( $tag, $spam_blocker_msg );
			}
		}

		return $result;
	}

	private function check_email_and_domain( $email, $post_id ) {
		$spam_blocker_data            = get_post_meta( $post_id, 'eacf7_spam_blocker_data', true );
		$spam_blocker_status          = $spam_blocker_data['spamBlocker'] ?? false;
		$spam_blocker_emails_str      = str_replace( " ", "", $spam_blocker_data['blockEmails'] ?? '' );
		$spam_blocker_emails          = explode( ",", trim( $spam_blocker_emails_str ) );
		$spam_blocker_domains_str     = str_replace( " ", "", $spam_blocker_data['blockDomains'] ?? '' );
		$spam_blocker_domains         = explode( ",", trim( $spam_blocker_domains_str ) );
		$spam_blocker_top_domains_str = str_replace( " ", "", $spam_blocker_data['blockTLDs'] ?? '' );
		$spam_blocker_top_domains     = explode( ",", trim( $spam_blocker_top_domains_str ) );

		if ( ! $spam_blocker_status ) {
			return true;
		}

		$email_domain     = strtolower( strstr( $email, '@' ) );
		$email_domain     = str_replace( '@', '', $email_domain );
		$email_top_domain = strtolower( strrchr( $email, '.' ) );

		if ( in_array( $email_domain, $spam_blocker_domains ) || in_array( strtolower( $email ), $spam_blocker_emails ) || in_array( $email_top_domain, $spam_blocker_top_domains ) ) {
			return false;
		} else {
			return true;
		}
	}

	public function check_words( $result, $tag ) {
		$post_id             = sanitize_text_field( $_POST['_wpcf7'] );
		$spam_blocker_data   = get_post_meta( $post_id, 'eacf7_spam_blocker_data', true );
		$spam_blocker_status = $spam_blocker_data['spamBlocker'] ?? false;
		$spam_blocker_words  = $spam_blocker_data['blockWords'] ?? '';
		$spam_blocker_msg    = $spam_blocker_data['blockCustomMessage'] ?? __( 'We do not accept spam emails, ADs and other type of unwanted info. If this is a false block, please contact us.', 'essential-addons-for-contact-form-7' );

		if ( ! $spam_blocker_status || empty( $spam_blocker_words ) ) {
			return $result;
		}

		$name     = $tag['name'];
		$basetype = $tag['basetype'];
		$post_id  = sanitize_text_field( $_POST['_wpcf7'] );

		if ( $basetype == 'text' || $basetype == 'textarea' ) {
			$value = $_POST[ $name ];
			if ( $this->check_word( $value, $spam_blocker_words ) ) {
				$result->invalidate( $tag, $spam_blocker_msg );
			}
		}

		return $result;
	}

	private function check_word( $text, $spam_blocker_words ) {
		$spam_blocker_words = explode( ",", $spam_blocker_words );
		foreach ( $spam_blocker_words as $word ) {
			if ( stripos( $text, $word ) !== false ) {
				return true;
			}
		}

		return false;
	}

	public function check_ip_address( $result, $tags ) {
		$submission = \WPCF7_Submission::get_instance();

		if ( ! $submission ) {
			return $result;
		}

		$contact_form = $submission->get_contact_form();
		$form_id      = $contact_form->id();

		$spam_blocker_data          = get_post_meta( $form_id, 'eacf7_spam_blocker_data', true );
		$spam_blocker_status        = $spam_blocker_data['spamBlocker'] ?? false;
		$spam_blocker_ips_str       = str_replace( " ", "", $spam_blocker_data['blockIPs'] ?? '' );
		$spam_blocker_ips           = explode( ",", trim( $spam_blocker_ips_str ) );
		$spam_blocker_countries_str = str_replace( ", ", ",", $spam_blocker_data['blockCountries'] ?? '' );
		$spam_blocker_countries     = explode( ",", $spam_blocker_countries_str );

		if ( ! $spam_blocker_status ) {
			return $result;
		}

		$user_ip = isset( $_SERVER['HTTP_CLIENT_IP'] )
			? $_SERVER['HTTP_CLIENT_IP']
			: ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] )
				? $_SERVER['HTTP_X_FORWARDED_FOR']
				: $_SERVER['REMOTE_ADDR'] );

		if ( ! empty( $spam_blocker_countries_str ) && ! empty( $spam_blocker_countries ) ) {
			$url = "http://ip-api.com/json/{$user_ip}?fields=status,country";

			$response = wp_remote_get( $url, [ 'timeout' => 5 ] );

			if ( is_wp_error( $response ) ) {
				error_log( 'IP API request failed: ' . $response->get_error_message() );
			} else {
				$body = wp_remote_retrieve_body( $response );
				$data = json_decode( $body, true );

				if ( $data && isset( $data['status'] ) && $data['status'] === 'success' ) {
					$country = $data['country'];
				} else {
					$country = '';
					error_log( 'Failed to retrieve valid country data.' );
				}
			}
		}

		if ( ! empty( $spam_blocker_ips_str ) && in_array( $user_ip, $spam_blocker_ips ) ) {
			$result->invalidate( '', '' );
			$this->ip_block = true;

			return $result;
		}

		if ( ! empty( $spam_blocker_countries_str ) && in_array( $country, $spam_blocker_countries ) ) {
			$result->invalidate( '', '' );
			$this->country_block = true;
		}

		return $result;
	}

	public function display_message( $message ) {
		switch ( true ) {
			case $this->ip_block:
				return esc_html__( 'Your IP address is blocked from submitting this form.', 'essential-addons-for-contact-form-7' );
			case $this->country_block:
				return esc_html__( 'Your country is blocked from submitting this form.', 'essential-addons-for-contact-form-7' );
			default:
				return $message;
		}
	}


	/**
	 * @return Spam_Blocker|null
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Spam_Blocker::instance();
