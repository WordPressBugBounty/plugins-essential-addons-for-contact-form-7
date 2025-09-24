<?php

namespace EACF7\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pabbly {
	/**
	 * @var null
	 */
	protected static $instance = null;

	public function __construct() {
		add_action( 'wpcf7_before_send_mail', array( $this, 'send_data' ) );
	}

	/**
	 * Send Data to Telegram
	 */
	public function send_data( $contact_form ) {
		$submission = \WPCF7_Submission::get_instance();

		if ( $submission ) {
			$form_id     = $contact_form->id();
			$form_title  = $contact_form->title();
			$posted_data = $submission->get_posted_data();

			$integration_data = get_post_meta( $form_id, 'eacf7_integrations_data', true ) ? get_post_meta( $form_id, 'eacf7_integrations_data', true ) : '';

			/**
			 * Pabbly
			 */
			$eacf7_pabbly_status = isset( $integration_data['enablePabbly'] ) ? intval( $integration_data['enablePabbly'] ) : 0;
			$eacf7_pabbly_url    = isset( $integration_data['pabblyUrl'] ) ? sanitize_text_field( $integration_data['pabblyUrl'] ) : '';

			if ( $eacf7_pabbly_status && ! empty( $eacf7_pabbly_url ) ) {

				$data = array(
					'form_id'    => $form_id,
					'form_title' => $form_title,
					'form_data'  => $posted_data,
				);

				// Convert the data to JSON
				$jsonData = json_encode( $data );

				// headers
				$headers = array( 'Content-Type' => 'application/json' );

				// Send the data to the webhook URL
				$response = wp_remote_post( $eacf7_pabbly_url, array(
					'method'  => 'POST',
					'body'    => $jsonData,
					'headers' => $headers,
				) );
			}
		}
	}

	/**
	 * @return Pabbly|null
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Pabbly::instance();
