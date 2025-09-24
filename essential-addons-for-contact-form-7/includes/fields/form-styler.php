<?php

namespace EACF7;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Form_Styler {
	/**
	 * @var null
	 */
	protected static $instance = null;

	public function __construct() {

		add_action( 'wpcf7_after_save', array( $this, 'save_data' ) );

		add_filter( 'wpcf7_contact_form_properties', array( $this, 'add_form_styler_properties' ), 10, 2 );

		// add form-styler data to localize script
		add_filter( 'eacf7_localize_data', array( $this, 'add_localize_data' ) );
	}


	public function add_form_styler_properties( $properties, $form ) {

		if ( is_admin() ) {
			return $properties;
		}

		$form_id     = $form->id();
		$styler_data = (array) $this->get_form_styler_data( $form_id );

		$style_template = $styler_data['stylePreset'] ?? 'default';

		$class = " eacf7-form-styler eacf7-form-styler-$style_template";

		$properties['form'] = sprintf( '<div class="%s" data-styler="%s" data-form_id="%s">%s</div>',
			$class,
			base64_encode( json_encode( $styler_data ) ),
			$form_id,
			$properties['form']
		);

		return $properties;
	}

	public function add_localize_data( $data ) {
		if ( eacf7_is_editor_page() ) {
			$data['formStylerData'] = $this->get_form_styler_data();
		}

		return $data;
	}

	public function get_form_styler_data( $form_id = null ) {

		if ( ! $form_id ) {
			$contact_form = \WPCF7_ContactForm::get_current();
			$form_id      = $contact_form->id();
		}

		$data = get_post_meta( $form_id, 'eacf7_form_styler_data', true );

		return ! empty( $data ) ? $data : [];

	}

	/**
	 * Save meta
	 * @since 1.0.0
	 */
	public function save_data( $contact_form ) {
		$post_id = $contact_form->id();

		if ( empty( $_POST['eacf7_form_styler_data'] ) ) {
			return;
		}

		$form_styler_data = stripslashes( $_POST['eacf7_form_styler_data'] );
		$form_styler_data = json_decode( $form_styler_data, true );

		update_post_meta( $post_id, 'eacf7_form_styler_data', $form_styler_data );
	}

	/**
	 * @return Form_Styler|null
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Form_Styler::instance();
