<?php

namespace EACF7;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Entries {

	/**
	 * @var null
	 */
	protected static $instance = null;

	private $table;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function __construct() {
		global $wpdb;
		$this->table = $wpdb->prefix . 'eacf7_entries';

		// Add admin sub-menu
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'wpcf7_before_send_mail', array( $this, 'save_entry' ) );

		// Get forms
		add_action( 'wp_ajax_eacf7_get_forms', array( $this, 'get_forms' ) );

		// Handle entries
		add_action( 'wp_ajax_eacf7_get_entries', array( $this, 'get_entries' ) );
		add_action( 'wp_ajax_eacf7_update_entries_status', array( $this, 'update_status' ) );
		add_action( 'wp_ajax_eacf7_bulk_entries_action', array( $this, 'entries_bulk_action' ) );
	}

	public function add_menu() {
		add_submenu_page(
			'wpcf7',
			__( 'Entries', 'essential-addons-for-contact-form-7' ),
			__( 'Entries', 'essential-addons-for-contact-form-7' ),
			'manage_options',
			'eacf7-entries',
			array(
				$this,
				'render_entries',
			)
		);
	}

	public function render_entries() {
		do_action( 'eacf7_before_render_entries' );
		?>
		<div id="eacf7-entries"></div>
		<?php
		do_action( 'eacf7_after_render_entries' );
	}

	public function get_forms() {

		$posts = get_posts(
			array(
				'post_type'   => 'wpcf7_contact_form',
				'numberposts' => -1,
			)
		);

		$forms = array();

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$forms[] = array(
					'value' => $post->ID,
					'label' => $post->post_title,
				);
			}
		}

		wp_send_json_success( $forms );
	}

	/**
	 * Get Entries
	 */
	public function get_entries() {

		// Check nonce
		if ( ! check_ajax_referer( 'eacf7', 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid nonce', 'essential-addons-for-contact-form-7' ) );
		}

		// Check permission
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You do not have permission to perform this action.', 'essential-addons-for-contact-form-7' ) );
		}

		$current_page = ! empty( $_POST['currentPage'] ) ? intval( $_POST['currentPage'] ) : array();
		$per_page     = ! empty( $_POST['perPage'] ) ? intval( $_POST['perPage'] ) : array();
		$form         = ! empty( $_POST['form'] ) ? intval( $_POST['form'] ) : '';
		$start_date   = ! empty( $_POST['startDate'] ) ? sanitize_text_field( $_POST['startDate'] ) : '';
		$end_date     = ! empty( $_POST['endDate'] ) ? sanitize_text_field( $_POST['endDate'] ) : '';
		$end_date 	  .= ' 23:59:59';
		$order_by     = 'id';
		$order        = 'ASC';
		$offset       = ( $current_page - 1 ) * $per_page;

		global $wpdb;

		if ( ! empty( $form ) ) {
			$sql = $wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}eacf7_entries WHERE form_id = %d AND created_at BETWEEN %s AND %s
				ORDER BY %s %s
				LIMIT %d, %d",
				$form,
				$start_date,
				$end_date,
				$order_by,
				$order,
				$offset,
				$per_page
			);
		} else {
			$sql = $wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}eacf7_entries WHERE created_at BETWEEN %s AND %s
				ORDER BY %s %s
				LIMIT %d, %d",
				$start_date,
				$end_date,
				$order_by,
				$order,
				$offset,
				$per_page
			);
		}

		$items = $wpdb->get_results( $sql );

		wp_send_json_success(
			array(
				'entries' => $items,
				'charts'  => $this->get_chart_data( $form, $start_date, $end_date ),
				'total'   => $this->get_entry_count( $form, $start_date, $end_date ),
			)
		);
	}

	/**
	 * Get chart data for given form and date range.
	 *
	 * @param int    $form     The form ID.
	 * @param string $start_date The start date in format 'd-m-Y'.
	 * @param string $end_date   The end date in format 'd-m-Y'.
	 *
	 * @return array The chart data with date and value.
	 */
	public function get_chart_data( $form, $start_date, $end_date ) {
		global $wpdb;

		$table_name    = $wpdb->prefix . 'eacf7_entries';
		$interval_days = 60;

		$start_date .= ' 00:00:00';
		$end_date   .= ' 23:59:59';

		// Prepare the query.
		if ( ! empty( $form ) ) {
			$query = $wpdb->prepare(
				"SELECT 
					DATE_FORMAT(created_at, '%%d-%%m-%%Y') AS date,
					COUNT(*) AS value
				FROM {$table_name}
				WHERE form_id = %d
				AND created_at BETWEEN %s AND %s
				GROUP BY DATE(created_at)
				ORDER BY DATE(created_at) ASC",
				$form,
				$start_date,
				$end_date
			);
		} else {
			$query = $wpdb->prepare(
				"SELECT 
					DATE_FORMAT(created_at, '%%d-%%m-%%Y') AS date,
					COUNT(*) AS value
				FROM {$table_name}
				WHERE created_at BETWEEN %s AND %s
				GROUP BY DATE(created_at)
				ORDER BY DATE(created_at) ASC",
				$start_date,
				$end_date
			);
		}

		// phpcs:ignore
		$results = $wpdb->get_results( $query, ARRAY_A );

		$chart_data = array();
		foreach ( $results as $row ) {
			$chart_data[] = array(
				'date'  => $row['date'],
				'value' => $row['value'] ?? 0,
			);
		}

		// Add dates with no data.
		$current_date = $start_date;
		while ( $current_date <= $end_date ) {
			if ( ! in_array( $current_date, array_column( $chart_data, 'date' ) ) ) {
				$chart_data[] = array(
					'date'  => date( 'd-m-Y', strtotime( $current_date ) ), // phpcs:ignore
					'value' => 0,
				);
			}
			$current_date = date( 'd-m-Y', strtotime( $current_date . ' +1 day' ) );
		}

		// Sort the chart data by date.
		usort(
			$chart_data,
			function ( $a, $b ) {
				return strtotime( $a['date'] ) - strtotime( $b['date'] );
			}
		);

		// Return complete data with missing dates filled.
		return $chart_data;
	}

	/**
	 * Retrieves the count of entries within a specified date range.
	 *
	 * This function queries the database to count the number of entries
	 * for a given form ID and date range. If no form ID is provided,
	 * it returns the count of all entries within the date range.
	 *
	 * @param int|null $form The form ID to filter entries. If null, counts all forms.
	 * @param string   $start_date The start date for the range in 'Y-m-d' format.
	 * @param string   $end_date The end date for the range in 'Y-m-d' format.
	 * @return int The number of entries within the specified range.
	 */
	public function get_entry_count( $form, $start_date, $end_date ) {
		global $wpdb;

		if ( ! empty( $form ) ) {
			return (int) $wpdb->get_var(
				$wpdb->prepare( "SELECT count(id) FROM {$wpdb->prefix}eacf7_entries WHERE form_id = %d AND created_at BETWEEN %s AND %s", $form, $start_date, $end_date )
			);
		}

		return (int) $wpdb->get_var( $wpdb->prepare( "SELECT count(id) FROM {$wpdb->prefix}eacf7_entries WHERE created_at BETWEEN %s AND %s", $start_date, $end_date ) ); // phpcs:ignore", ARRAY_A );
	}

	/**
	 * Update entries
	 *
	 * @return void
	 */
	public function update_status() {

		// Check nonce
		if ( ! check_ajax_referer( 'eacf7', 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid nonce', 'essential-addons-for-contact-form-7' ) );
		}

		// Check permission
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You do not have permission to perform this action.', 'essential-addons-for-contact-form-7' ) );
		}

		$id     = ! empty( $_POST['id'] ) ? intval( $_POST['id'] ) : '';
		$status = isset( $_POST['status'] ) ? ( 0 == intval( $_POST['status'] ) ? 1 : 0 ) : 0;

		global $wpdb;

		$sql = $wpdb->prepare(
			"UPDATE {$wpdb->prefix}eacf7_entries SET status = %d WHERE id = %d",
			$status,
			$id
		);

		$wpdb->query( $sql );

		wp_send_json_success();
	}

	/**
	 * Delete Entries
	 */
	public function entries_bulk_action() {

		// Check nonce
		if ( ! check_ajax_referer( 'eacf7', 'nonce', false ) ) {
			wp_send_json_error( __( 'Invalid nonce', 'essential-addons-for-contact-form-7' ) );
		}

		// Check permission
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You do not have permission to perform this action.', 'essential-addons-for-contact-form-7' ) );
		}

		$ids    = ! empty( $_POST['ids'] ) ? eacf7_sanitize_array( $_POST['ids'] ) : array();
		$action = ! empty( $_POST['bulk_action'] ) ? sanitize_text_field( $_POST['bulk_action'] ) : '';
		$status = ( 'read' === $action ) ? 1 : 0;

		if ( empty( $ids ) ) {
			wp_send_json_error( __( 'No IDs provided', 'essential-addons-for-contact-form-7' ) );
		}

		global $wpdb;

		// Delete entries
		if ( 'delete' === $action ) {
			$placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
			$sql          = $wpdb->prepare(
				"DELETE FROM {$wpdb->prefix}eacf7_entries WHERE id IN ($placeholders)",
				$ids
			);
			$wpdb->query( $sql );
			wp_send_json_success();
		}

		// Update status
		$placeholders = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
		$sql          = $wpdb->prepare(
			"UPDATE {$wpdb->prefix}eacf7_entries SET status = %d WHERE id IN ($placeholders)",
			array_merge( array( $status ), $ids )
		);

		$wpdb->query( $sql );

		wp_send_json_success();
	}


	/**
	 * Save entry
	 *
	 * @param \WPCF7_ContactForm $cf7
	 */
	public function save_entry( $cf7 ) {
		global $wpdb;

		$submission = \WPCF7_Submission::get_instance();

		if ( ! $submission ) {
			return;
		}

		$posted_data = $submission->get_posted_data();
		$data        = array(
			'form_id'    => $cf7->id(),
			'form_name'  => get_the_title( $cf7->id() ),
			'form_data'  => $posted_data ? json_encode( $posted_data ) : '',
			'created_at' => current_time( 'mysql' ),
			'updated_at' => current_time( 'mysql' ),
		);

		$wpdb->insert( $this->table, $data );
	}

	/**
	 * @return null|Entries
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Entries::instance();
