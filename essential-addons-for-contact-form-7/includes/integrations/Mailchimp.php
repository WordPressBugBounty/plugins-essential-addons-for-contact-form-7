<?php

namespace EACF7\Integrations;

if (! defined('ABSPATH')) {
	exit;
}

class Mailchimp {
	/**
	 * @var null
	 */
	protected static $instance = null;

	public function __construct() {
		add_action('wp_ajax_eacf7_check_mailchimp_api', array($this, 'check_mailchimp_api'));
		add_action('wpcf7_before_send_mail', array($this, 'send_data'));
	}

	public function check_mailchimp_api() {
		// Check nonce
		if (! check_ajax_referer('eacf7', 'nonce', false)) {
			wp_send_json_error(__('Invalid nonce', 'essential-addons-for-contact-form-7'));
		}

		if (! current_user_can('manage_options')) {
			wp_send_json_error(array('message' => __('You do not have permission to do this', 'essential-addons-for-contact-form-7')));
		}

		$api_key = ! empty($_POST['apiKey']) ? sanitize_text_field($_POST['apiKey']) : '';

		if (empty($api_key)) {
			wp_send_json_error(esc_html__('No API key provided.', 'essential-addons-for-contact-form-7'));
		}

		if (! empty($api_key)) {

			// Extract the server prefix from the API key
			$server_prefix = explode("-", $api_key);

			if (! isset($server_prefix[1])) {
				return false;
			}

			$server_prefix = $server_prefix[1];

			// Mailchimp API root endpoint
			$url = "https://$server_prefix.api.mailchimp.com/3.0/";

			// Set up the request arguments
			$args = array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $api_key
				),
				'timeout' => 10, // Timeout after 10 seconds
			);

			// Make the request
			$response = wp_remote_get($url, $args);

			// Check if the request failed
			if (is_wp_error($response)) {
				wp_send_json_error(esc_html__('Could not resolve host. Please check API Key & try again.', 'essential-addons-for-contact-form-7'));
			}

			// Get the response code
			$response_code = wp_remote_retrieve_response_code($response);

			// A valid API key should return a 200 response code
			if ($response_code == 200) {

				$data = wp_remote_get($url . '/lists', $args);

				// Decode the JSON body into an associative array
				$data = json_decode($data['body'], true);

				// Check if JSON decoding was successful
				if (json_last_error() === JSON_ERROR_NONE) {
					// Extract the lists array
					if (isset($data['lists']) && is_array($data['lists'])) {
						$audience = [];

						// Loop through the lists array to collect information
						foreach ($data['lists'] as $list) {
							if (isset($list['id'], $list['name'])) {
								// $audience[$list['id']] = $list['name'];
								$audience[] = [
									'id'   => $list['id'],
									'name' => $list['name'],
								];
							}
						}
					}
				}

				$data = array(
					'message' => esc_html__('Everything\'s Okay!', 'essential-addons-for-contact-form-7'),
					'list'    => $audience,
				);

				wp_send_json_success($data);
			} else {
				wp_send_json_error(esc_html__('Invalid API Key.', 'essential-addons-for-contact-form-7'));
			}
		}
	}

	/**
	 * Send Data to Mailchimp
	 */
	public function send_data($contact_form) {
		$submission = \WPCF7_Submission::get_instance();

		if ($submission) {
			$form_id     = $contact_form->id();
			$posted_data = $submission->get_posted_data();

			$integration_data = get_post_meta($form_id, 'eacf7_integrations_data', true) ? get_post_meta($form_id, 'eacf7_integrations_data', true) : '';

			/**
			 * Mailchimp
			 */
			$eacf7_mailchimp_api_key    = eacf7_get_settings('mailchimpApiKey') ? sanitize_text_field(eacf7_get_settings('mailchimpApiKey')) : '';
			$eacf7_mailchimp_status     = isset($integration_data['enableMailchimp']) ? intval($integration_data['enableMailchimp']) : 0;
			$eacf7_mailchimp_audience   = isset($integration_data['subscribeAudience']) ? sanitize_text_field($integration_data['subscribeAudience']) : '';
			$eacf7_mailchimp_mail       = isset($integration_data['subscribeMail']) ? sanitize_text_field($integration_data['subscribeMail']) : '';
			$eacf7_mailchimp_first_name = isset($integration_data['subscribeFirstName']) ? sanitize_text_field($integration_data['subscribeFirstName']) : '';
			$eacf7_mailchimp_last_name  = isset($integration_data['subscribeLastName']) ? sanitize_text_field($integration_data['subscribeLastName']) : '';
			$eacf7_mailchimp_phone      = isset($integration_data['subscribePhone']) ? sanitize_text_field($integration_data['subscribePhone']) : '';

			if (! $eacf7_mailchimp_status) {
				return;
			}

			if (! empty($eacf7_mailchimp_api_key) && $eacf7_mailchimp_status) {
				$server_prefix = explode("-", $eacf7_mailchimp_api_key);
				$server_prefix = $server_prefix[1];

				$subscriber_fname = ! empty($eacf7_mailchimp_first_name) ? $posted_data[$eacf7_mailchimp_first_name] : '';

				$subscriber_lname = ! empty($eacf7_mailchimp_last_name) ? $posted_data[$eacf7_mailchimp_last_name] : '';

				$subscriber_phone = ! empty($eacf7_mailchimp_phone) ? $posted_data[$eacf7_mailchimp_phone] : '';

				$subscriber_mail = ! empty($eacf7_mailchimp_mail) ? $posted_data[$eacf7_mailchimp_mail] : '';

				$url = "https://$server_prefix.api.mailchimp.com/3.0/lists/" . $eacf7_mailchimp_audience . "/members";

				// Mailchimp data
				$data = json_encode(array(
					'email_address' => sanitize_email($subscriber_mail),
					'status'        => 'subscribed',
					'merge_fields'  => array_merge(
						array(
							'FNAME' => sanitize_text_field($subscriber_fname),
							'LNAME' => sanitize_text_field($subscriber_lname),
							'PHONE' => sanitize_text_field($subscriber_phone),
						),
					),
					'vip'           => false,
					'location'      => array(
						'latitude'  => 0,
						'longitude' => 0
					)
				));

				// Prepare headers
				$headers = array(
					'Authorization' => 'Bearer ' . $eacf7_mailchimp_api_key,
					'Content-Type'  => 'application/json',
				);

				// Perform the request using wp_remote_post
				$response = wp_remote_post($url, array(
					'method'    => 'POST',
					'body'      => $data,
					'headers'   => $headers,
					'sslverify' => false, // For development purposes only. Remove or set to true for production.
				));

				// Handle the response
				if (is_wp_error($response)) {
					$error_message = $response->get_error_message();
				} else {
					$body = wp_remote_retrieve_body($response);
				}
			}
		}
	}

	/**
	 * @return Mailchimp|null
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Mailchimp::instance();
