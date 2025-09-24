<?php

namespace EACF7;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FieldsManager {

	private static $instance = null;

	private $active_fields;
	private $active_features;

	public function __construct() {

		$this->active_fields = eacf7_get_settings( 'fields', [
			'address',
			'country-list',
			'file-upload',
			'image-upload',
			'honeypot',
			'phone',
			'range-slider',
			'star-rating',
			'section-break',
			'dynamic-text',
			'date-time',
		] );

		$this->active_features = eacf7_get_settings( 'features', [
			'form-styler',
			'preview',
			'entries',
			'redirection',
			'conditional',
			'submission-id',
			'form-template',
			'form-generator',
		] );

		$this->include_fields();
	}

	public function include_fields() {
		$fields = $this->get_fields_map();


		foreach ( $fields as $item ) {

			if ( ! empty( $item['isPro'] ) && ! eacf7_fs()->can_use_premium_code__premium_only() ) {
				continue;
			}

			if ( $this->is_active( $item['key'] ) ) {
				$file = EACF7_INCLUDES . '/fields/' . $item['key'] . '.php';

				if ( file_exists( $file ) ) {
					include_once $file;
				}
			}

		}
	}

	public function is_active( $field ) {
		$active_items = array_merge( $this->active_fields, $this->active_features );

		return in_array( $field, $active_items );
	}

	public function get_fields_map() {
		$fields = [
			[
				'key'   => 'form-generator',
				'title' => 'Form Generator',
			],
			[
				'key'   => 'address',
				'title' => 'Address',
			],
			[
				'key'   => 'math-captcha',
				'title' => 'Math Captcha',
				'isPro' => true,
			],
			[
				'key'   => 'mask-input',
				'title' => 'Mask Input',
				'isPro' => true,
			],
			[
				'key'   => 'digital-signature',
				'title' => 'Digital Signature',
				'isPro' => true,
			],
			[
				'key'   => 'dynamic-text',
				'title' => 'Dynamic Text',
			],
			[
				'key'   => 'leads-info',
				'title' => 'Leads Info',
				'isPro' => true,
			],
			[
				'key'   => 'submission-id',
				'title' => 'Submission ID',
			],
			[
				'key'   => 'country-list',
				'title' => 'Country List',
			],
			[
				'key'   => 'custom-html',
				'title' => 'Custom HTML',
				'isPro' => true,
			],
			[
				'key'   => 'phone',
				'title' => 'Phone',
			],
			[
				'key'   => 'date-time',
				'title' => 'Date Time',
			],
			[
				'key'   => 'range-slider',
				'title' => 'Range Slider',
			],
			[
				'key'   => 'star-rating',
				'title' => 'Star Rating',
			],
			[
				'key'   => 'rich-text',
				'title' => 'Rich Text Editor',
				'isPro' => true,
			],
			[
				'key'   => 'image-upload',
				'title' => 'Image Upload',
			],
			[
				'key'   => 'file-upload',
				'title' => 'File Upload',
			],
			[
				'key'   => 'google-drive-upload',
				'title' => 'Google Drive Upload',
				'isPro' => true,
			],

			// [
			// 	'key'   => 'google-maps',
			// 	'title' => 'Google Ma),
			// 	'isPro' => true,
			// ],
			// [
			// 	'key'   => 'openstreet-maps',
			// 	'title' => 'OpenStreet Ma),
			// 	'isPro' => true,
			// ],
			[
				'key'   => 'spam-blocker',
				'title' => 'Spam Blocker',
				'isPro' => true,
			],
			[
				'key'   => 'image-choice',
				'title' => 'Image Choice',
				'isPro' => true,
			],
			[
				'key'   => 'honeypot',
				'title' => 'Honeypot',
			],
			[
				'key'   => 'hidden-field',
				'title' => 'Hidden Field',
				'isPro' => true,
			],
			[
				'key'   => 'html',
				'title' => 'Custom HTML',
				'isPro' => true,
			],
			[
				'key'   => 'color-picker',
				'title' => 'Color Picker',
				'isPro' => true,
			],
			[
				'key'   => 'repeater',
				'title' => 'Repeater Field',
				'isPro' => true,
			],
			[
				'key'   => 'shortcode',
				'title' => 'Shortcode',
				'isPro' => true,
			],
			[
				'key'   => 'hook',
				'title' => 'Action Hook',
				'isPro' => true,
			],
			[
				'key'   => 'section-break',
				'title' => 'Section Break',
			],
			[
				'key'   => 'wc-product-dropdown',
				'title' => 'WooCommerce Product Dropdown',
				'isPro' => true,
			],
			[
				'key'   => 'save',
				'title' => 'Save Form Progress',
				'isPro' => true,
			],
			[
				'key'   => 'entries',
				'title' => 'Database Entries',
			],
			[
				'key'   => 'multistep',
				'title' => 'Multi Step Form',
			],
			[
				'key'   => 'booking',
				'title' => 'Booking/Appointment Form',
				'isPro' => true,
			],
			[
				'key'   => 'conditional',
				'title' => 'Conditional Logic',
				'isPro' => false,
			],
			[
				'key'   => 'repeater',
				'title' => 'Repeater Fields',
				'isPro' => true,
			],
			[
				'key'   => 'column',
				'title' => 'Column Layout',
				'isPro' => false,
			],
			[
				'key'   => 'conversational',
				'title' => 'Conversational Forms',
				'isPro' => true,
			],
			[
				'key'   => 'pdf-generator',
				'title' => 'PDF Generator',
				'isPro' => true,
			],
			[
				'key'   => 'preview',
				'title' => 'Form Preview',
			],
			[
				'key'   => 'form-template',
				'title' => 'Form Template',
			],
			[
				'key'   => 'form-styler',
				'title' => 'Form Styler',
			],

			[
				'key'   => 'pre-populate',
				'title' => 'Pre Populate Field',
			],
			[
				'key'   => 'post-submission',
				'title' => 'Post/Blog Submission',
				'isPro' => true,
			],
			[
				'key'   => 'popup-form',
				'title' => 'Popup Form',
				'isPro' => true,
			],
			[
				'key'   => 'user-registration',
				'title' => 'User Registration',
				'isPro' => true,
			],
			[
				'key'   => 'redirection',
				'title' => 'Redirection',
			],
			[
				'key'   => 'submission-id',
				'title' => 'Submission ID',
			],
			[
				'key'   => 'wc-checkout',
				'title' => 'WooCommerce Checkout',
				'isPro' => true,
			],
			[
				'key'   => 'preview-submission',
				'title' => 'Preview Submission',
				'isPro' => true,
			],
			[
				'key'   => 'email-summaries',
				'title' => 'Email Summaries',
				'isPro' => true,
			]
		];

		return apply_filters( 'eacf7_fields_map', $fields );
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}

FieldsManager::instance();