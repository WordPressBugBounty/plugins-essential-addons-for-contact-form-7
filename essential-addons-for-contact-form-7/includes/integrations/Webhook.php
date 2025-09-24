<?php

namespace EACF7\Integrations;

if (! defined('ABSPATH')) {
	exit;
}

class Webhook {
	/**
	 * @var null
	 */
	protected static $instance = null;

	public function __construct() {
		add_action('wpcf7_before_send_mail', array($this, 'send_data'));
	}

	/**
	 * Send Data to Webhook
	 */
	public function send_data($contact_form) {
		$submission = \WPCF7_Submission::get_instance();

		if ($submission) {
			$form_id     = $contact_form->id();
			$form_title  = $contact_form->title();
			$posted_data = $submission->get_posted_data();

			$integration_data = get_post_meta($form_id, 'eacf7_integrations_data', true) ? get_post_meta($form_id, 'eacf7_integrations_data', true) : '';

			/**
			 * Webhook
			 */
			$eacf7_webhook_status        = isset($integration_data['enableWebhook']) ? intval($integration_data['enableWebhook']) : 0;
			$eacf7_webhook_url           = isset($integration_data['webhookUrl']) ? sanitize_text_field($integration_data['webhookUrl']) : '';
			$eacf7_webhook_method        = isset($integration_data['webhookMethod']) ? sanitize_text_field($integration_data['webhookMethod']) : 'POST';
			$eacf7_webhook_header        = isset($integration_data['webhookRequestHeader']) ? sanitize_text_field($integration_data['webhookRequestHeader']) : 'no-header';
			$eacf7_webhook_custom_header = ! empty($integration_data['webhookRequestHeaderFields']) ? $integration_data['webhookRequestHeaderFields'] : array();
			$eacf7_webhook_body          = isset($integration_data['webhookRequestBody']) ? sanitize_text_field($integration_data['webhookRequestBody']) : '';
			$eacf7_webhook_custom_body   = ! empty($integration_data['webhookRequestBodyFields']) ? $integration_data['webhookRequestBodyFields'] : array();

			if (! $eacf7_webhook_status) {
				return;
			}

			if ($eacf7_webhook_status && ! empty($eacf7_webhook_url) && isset($eacf7_webhook_body)) {

				// headers
				$headers = array();

				// no header
				if ($eacf7_webhook_header === 'no-header') {
					$headers['Content-Type'] = 'application/json';
				}

				// custom header
				if ($eacf7_webhook_header === 'custom-header') {
					// Loop through $data and set the values in $headers
					foreach ($eacf7_webhook_custom_header as $item) {
						$headers[$item['field']] = $item['key'];
					}
				}

				$body = array();

				$data = array(
					'form_id'    => $form_id,
					'form_title' => $form_title,
					'form_data'  => $posted_data,
				);

				if ('custom' === $eacf7_webhook_body) {
					// Loop through $data and set the values in $headers
					foreach ($eacf7_webhook_custom_body as $item) {
						$body[$item['key']] = $posted_data[$item['field']];
					}
				}

				// set custom body data
				if ('custom' === $eacf7_webhook_body && ! empty($eacf7_webhook_custom_body)) {
					$data['form_data'] = $body;
				}

				// check method & send request
				if( 'GET' === $eacf7_webhook_method ){
					$eacf7_webhook_url = add_query_arg($data, $eacf7_webhook_url);

					// send request
					wp_remote_request($eacf7_webhook_url, array(
						'method'  => $eacf7_webhook_method,
						'headers' => $headers,
					));
				}else{
					// Convert the data to JSON
					$jsonData = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

					// send request
					wp_remote_request($eacf7_webhook_url, array(
						'method'  => $eacf7_webhook_method,
						'headers' => $headers,
						'body'    => $jsonData,
					));
				}
			}
		}
	}

	/**
	 * @return Webhook|null
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Webhook::instance();
