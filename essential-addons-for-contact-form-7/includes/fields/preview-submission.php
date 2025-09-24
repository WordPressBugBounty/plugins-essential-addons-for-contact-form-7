<?php

namespace EACF7;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Preview_Submission
 */
class Preview_Submission {
	/**
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wpcf7_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wpcf7_after_save', array( $this, 'save_data' ) );
		add_filter( 'eacf7_localize_data', array( $this, 'add_localize_data' ) );

		// Add integration data to the form footer
		add_filter( 'wpcf7_form_elements', array( $this, 'print_preview_submission_js_data' ) );
	}

	/**
	 * Add the preview submission data to the localize data.
	 *
	 * @param array $data The localize data.
	 *
	 * @return array The updated localize data.
	 */
	public function add_localize_data( $data ) {
		if ( eacf7_is_editor_page() ) {
			$data['previewSubmissionData'] = $this->get_preview_submission_data();
		}

		return $data;
	}

	public function print_preview_submission_js_data( $form_html ) {

		$form_id = eacf7_get_current_form_id();

		$data = $this->get_preview_submission_data( $form_id );

		if ( empty( $data ) ) {
			return $form_html;
		}

		// Encode PHP array/object to JSON for safe JS usage
		$json_data = wp_json_encode( $data );

		$js = <<<HTML
<script>
var eacf7PreviewSubmissionData_{$form_id} = {$json_data};
</script>
HTML;

		return $form_html . $js;
	}

	/**
	 * Retrieve the preview submission data.
	 *
	 * @param int|null $form_id The form ID.
	 *
	 * @return array The preview submission data.
	 */
	function get_preview_submission_data( $form_id = null ) {

		if ( ! $form_id ) {
			$form_id = eacf7_get_current_form_id();
		}

		$data = get_post_meta( $form_id, 'eacf7_preview_submission_data', true );

		return ! empty( $data ) ? $data : [];
	}

	/**
	 * Enqueue frontend scripts.
	 *
	 * Registers and enqueues the necessary frontend scripts for the plugin.
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'eacf7-swal' );
		wp_enqueue_style( 'eacf7-frontend' );

		wp_enqueue_script( 'eacf7-swal' );
		wp_enqueue_script( 'eacf7-frontend' );
	}


	/**
	 * Save the preview submission data.
	 *
	 * @param WPCF7_ContactForm $contact_form The contact form object.
	 *
	 * @return void
	 */
	public function save_data( $contact_form ) {
		$post_id = $contact_form->id();

		if ( empty( $_POST['eacf7_preview_submission_data'] ) ) {
			return;
		}

		$preview_submission_data = stripslashes( $_POST['eacf7_preview_submission_data'] );
		$preview_submission_data = json_decode( $preview_submission_data, true );

		update_post_meta( $post_id, 'eacf7_preview_submission_data', $preview_submission_data );
	}

	/**
	 * @return Preview_Submission|null
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

// Initialize the Preview_Submission class.
Preview_Submission::instance();
