<?php

namespace EACF7;

defined( 'ABSPATH' ) || exit();

class Update_1_0_1 {

	private static $instance = null;

    /**
     * Constructor
     */
	public function __construct() {
		$this->create_tables();
	}

    /**
     * Create Table
     */
	public function create_tables() {
		global $wpdb;
		$wpdb->hide_errors();
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $charset_collate = $wpdb->get_charset_collate();

		$tables = [
			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}eacf7_draft_submissions (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				form_id bigint(20) unsigned NOT NULL,
				hash text(20) NOT NULL,
				steps_completed int(20) NOT NULL,
				saved_url longtext NOT NULL,
				user_id int(10) NOT NULL DEFAULT 0,
				response longtext NOT NULL,
				created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				updated_at datetime NOT NULL,
				PRIMARY KEY (id),
				KEY form_id (form_id)
			) $charset_collate;",
		];

		foreach ( $tables as $table ) {
			dbDelta( $table );
		}
	}

	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Update_1_0_1::instance();