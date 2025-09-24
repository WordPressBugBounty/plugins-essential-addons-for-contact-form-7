<?php

namespace EACF7;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Redirection {
	/**
	 * @var null
	 */
	protected static $instance = null;

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'redirection_assets' ) );
		add_action( 'wpcf7_after_save', array( $this, 'save_redirection_data' ) );

		//get pages
		add_action( 'wp_ajax_eacf7_get_pages', array( $this, 'get_pages' ) );
		add_action( 'wp_ajax_eacf7_get_posts', array( $this, 'get_posts' ) );

		// add redirection data to localize script
		add_filter( 'eacf7_localize_data', array( $this, 'add_localize_data' ) );

		// Add integration data to the form footer
		add_filter( 'wpcf7_form_elements', array( $this, 'print_redirection_js_data' ) );


	}

	public function add_localize_data( $data ) {
		if ( eacf7_is_editor_page() ) {
			$data['redirectionData'] = $this->get_redirection_data();
		}

		return $data;
	}

	public function print_redirection_js_data( $form_html ) {

		$form_id = eacf7_get_current_form_id();

		$data = $this->get_redirection_data( $form_id );

		if ( empty( $data ) ) {
			return $form_html;
		}

		// Encode PHP array/object to JSON for safe JS usage
		$json_data = wp_json_encode( $data );

		$js = <<<HTML
<script>
var eacf7RedirectionData_{$form_id} = {$json_data};
</script>
HTML;

		return $form_html . $js;
	}

	function get_redirection_data( $form_id = null ) {

		if ( ! $form_id ) {
			$form_id = eacf7_get_current_form_id();
		}

		$data = get_post_meta( $form_id, 'eacf7_redirection_data', true );

		return ! empty( $data ) ? $data : [];

	}


	/**
	 * Get Pages
	 *
	 * @since 1.0.0
	 */
	public function get_pages() {
		$pages = get_posts( array(
			'post_type'      => 'page',
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
		) );

		$result = array();

		foreach ( $pages as $page ) {
			$result[] = array(
				'label' => $page->post_title,
				'value' => get_permalink( $page->ID ),
			);
		}

		wp_send_json_success( $result );
	}

	/**
	 * Get Posts
	 *
	 * @since 1.0.0
	 */
	public function get_posts() {
		$pages = get_posts( array(
			'post_type'      => 'post',
			'posts_per_page' => - 1,
			'post_status'    => 'publish',
		) );

		$result = array();

		foreach ( $pages as $page ) {
			$result[] = array(
				'label' => $page->post_title,
				'value' => get_permalink( $page->ID ),
			);
		}

		wp_send_json_success( $result );
	}

	/**
	 * Enqueue Assets
	 */
	public function redirection_assets() {
		wp_enqueue_script( 'eacf7-frontend' );
	}

	/**
	 * Save Redirection Data
	 */
	public function save_redirection_data( $contact_form ) {
		$post_id = $contact_form->id();

		if ( empty( $_POST['eacf7_redirection_data'] ) ) {
			return;
		}

		$placeholder_data = stripslashes( $_POST['eacf7_redirection_data'] );
		$placeholder_data = json_decode( $placeholder_data, true );

		update_post_meta( $post_id, 'eacf7_redirection_data', $placeholder_data );
	}

	/**
	 * @return Redirection|null
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

Redirection::instance();