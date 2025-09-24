<?php

namespace EACF7;

if (! defined('ABSPATH')) {
	exit;
}

/**
 * Class Install
 */
class Install {

	/**
	 * Plugin activation stuffs
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		require_once EACF7_INCLUDES . '/Update.php';

		$updater = new Update();

        if ( $updater->needs_update() ) {
            $updater->perform_updates();
        }else{
			self::create_default_data();
			self::create_tables();
		}
	}

	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$charset_collate = $wpdb->get_charset_collate();

		$tables = [
			"CREATE TABLE IF NOT EXISTS {$wpdb->prefix}eacf7_entries (
				id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				form_id bigint(20) unsigned NOT NULL,
				form_name text(20) NOT NULL,
				form_data longtext NOT NULL,
				status int(10) NOT NULL DEFAULT 0,
				created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				updated_at datetime NOT NULL,
				PRIMARY KEY (id),
				KEY form_id (form_id)
			) $charset_collate;",
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

		foreach ($tables as $table) {
			dbDelta($table);
		}
	}

	/**
	 * Create plugin settings default data
	 *
	 * @since 1.0.0
	 */
	private static function create_default_data() {
		if (! get_option('eacf7_version')) {
			update_option('eacf7_version', EACF7_VERSION);
		}

		if (! get_option('eacf7_db_version')) {
			update_option('eacf7_db_version', EACF7_DB_VERSION);
		}

		if (! get_option('eacf7_install_time')) {
			update_option('eacf7_install_time', current_time('mysql'));
		}
	}
}
