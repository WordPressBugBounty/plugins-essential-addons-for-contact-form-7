<?php

namespace EACF7\Integrations;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Telegram {
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
			$mail        = $contact_form->prop( 'mail' );
			$message     = wpcf7_mail_replace_tags( @$mail['body'] );

			$integration_data = get_post_meta( $form_id, 'eacf7_integrations_data', true ) ? get_post_meta( $form_id, 'eacf7_integrations_data', true ) : '';

			/**
			 * Telegram
			 */
			$eacf7_telegram_status        = isset( $integration_data['enableTelegram'] ) ? intval( $integration_data['enableTelegram'] ) : 0;
			$eacf7_telegram_global_config = isset( $integration_data['enableTelegramGlobalConfig'] ) ? intval( $integration_data['enableTelegramGlobalConfig'] ) : 0;

			if ( $eacf7_telegram_global_config ) {
				$eacf7_telegram_bot_token = eacf7_get_settings( 'telegramBotToken' );
				$eacf7_telegram_chat_id   = eacf7_get_settings( 'telegramChatId' );
			} else {
				$eacf7_telegram_bot_token = isset( $integration_data['telegramBotToken'] ) ? sanitize_text_field( $integration_data['telegramBotToken'] ) : '';
				$eacf7_telegram_chat_id   = isset( $integration_data['telegramChatId'] ) ? intval( $integration_data['telegramChatId'] ) : '';
			}

			if ( $eacf7_telegram_status && ! empty( $eacf7_telegram_bot_token ) && ! empty( $eacf7_telegram_chat_id ) ) {

				$api_url = "https://api.telegram.org/bot$eacf7_telegram_bot_token/sendMessage";

				$args = array(
					'chat_id' => $eacf7_telegram_chat_id,
					'text'    => $message,
				);

				$response = wp_remote_post( $api_url, array(
					'body'    => json_encode( $args ),
					'headers' => array( 'Content-Type' => 'application/json' ),
				) );

				if ( is_wp_error( $response ) ) {
					error_log( 'Telegram API request failed: ' . $response->get_error_message() );
				}
			}
		}
	}

	/**
	 * @return Telegram|null
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Telegram::instance();
