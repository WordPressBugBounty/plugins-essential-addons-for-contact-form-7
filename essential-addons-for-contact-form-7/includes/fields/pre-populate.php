<?php

namespace EACF7;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Pre_Populated {
	/**
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'wpcf7_after_save', array( $this, 'eacf7_save_meta' ) );

		// add pre-populate data to localize script
		add_filter( 'eacf7_localize_data', array( $this, 'add_localize_data' ) );

		// Add pre-populate data to the form footer
		add_filter( 'wpcf7_form_elements', array( $this, 'print_prepopulate_js_data' ) );

	}

	public function add_localize_data( $data ) {
		if ( eacf7_is_editor_page() ) {
			$data['prePopulateData'] = $this->get_prepopulate_data();
			$data['forms']           = eacf7_get_forms();
		}

		return $data;
	}

	public function print_prepopulate_js_data( $form_html ) {

		$form_id = eacf7_get_current_form_id();

		$data = $this->get_prepopulate_data( $form_id );

		if ( empty( $data ) ) {
			return $form_html;
		}

		// Encode PHP array/object to JSON for safe JS usage
		$json_data = wp_json_encode( $data );

		$js = <<<HTML
<script>
var eacf7PrePopulateData_{$form_id} = {$json_data};
</script>
HTML;

		return $form_html . $js;
	}

	public function get_prepopulate_data( $form_id = null ) {

		if ( ! $form_id ) {
			$form_id = eacf7_get_current_form_id();
		}

		$data = get_post_meta( $form_id, 'eacf7_prepopulate_data', true );

		return ! empty( $data ) ? $data : [];
	}


	/**
	 * Save meta
	 */
	public function eacf7_save_meta( $contact_form ) {
		$post_id = $contact_form->id();

		if ( empty( $_POST['eacf7_prepopulate_data'] ) ) {
			return;
		}

		$multistep_data = stripslashes( $_POST['eacf7_prepopulate_data'] );
		$multistep_data = json_decode( $multistep_data, true );

		update_post_meta( $post_id, 'eacf7_prepopulate_data', $multistep_data );
	}

	/**
	 * @return Pre_Populated|null
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Pre_Populated::instance();
