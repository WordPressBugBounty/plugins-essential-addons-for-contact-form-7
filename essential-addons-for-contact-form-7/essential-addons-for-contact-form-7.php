<?php

/**
 * Plugin Name: Essential Addons for Contact Form 7
 * Plugin URI: https://softlabbd.com/essential-addons-for-contact-form-7
 * Description: All-in-one enhancement suite to supercharge Contact Form 7.
 * Version:     1.0.8
 * Author:      SoftLab
 * Author URI:  https://softlabbd.com/
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: essential-addons-for-contact-form-7
 * Domain Path: /languages/
 * Requires Plugins: contact-form-7
 *
 */
// don't call the file directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( function_exists( 'eacf7_fs' ) ) {
    eacf7_fs()->set_basename( false, __FILE__ );
} else {
    if ( !function_exists( 'eacf7_fs' ) ) {
        // Create a helper function for easy SDK access.
        function eacf7_fs() {
            global $eacf7_fs;
            if ( !isset( $eacf7_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $eacf7_fs = fs_dynamic_init( array(
                    'id'             => '16650',
                    'slug'           => 'essential-addons-for-contact-form-7',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_a223a352b97599f958e06405bad79',
                    'is_premium'     => false,
                    'premium_suffix' => 'PRO',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'trial'          => array(
                        'days'               => 3,
                        'is_require_payment' => false,
                    ),
                    'menu'           => array(
                        'slug'       => 'eacf7',
                        'first-path' => 'admin.php?page=eacf7',
                        'contact'    => false,
                        'support'    => false,
                        'parent'     => array(
                            'slug' => 'wpcf7',
                        ),
                    ),
                    'is_live'        => true,
                ) );
            }
            return $eacf7_fs;
        }

        // Init Freemius.
        eacf7_fs();
        // Check if Contact Form 7 is active, then redirect to dashboard
        eacf7_fs()->add_filter( 'redirect_on_activation', function ( $redirect ) {
            return class_exists( 'WPCF7' );
        } );
        // Signal that SDK was initiated.
        do_action( 'eacf7_fs_loaded' );
    }
    /** define constants */
    define( 'EACF7_VERSION', '1.0.8' );
    define( 'EACF7_DB_VERSION', '1.0.1' );
    define( 'EACF7_FILE', __FILE__ );
    define( 'EACF7_PATH', dirname( EACF7_FILE ) );
    define( 'EACF7_INCLUDES', EACF7_PATH . '/includes' );
    define( 'EACF7_URL', plugins_url( '', EACF7_FILE ) );
    define( 'EACF7_ASSETS', EACF7_URL . '/assets' );
    /*
     * The code that runs during plugin activation
     *
     * @since 1.0.0
     */
    register_activation_hook( EACF7_FILE, function () {
        if ( !class_exists( 'EACF7\\Install' ) ) {
            require_once EACF7_INCLUDES . '/Install.php';
        }
        EACF7\Install::activate();
    } );
    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     *
     * @since 1.0.0
     */
    add_action( 'plugins_loaded', function () {
        include_once EACF7_INCLUDES . '/Main.php';
    } );
}