<?php

namespace EACF7;

if (! defined('ABSPATH')) {
	exit;
}

class Form_Template {
	/**
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action('admin_enqueue_scripts', array($this, 'admin_assets'), 1);
		add_action('wp_ajax_eacf7_get_form_template', array($this, 'get_form_template'));
		add_action('wpcf7_admin_footer', array($this, 'form_template_popup'));
	}

	/**
	 * Admin Assets
	 * @since 1.0.0
	 */
	public function admin_assets() {
	}

	public function get_form_template() {
		if (! check_ajax_referer('eacf7', 'nonce', false)) {
			wp_send_json_error(__('Invalid nonce', 'essential-addons-for-contact-form-7'));
			exit();
		}

		$form = isset($_POST['form']) ? sanitize_text_field($_POST['form']) : '';

		$data = '';

		// check & get form
		if (! empty($form) ) {
			require_once EACF7_INCLUDES . '/fields/templates/forms.php';
		}

		wp_send_json_success($data);
	}

	/**
	 * Form Template Popup
	 * @since 1.0.0
	 */
	public function form_template_popup() {
		ob_start();
?>
		<div id="eacf7-form-template" class="eacf7-form-template"></div>
<?php
		echo ob_get_clean();
	}

	/**
	 * @return Form_Template|null
	 */
	public static function instance() {
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Form_Template::instance();
