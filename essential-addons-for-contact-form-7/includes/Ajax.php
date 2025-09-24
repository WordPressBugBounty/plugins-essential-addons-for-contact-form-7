<?php

namespace EACF7;

use EACF7\Integrations\GoogleDrive\Account;
use EACF7\Integrations\GoogleDrive\Authorization;
use EACF7\Integrations\GoogleDrive\Client;
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
class Ajax {
    private static $instance = null;

    public function __construct() {
        // Save settings
        add_action( 'wp_ajax_eacf7_save_settings', array($this, 'save_settings') );
        add_action( 'wp_ajax_eacf7_get_google_drive_auth_url', array($this, 'get_google_drive_auth_url') );
        add_action( 'wp_ajax_eacf7_get_google_drive_access_token', array($this, 'get_google_drive_access_token') );
        // save form styler
        add_action( 'wp_ajax_eacf7_save_form_styler', array($this, 'save_form_styler') );
        // Hide Recommended Plugins
        add_action( 'wp_ajax_eacf7_hide_recommended_plugins', array($this, 'hide_recommended_plugins') );
        // Export data.
        add_action( 'wp_ajax_eacf7_get_export_data', array($this, 'export_data') );
        // Import data.
        add_action( 'wp_ajax_eacf7_import_data', array($this, 'import_data') );
    }

    public function get_google_drive_auth_url() {
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'You do not have permission to do this', 'essential-addons-for-contact-form-7' ),
            ) );
        }
        // nonce check
        if ( !check_ajax_referer( 'eacf7', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'essential-addons-for-contact-form-7' ) );
        }
        if ( !class_exists( 'EACF7\\Integrations\\GoogleDrive\\Client' ) ) {
            include_once EACF7_INCLUDES . '/integrations/GoogleDrive/Account.php';
            include_once EACF7_INCLUDES . '/integrations/GoogleDrive/Client.php';
        }
        wp_send_json_success( Client::instance()->get_auth_url() );
    }

    public function get_google_drive_access_token() {
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'You do not have permission to do this', 'essential-addons-for-contact-form-7' ),
            ) );
        }
        // nonce check
        if ( !check_ajax_referer( 'eacf7', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'essential-addons-for-contact-form-7' ) );
        }
        if ( !class_exists( 'EACF7\\Integrations\\GoogleDrive\\Client' ) ) {
            include_once EACF7_INCLUDES . '/integrations/GoogleDrive/Client.php';
        }
        if ( !class_exists( 'EACF7\\Integrations\\GoogleDrive\\Authorization' ) ) {
            include_once EACF7_INCLUDES . '/integrations/GoogleDrive/Authorization.php';
        }
        if ( !class_exists( 'EACF7\\Integrations\\GoogleDrive\\Account' ) ) {
            include_once EACF7_INCLUDES . '/integrations/GoogleDrive/Account.php';
        }
        $id = ( !empty( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '' );
        $account = Account::get_accounts( $id );
        $authorization = new Authorization($account);
        $access_token_string = $authorization->get_access_token();
        $access_token_obj = json_decode( $access_token_string, true );
        $access_token = $access_token_obj['access_token'];
        wp_send_json_success( $access_token );
    }

    public function save_form_styler() {
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'You do not have permission to do this', 'essential-addons-for-contact-form-7' ),
            ) );
        }
        // nonce check
        if ( !check_ajax_referer( 'eacf7', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'essential-addons-for-contact-form-7' ) );
        }
        $id = ( !empty( $_POST['id'] ) ? intval( $_POST['id'] ) : 0 );
        $data = ( !empty( $_POST['data'] ) ? json_decode( stripslashes( $_POST['data'] ), true ) : array() );
        update_post_meta( $id, 'eacf7_form_styler_data', $data );
        wp_send_json_success();
    }

    public function hide_recommended_plugins() {
        // Verify nonce
        if ( !check_ajax_referer( 'eacf7', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'essential-addons-for-contact-form-7' ) );
        }
        // check user permission
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Invalid user' );
        }
        update_option( 'eacf7_hide_recommended_plugins', true );
        wp_send_json_success();
    }

    /**
     * Save Settings
     *
     * @since 1.0.0
     */
    public function save_settings() {
        // Check nonce
        if ( !check_ajax_referer( 'eacf7', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'essential-addons-for-contact-form-7' ) );
        }
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array(
                'message' => __( 'You do not have permission to do this', 'essential-addons-for-contact-form-7' ),
            ) );
        }
        $settings = ( !empty( $_POST['settings'] ) ? json_decode( stripslashes( $_POST['settings'] ) ) : array() );
        update_option( 'eacf7_settings', $settings );
        wp_send_json_success();
    }

    /**
     * Exports form, entry, and settings data.
     *
     * This method handles the export of form, entry, and settings data based on the requested type.
     * It verifies the request's nonce and user permissions before proceeding with the export.
     * The data includes entries, forms, and settings that can be exported individually or all together.
     *
     * @since 1.0.0
     */
    public function export_data() {
        // Check nonce.
        if ( !check_ajax_referer( 'eacf7', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'essential-addons-for-contact-form-7' ) );
        }
        // Check permission.
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You do not have permission to export data', 'essential-addons-for-contact-form-7' ) );
        }
        $type = ( !empty( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'all' );
        $export_data = array();
        switch ( $type ) {
            case 'entries':
                $export_data['entries'] = eacf7_get_entries();
                break;
            case 'forms':
                $export_data['forms'] = eacf7_get_forms_data();
                break;
            case 'settings':
                $export_data['settings'] = eacf7_get_settings();
                break;
            case 'all':
                $export_data = eacf7_get_all_export_data();
                break;
            default:
                // Handle unknown type.
                break;
        }
        wp_send_json_success( $export_data );
    }

    /**
     * Imports form, entry, and settings data.
     *
     * This method handles the import of form, entry, and settings data from a JSON payload.
     * It verifies the request's nonce and user permissions before processing the data.
     * The data includes entries that are sanitized and saved to the database, forms that are
     * inserted or updated as WordPress posts, and settings that are saved as options.
     *
     * @since 1.0.0
     */
    public function import_data() {
        // Check nonce.
        if ( !check_ajax_referer( 'eacf7', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'essential-addons-for-contact-form-7' ) );
        }
        // Check permission.
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You do not have permission to import data', 'essential-addons-for-contact-form-7' ) );
        }
        $entries = ( !empty( $_POST['data']['entries'] ) ? eacf7_sanitize_array( $_POST['data']['entries'] ) : array() );
        $forms = ( !empty( $_POST['data']['forms'] ) ? eacf7_sanitize_array( $_POST['data']['forms'] ) : array() );
        $settings = ( !empty( $_POST['data']['settings'] ) ? eacf7_sanitize_array( $_POST['data']['settings'] ) : array() );
        // Settings.
        if ( !empty( $settings ) ) {
            $fields = array(
                'math-captcha',
                'mask-input',
                'digital-signature',
                'rich-text',
                'google-drive-upload',
                'image-choice',
                'custom-html',
                'color-picker',
                'repeater',
                'shortcode',
                'hook',
                'wc-product-dropdown'
            );
            $features = array(
                'multistep',
                'booking',
                'conversational',
                'pdf-generator',
                'post-submission',
                'popup-form',
                'user-registration',
                'wc-checkout',
                'save'
            );
            $integrations = array('google-drive', 'whatsapp');
            // Unset fields.
            if ( !empty( $settings['fields'] ) ) {
                $settings['fields'] = array_values( array_diff( $settings['fields'], $fields ) );
            }
            // Unset features.
            if ( !empty( $settings['features'] ) ) {
                $settings['features'] = array_values( array_diff( $settings['features'], $features ) );
            }
            // Unset Integrations.
            if ( !empty( $settings['integrations'] ) ) {
                $settings['integrations'] = array_values( array_diff( $settings['integrations'], $integrations ) );
            }
            update_option( 'eacf7_settings', $settings );
        }
        // Entries.
        if ( !empty( $entries ) ) {
            global $wpdb;
            $table = $wpdb->prefix . 'eacf7_entries';
            $wpdb->query( "TRUNCATE TABLE {$table}" );
            foreach ( $entries as $entry ) {
                $this->update_entry( $entry );
            }
        }
        // Forms.
        if ( !empty( $forms ) ) {
            foreach ( $forms as $form ) {
                // Prepare post data.
                $post_data = array(
                    'import_id'   => intval( $form['id'] ),
                    'post_title'  => sanitize_text_field( $form['title'] ),
                    'post_status' => 'publish',
                    'post_type'   => 'wpcf7_contact_form',
                );
                // Insert or update the post.
                $post_id = wp_insert_post( $post_data );
                // Check if the post was inserted successfully.
                if ( !is_wp_error( $post_id ) && $post_id > 0 ) {
                    // Meta keys to check and add if they exist.
                    $meta_keys = array(
                        '_form',
                        '_mail',
                        '_mail_2',
                        '_messages',
                        '_additional_settings',
                        '_locale',
                        '_hash',
                        'eacf7_conditional_rules',
                        'eacf7_form_styler_data',
                        'eacf7_prepopulate_data',
                        'eacf7_range_slider_data',
                        'eacf7_redirection_data',
                        'eacf7_submission_id_data',
                        'eacf7_integrations_data'
                    );
                    // Add only non-empty meta values.
                    foreach ( $meta_keys as $key ) {
                        if ( !empty( $form[$key] ) ) {
                            update_post_meta( $post_id, $key, $form[$key] );
                        }
                    }
                }
            }
        }
        wp_send_json_success();
    }

    /**
     * Updates or inserts an entry in the database.
     *
     * This function handles updating an existing entry or inserting a new entry
     * in the database table for form entries. It checks the nonce and user
     * permissions, sanitizes input data, and performs the database operation.
     * If $data is provided, the function returns the inserted or updated data
     * array; otherwise, it sends a JSON response with the data.
     *
     * @param array|null $data An associative array of entry data to be updated or inserted.
     *                         If null, the data is retrieved from the POST request.
     * @return array|void The inserted or updated data array if $data is provided, otherwise nothing.
     */
    public function update_entry( $data = null ) {
        // Check nonce.
        if ( !check_ajax_referer( 'eacf7', 'nonce', false ) ) {
            wp_send_json_error( __( 'Invalid nonce', 'essential-addons-for-contact-form-7' ) );
        }
        // Check permission.
        if ( !current_user_can( 'manage_options' ) ) {
            wp_send_json_error( __( 'You do not have permission to update this player', 'essential-addons-for-contact-form-7' ) );
        }
        if ( !$data ) {
            $nonce = ( !empty( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '' );
            if ( !wp_verify_nonce( $nonce, 'essential-addons-for-contact-form-7' ) ) {
                wp_send_json_error( __( 'Invalid nonce', 'essential-addons-for-contact-form-7' ) );
            }
        }
        $posted = ( !$data ? json_decode( base64_decode( $_POST['data'] ), 1 ) : $data );
        $id = ( !empty( $posted['id'] ) ? intval( $posted['id'] ) : 0 );
        $form_id = ( !empty( $posted['form_id'] ) ? intval( $posted['form_id'] ) : '' );
        $form_name = ( !empty( $posted['form_name'] ) ? sanitize_text_field( $posted['form_name'] ) : '' );
        $form_data = ( !empty( $posted['form_data'] ) ? sanitize_text_field( $posted['form_data'] ) : '' );
        $status = ( !empty( $posted['status'] ) ? intval( $posted['status'] ) : 0 );
        $created_at = ( !empty( $posted['created_at'] ) ? sanitize_text_field( $posted['created_at'] ) : '' );
        $updated_at = ( !empty( $posted['updated_at'] ) ? sanitize_text_field( $posted['updated_at'] ) : '' );
        global $wpdb;
        $table = $wpdb->prefix . 'eacf7_entries';
        $insert_data = array(
            'form_id'    => $form_id,
            'form_name'  => $form_name,
            'form_data'  => $form_data,
            'status'     => $status,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
        );
        if ( $id > 0 ) {
            $insert_data['id'] = $id;
        }
        if ( $id > 0 && empty( $data ) ) {
            $wpdb->update( $table, $insert_data, array(
                'id' => $id,
            ) );
        } else {
            $wpdb->insert( $table, $insert_data );
            $id = $wpdb->insert_id;
        }
        $insert_data['id'] = $id;
        if ( !empty( $data ) ) {
            return $insert_data;
        }
        wp_send_json_success( $insert_data );
    }

    /**
     * Gets the instance of this class.
     *
     * @return self
     */
    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

}

Ajax::instance();