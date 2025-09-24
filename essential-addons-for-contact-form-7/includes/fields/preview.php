<?php

namespace EACF7;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Preview {

	private static $instance = null;

	public function __construct() {
		// Add query vars for eacf7-preview
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );

		// Render preview
		add_action( 'template_redirect', array( $this, 'render_preview' ) );
	}

	/**
	 * Add query vars for eacf7-preview
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'eacf7-preview';

		return $vars;

	}

	/**
	 * Render preview
	 */
	public function render_preview() {

		if ( get_query_var( 'eacf7-preview' ) ) {

			// Get the form id
			$form_id = get_query_var( 'eacf7-preview' );

			// Get the form
			$form = \WPCF7_ContactForm::get_instance( $form_id );

			// Check if form exists
			if ( ! $form ) {
				return;
			}

			add_filter( 'show_admin_bar', '__return_false' );

			// Remove all WordPress actions
			remove_all_actions( 'wp_head' );
			remove_all_actions( 'wp_print_styles' );
			remove_all_actions( 'wp_print_head_scripts' );
			remove_all_actions( 'wp_footer' );

			// Handle `wp_head`
			add_action( 'wp_head', 'wp_enqueue_scripts' );
			add_action( 'wp_head', 'wp_print_styles' );
			add_action( 'wp_head', 'wp_print_head_scripts' );
			add_action( 'wp_head', 'wp_site_icon' );
			remove_action( 'wp_head', 'wp_auth_check_load' );

			// Handle `wp_footer`
			add_action( 'wp_footer', 'wp_print_footer_scripts' );

			// Also remove all scripts hooked into after_wp_tiny_mce.
			remove_all_actions( 'after_wp_tiny_mce' );


			$styler_data    = get_post_meta( $form_id, 'eacf7_form_styler_data', true );

			?>
            <!doctype html>
            <html lang="<?php language_attributes(); ?>">
            <head>
                <meta charset="<?php bloginfo( 'charset' ); ?>">
                <meta name="viewport"
                      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
                <meta http-equiv="X-UA-Compatible" content="ie=edge">
                <title><?php echo $form->title(); ?></title>

				<?php

				wp_enqueue_style( 'google-font-roboto', 'https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap' );

				wp_enqueue_style( 'eacf7-swal', EACF7_URL . '/assets/vendor/sweetalert2/sweetalert2.min.css', array(), EACF7_VERSION );

				// Enqueue the preview styles
				wp_enqueue_style( 'eacf7-preview', EACF7_ASSETS . '/css/preview.css', array(
					'wp-codemirror',
					'wp-components',
					'eacf7-frontend',
				), EACF7_VERSION );


				wp_enqueue_editor();
				wp_enqueue_media();

				wp_enqueue_script( 'eacf7-swal', EACF7_URL . '/assets/vendor/sweetalert2/sweetalert2.min.js', array( 'jquery' ), EACF7_VERSION, true );

                // Enqueue the preview script
				wp_enqueue_script( 'eacf7-preview', EACF7_ASSETS . '/js/preview.js', array(
					'jquery',
					'wp-element',
					'wp-components',
					'eacf7-frontend',
				), EACF7_VERSION, true );

				do_action( 'wp_head' );

				?>

            </head>

            <body class="eacf7-preview-wrap">

            <div class="eacf7-preview">

                <div class="eacf7-preview-header">
					<div class="header-logo">
						<img src="<?php echo EACF7_ASSETS . '/images/eacf7-logo.svg'; ?>" alt="Essential addons for contact form 7" />
					</div>

                    <div class="header-title">
                        <span class="header-title-form-preview"><?php esc_html_e( 'Form Preview : ', 'essential-addons-for-contact-form-7' ); ?></span>
                        <span class="header-title-form-name"><?php echo $form->title(); ?></span>
                        <span class="header-title-form-id">(#<?php echo $form->id(); ?>)</span>
                    </div>

                    <div class="header-actions">
                        <img src="<?php echo EACF7_ASSETS . '/images/icons/duplicate.svg'; ?>" />
                        <input type="text" class="copy-shortcode" value='[contact-form-7 id="<?php echo $form->id(); ?>"]'>
                    </div>

                </div>


                <div class="eacf7-preview-form-wrap desktop">
                    <div class="eacf7-preview-form-header">
                        <div class="header-dots">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>

                        <div class="eacf7-preview-devices">
                            <i class="dashicons dashicons-desktop active"
                               data-device="desktop"
                               title="<?php esc_html_e( 'Desktop', 'essential-addons-for-contact-form-7' ); ?>"></i>
                            <i class="dashicons dashicons-tablet"
                               data-device="tablet"
                               title="<?php esc_html_e( 'Tablet', 'essential-addons-for-contact-form-7' ); ?>"></i>
                            <i class="dashicons dashicons-smartphone"
                               data-device="mobile"
                               title="<?php esc_html_e( 'Mobile', 'essential-addons-for-contact-form-7' ); ?>"
                            ></i>
                        </div>

                    </div>
                    <div class="eacf7-preview-form">
						<?php echo trim( $form->form_html() ); ?>
                    </div>
                </div>

                <div id="eacf7-preview-styler-wrap" class="eacf7-preview-styler-wrap"></div>

            </div>

			<?php do_action( 'wp_footer' ); ?>
            </body>
            </html>
			<?php
			exit();
		}
	}

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

}

Preview::instance();